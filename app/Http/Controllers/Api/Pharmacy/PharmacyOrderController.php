<?php
namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Traits\PushNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PharmacyOrderController extends Controller
{
    use PushNotification;

    /**
     * Get all orders for the authenticated pharmacy with optional status filter.
     */
    public function allOrders($type, $status)
    {
        $pharmacyId = auth()->user()->pharmacy->id;

        $query = Order::where(['pharmacy_id' => $pharmacyId, 'order_type' => $type]);

        if ($status === 'delivered') {
            $query->where('status', 'delivered');
        } elseif ($status === 'canceled') {
            $query->where('status', 'canceled');
        } elseif ($status !== 'all') {
            $query->whereNotIn('status', ['delivered', 'canceled']);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully.',
            'orders' => OrderResource::collection($orders),
        ], 200);
    }

    /**
     * Get all new orders for the authenticated pharmacy with optional status filter.
     */
    public function newOrders($type)
    {
        $pharmacyId = auth()->user()->pharmacy->id;

        $query = Order::where(['pharmacy_id' => $pharmacyId, 'order_type' => $type]);

        $query->whereIn('status', ['order_placed', 'store_accepts', 'store_rejects']);

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'New orders fetched successfully.',
            'orders' => OrderResource::collection($orders),
        ], 200);
    }
    /**
     * Get the details of a specific order.
     */
    public function orderDetails(Order $order)
    {
        return response()->json([
            'success' => true,
            'message' => 'Order details fetched successfully.',
            'order' => new OrderResource($order->load([
                'customer',
                'orderDetails.medicine',
                'orderDetails.unit'
            ]))
        ], 200);
    }

    public function addDetailsToManualOrder(Request $request, Order $order)
    {
        try {
            $validator = Validator::make($request->all(), [
                'medicine_id' => 'required|exists:medicines,id',
                'unit_id' => 'required|exists:units,id',
                'qty' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'discounted_price' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())
                        ->map(fn($error) => $error)->toArray(),
                ], 422);
            }
            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'medicine_id' => $request->medicine_id,
                'unit_id' => $request->unit_id,
                'qty' => $request->qty,
                'price' => $request->price,
                'discounted_price' => $request->discounted_price,
                'status' => 'provided',
            ]);
            $medicineCost = $orderDetail->discounted_price * $orderDetail->qty;

            $order->update([
                'total' => $order->total + $medicineCost,
                'sub_total' => $order->sub_total + $medicineCost,
                'tax' => (($order->sub_total + $medicineCost) / 100) * setting()->tax_percantage
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Order details added successfully.',
                'order' => new OrderResource($order->load('orderDetails')),
            ], 200);
        } catch (Exception $e) {

            // Log error for debugging
            Log::error('Failed to add order details' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while adding order details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMedicinesByPharmacy()
    {
        $pharmacy = auth()->user()->pharmacy;

        $medicines = $pharmacy->medicines()->with('units')->get();

        return response()->json([
            'success' => true,
            'message' => 'Medicines retrieved successfully',
            'medicines' => MedicineResource::collection($medicines),
        ]);
    }


    /**
     * Update the status of an order with validation for valid transitions.
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Start database transaction
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:store_accepts,store_rejects,ready_for_rider',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())
                        ->map(fn($error) => $error)->toArray(),
                ], 422);
            }

            // Define valid transitions between statuses
            $validTransitions = [
                'store_accepts' => 'order_placed',
                'store_rejects' => 'order_placed',
                'ready_for_rider' => ['store_accepts', 'store_rejects'],
            ];

            // Check for invalid status transition
            if (
                isset($validTransitions[$request->status]) &&
                (is_array($validTransitions[$request->status])
                    ? !in_array(
                        $order->status,
                        $validTransitions[$request->status]
                    )
                    : $order->status !== $validTransitions[$request->status])
            ) {
                return response()->json([
                    'message' => 'Invalid status transition',
                    'errors' => [
                        'status' => [
                            "Cannot transition from {$order->status}
                         to {$request->status}."
                        ]
                    ]
                ], 422);
            }

            // Update order status
            $order->update(['status' => $request->status]);

            $this->logOrderStatus($order);

            // Send notification about the status change
            $this->sendOrderStatusNotification(
                $order->customer_id,
                $order->status,
                $order->id
            );

            // Update the status of order details based on the order status
            if ($request->status === 'store_accepts') {
                $order->orderDetails()->update(['status' => 'provided']);
            } elseif ($request->status === 'store_rejects') {
                $order->orderDetails()->update(['status' => 'not-provided']);
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'order' => new OrderResource($order->load('orderDetails')),
            ], 200);
        } catch (Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            // Log error for debugging
            Log::error('Failed to update order status', [
                'order_id' => $order->id,
                'status' => $request->status,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the order status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Accept or reject the status of a specific medicine in an order.
     */
    public function acceptRejectMedicineOfOrder(
        Request $request,
        OrderDetail $orderDetail
    ) {
        // Start database transaction
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:provided,not-provided',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())
                        ->map(fn($error) => $error)->toArray(),
                ], 422);
            }

            if (!$orderDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order detail not found for this order.',
                ], 404);
            }

            // Update the status of the specific order detail
            $orderDetail->update(['status' => $request->status]);

            // Check if all order details are updated and handle the order status
            $order = $orderDetail->order;

            // Send notification about the status change
            if ($request->status == 'not-provided') {
                $this->sendOrderDetailRejectionNotification(
                    $order->customer_id,
                    $order->id,
                    $order->tracking_id,
                    $orderDetail->medicine->name_en
                );
            }
            $statuses = $order->orderDetails->pluck('status');

            if (!$statuses->contains('pending')) {
                $medicineCost = $orderDetail->discounted_price * $orderDetail->qty;
                if ($statuses->every(fn($status) => $status === 'not-provided')) {
                    $order->update([
                        'total' => $order->total - $medicineCost,
                        'sub_total' => $order->sub_total - $medicineCost,
                        'status' => 'store_rejects'
                    ]);
                } elseif ($statuses->every(fn($status) => $status === 'provided')) {
                    $order->update(['status' => 'store_accepts']);
                }

                $this->logOrderStatus($order);
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medicine status updated successfully.',
                'order_detail' => [
                    'id' => $orderDetail->id,
                    'status' => $orderDetail->status
                ],
            ], 200);
        } catch (Exception $e) {

            // Rollback transaction in case of error
            DB::rollBack();

            // Log error for debugging
            Log::error('Failed to update medicine status', [
                'order_detail_id' => $orderDetail->id,
                'status' => $request->status,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the medicine status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

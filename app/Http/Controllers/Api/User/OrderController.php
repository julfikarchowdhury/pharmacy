<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\ManualOrder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusLog;
use App\Traits\ImageHelper;
use App\Traits\PushNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use ImageHelper, PushNotification;
    public function placeOrder(PlaceOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            // Save the order
            $order = Order::create([
                'tracking_id' => rand(),
                'customer_id' => $request->customer_id,
                'total' => $request->total,
                'sub_total' => $request->sub_total,
                'delivery_address' => $request->delivery_address,
                'delivery_lat' => $request->delivery_lat,
                'delivery_long' => $request->delivery_long,
                'status' => 'order_placed',
                'date' => $request->date,
                'pharmacy_id' => $request->pharmacy_id,
                'discount_by_points' => $request->discount_by_points,
                'pharmacy_discount' => $request->pharmacy_discount,
                'delivery_charge' => $request->delivery_charge,
                'tax' => $request->tax,
                'payment_type' => $request->payment_type,
                'payment_status' => 'due'
            ]);

            // Save the order details
            foreach ($request->order_details as $detail) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'medicine_id' => $detail['medicine_id'],
                    'unit_id' => $detail['unit_id'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                    'discounted_price' => $detail['discounted_price'],
                    'status' => 'pending',
                ]);
            }
            $this->sendOrderNotificationToPharmacy(
                $request->pharmacy_id,
                $order->tracking_id,
                $order->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order saved successfully!',
                'order' => $order
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Order submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save order.'
            ], 500);
        }
    }

    public function placeManualOrder(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'note' => 'nullable|string',
            'prescription' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $prescriptionPath = null;
            if ($request->hasFile('prescription')) {
                $prescriptionPath = $this->saveNewImage(
                    $request->file('prescription'),
                    'prescriptions'
                );

            }
            $order = ManualOrder::create([
                'user_id' => $request->customer_id,
                'pharmacy_id' => $request->pharmacy_id,
                'note' => $request->note,
                'prescription' => $prescriptionPath,
                'status' => 'order_placed',

            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manual order placed successfully!',
                'order_id' => $order->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Manual order failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to place manual order.'
            ], 500);
        }
    }
    public function trackOrder($orderId)
    {
        // Check if the order exists
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        // Fetch order status logs
        $statusLogs = OrderStatusLog::where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get(['status', 'changed_at']);

        return response()->json([
            'success' => true,
            'order_id' => $orderId,
            'current_status' => $order->status,
            'tracking' => $statusLogs,
        ]);
    }


    public function updateCart(Request $request)
    {

    }
}

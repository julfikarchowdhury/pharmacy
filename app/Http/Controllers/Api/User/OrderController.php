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
            if ($request->hasFile('prescription')) {
                $prescriptionPath = $this->saveNewImage(
                    $request->file('prescription'),
                    'order_prescriptions'
                );
            }
            // Save the order
            $order = Order::create([
                'tracking_id' => rand(),
                'order_type' => $request->order_type,
                'customer_id' => $request->customer_id,
                'total' => $request->total ?? 0,
                'sub_total' => $request->sub_total ?? 0,
                'delivery_address' => $request->delivery_address,
                'delivery_lat' => $request->delivery_lat,
                'delivery_long' => $request->delivery_long,
                'status' => 'order_placed',
                'date' => $request->date,
                'pharmacy_id' => $request->pharmacy_id,
                'discount_by_points' => $request->discount_by_points ?? 0,
                'pharmacy_discount' => $request->pharmacy_discount ?? 0,
                'delivery_charge' => $request->delivery_charge ?? 0,
                'tax' => $request->tax ?? 0,
                'payment_type' => $request->payment_type ?? 'cod',
                'payment_status' => 'due',
                'note' => $request->note,
                'prescription' => $prescriptionPath ?? null
            ]);

            // Save the order details
            if ($request->order_details) {
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
            }

            $this->logOrderStatus($order);

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

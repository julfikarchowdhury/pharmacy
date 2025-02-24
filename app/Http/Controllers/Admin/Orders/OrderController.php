<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Traits\HandlesDeleteExceptions;
use App\Traits\PushNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    use HandlesDeleteExceptions, PushNotification;

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $orders = Order::with(['customer', 'pharmacy']);
            if ($request->filled('order_type')) {
                $orders->where('order_type', $request->order_type);
            }
            if ($request->filled('pharmacy_id')) {
                $orders->where('pharmacy_id', $request->pharmacy_id);
            }
            if ($request->filled('status')) {
                $orders->where('status', $request->status);
            }
            //dd($orders);
            return DataTables::of($orders)
                ->addColumn('pharmacy_name', function ($order) {
                    return $order->pharmacy?->name;
                })
                ->addColumn('customer_name', function ($order) {
                    return optional($order->customer)->name;
                })
                ->addColumn('total', function ($order) {
                    return number_format($order->total, 2) . ' ৳';
                })
                ->addColumn('status', function ($order) {
                    $statuses = [
                        'order_placed' => 'Order Placed',
                        'store_accepts' => 'Store Accepts',
                        'store_rejects' => 'Store Rejects',
                        'ready_for_rider' => 'Ready for Rider',
                        'rider_assigned' => 'Rider Assigned',
                        'out_for_delivery' => 'Out for Delivery',
                        'delivered' => 'Delivered',
                        'canceled' => 'Canceled',
                    ];

                    if ($order->status == 'delivered' || $order->status == 'canceled') {
                        // Show badge instead of dropdown
                        $badgeClass = $order->status == 'delivered' ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $badgeClass . '">' . $statuses[$order->status] . '</span>';
                    } else {
                        // Show dropdown for other statuses
                        $options = '';
                        foreach ($statuses as $value => $label) {
                            // Prevent selecting "delivered" if canceled and vice versa
                            if (
                                ($order->status == 'canceled' && $value == 'delivered') ||
                                ($order->status == 'delivered' && $value == 'canceled')
                            ) {
                                continue;
                            }

                            $selected = $order->status == $value ? 'selected' : '';
                            $options .= "<option value=\"$value\" $selected>$label</option>";
                        }

                        return '<select class="form-control form-control-sm order-status" data-order-id="' . $order->id . '">
                                    ' . $options . '
                                </select>';
                    }
                })
                ->addColumn('payment_status', function ($order) {
                    return $order->payment_status == 'paid'
                        ? '<span class="badge badge-success" style="cursor: pointer;" onclick="changePaymentStatus(' . $order->id . ')">Paid</span>'
                        : '<span class="badge badge-danger" style="cursor: pointer;" onclick="changePaymentStatus(' . $order->id . ')">Due</span>';
                })
                ->addColumn('actions', function ($order) {
                    return '
                <a href="' . route('orders.show', $order->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> 
                </a>
                '
                    ;
                    // <button class="btn btn-danger btn-sm" onclick="deleteOrder(' . $order->id . ')">
                    //     <i class="fas fa-trash"></i> 
                    // </button>
                })
                ->rawColumns(['status', 'actions', 'payment_status'])
                ->make(true);
        }

        $pharmacies = Pharmacy::all();
        return view('admin.orders.index', compact('pharmacies'));
    }
    public function show(Order $order)
    {
        $order->load(['customer', 'pharmacy.owner', 'orderDetails.medicine']);
        $statuses = [
            'order_placed' => 'Order Placed',
            'store_accepts' => 'Store Accepts',
            'store_rejects' => 'Store Rejects',
            'ready_for_rider' => 'Ready for Rider',
            'rider_assigned' => 'Rider Assigned',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'canceled' => 'Canceled',
        ];

        return view('admin.orders.show', compact('order', 'statuses'));
    }



    public function changeStatus(Order $order, Request $request)
    {
        try {
            DB::beginTransaction();
            // Prevent delivered orders from being canceled and vice versa
            if (
                ($order->status === 'delivered' && $request->status === 'canceled') ||
                ($order->status === 'canceled' && $request->status === 'delivered')
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change a delivered or canceled order.'
                ], 400);
            }

            // Update the order status
            $order->update(['status' => $request->status]);

            $this->logOrderStatus($order);

            // Send push notification to user
            $this->sendOrderStatusNotification(
                $order->customer_id,
                $order->status,
                $order->id
            );
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.'
            ], 500);
        }
    }

    public function changePaymentStatus(Order $order)
    {
        try {
            $newStatus = $order->payment_status === 'paid' ? 'due' : 'paid';
            $order->update(['payment_status' => $newStatus]);
            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating payment status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status.'
            ], 500);
        }
    }

    // public function destroy(Order $order)
    // {
    //     return $this->handleDelete(
    //         function () use ($order) {
    //             $order->delete();
    //         },
    //         'Order',
    //     );
    // }



}

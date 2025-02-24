<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\ManualOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ManualOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $orders = ManualOrder::with(['customer', 'pharmacy'])->get();
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
                    return '<select class="form-control form-control-sm order-status" data-order-id="' . $order->status . '">
                            <option value="pending" ' . ($order->status == 'pending' ? 'selected' : '') . '>Pending</option>
                            <option value="processing" ' . ($order->status == 'processing' ? 'selected' : '') . '>Processing</option>
                            <option value="delivered" ' . ($order->status == 'delivered' ? 'selected' : '') . '>Delivered</option>
                            <option value="cancelled" ' . ($order->status == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                        </select>';
                })
                ->addColumn('actions', function ($order) {
                    return '
                <a href="' . route('orders.show', $order->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> 
                </a>
                <button class="btn btn-danger btn-sm" onclick="deleteOrder(' . $order->id . ')">
                    <i class="fas fa-trash"></i> 
                </button>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.orders.manual.index');
    }
}

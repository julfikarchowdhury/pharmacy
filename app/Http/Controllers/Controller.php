<?php

namespace App\Http\Controllers;

use App\Models\OrderStatusLog;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected function paginateData($paginator)
    {
        return [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * Log order status change to the database
     */
    protected function logOrderStatus($order)
    {
        try {
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_at' => now(),
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to log order status', [
                'order_id' => $order->id,
                'status' => $order->status,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

}

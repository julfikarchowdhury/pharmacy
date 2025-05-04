<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\ManualOrder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusLog;
use App\Models\Pharmacy;
use App\Traits\ImageHelper;
use App\Traits\PushNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    use ImageHelper, PushNotification;
    public function placeOrder(PlaceOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

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
                'customer_id' => $user->id,
                'total' => $request->total ?? 0,
                'sub_total' => $request->sub_total ?? 0,
                'delivery_address' => $request->delivery_address,
                'delivery_lat' => $request->delivery_lat,
                'delivery_long' => $request->delivery_long,
                'status' => 'order_placed',
                'date' => now(),
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

            $user->update([
                'points' => $user->points - ($request->discount_by_points / setting()->points_conversion),
            ]);

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
    public function allOrders($type)
    {
        $query = Order::where('customer_id', auth()->user()->id); // Keeps it as a query builder

        switch ($type) {
            case 'canceled':
                $query->where('status', 'canceled');
                break;
            case 'delivered':
                $query->where('status', 'delivered');
                break;
            case 'all':
                // No filtering needed
                break;
            default:
                $query->whereNotIn('status', ['delivered', 'canceled']);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully.',
            'orders' => OrderResource::collection($query->get()),
        ], 200);
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


    public function getDeliveryCharge(Pharmacy $pharmacy)
    {
        try {
            $user = auth()->user();

            if (!$user || !$user->lat || !$user->long) {
                return response()->json(['error' => 'User location not found'], 400);
            }

            if (!$pharmacy->lat || !$pharmacy->long) {
                return response()->json(['error' => 'Pharmacy location not found'], 400);
            }

            // Get delivery charge rate from settings
            $rate = setting()->delivery_charge_rate ?? 10; // Default rate if not found

            // Make a request to the Google Maps API to get the road distance
            $distance = $this->getRoadDistance(
                $user->lat,
                $user->long,
                $pharmacy->lat,
                $pharmacy->long
            );

            if ($distance === null) {
                return response()->json(['error' => 'Unable to calculate road distance'], 400);
            }

            // Calculate delivery charge
            $deliveryCharge = $distance * $rate;

            return response()->json([
                'success' => true,
                'message' => 'Delivery charge calculated successfully.',
                'distance_km' => round($distance, 2),
                'delivery_charge' => round($deliveryCharge, 2),
                'tax_percentage' => round(setting()->tax_percentage, 2)
            ]);
        } catch (Exception $e) {
            Log::error('Error calculating delivery charge: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'pharmacy_id' => $pharmacy->id ?? null,
                'exception' => $e
            ]);

            return response()->json(['error' => 'Something went wrong while calculating delivery charge'], 500);
        }
    }

    /**
     * Get road distance using Google Maps Directions API.
     */
    private function getRoadDistance($lat1, $lon1, $lat2, $lon2)
    {
        try {
            $apiKey = env('YOUR_GOOGLE_MAPS_API_KEY');
            if (empty($apiKey)) {
                return 1;
            }
            $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
                'origin' => "$lat1,$lon1",
                'destination' => "$lat2,$lon2",
                'key' => $apiKey
            ]);

            $data = $response->json();

            // Check for valid response
            if (isset($data['routes'][0]['legs'][0]['distance']['value'])) {
                // Distance in meters, convert to kilometers
                return $data['routes'][0]['legs'][0]['distance']['value'] / 1000;
            }

            Log::warning('Google Maps API response did not contain distance data', [
                'origin' => "$lat1,$lon1",
                'destination' => "$lat2,$lon2",
                'response' => $data
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching road distance from Google Maps API: ' . $e->getMessage(), [
                'origin' => "$lat1,$lon1",
                'destination' => "$lat2,$lon2",
                'exception' => $e
            ]);

            return null;
        }
    }


}

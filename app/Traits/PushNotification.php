<?php
namespace App\Traits;

use App\Models\Pharmacy;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Log;

trait PushNotification
{
    /**
     * Send a push notification for order status update.
     */
    public function sendOrderDetailRejectionNotification(
        $userId,
        $orderId,
        $orderTrackingId,
        $medicineName
    ) {
        try {
            $user = User::find($userId);
            if ($user && $user->device_token) {
                $title = "Medicine has been removed from order !";
                $message = ucfirst($medicineName) . " has been removed from your order due to unavailabilty from order #" . $orderTrackingId;

                $this->saveNotification(
                    $userId,
                    $title,
                    $message,
                    $orderId
                );

                $this->sendPushNotification(
                    $user->device_token,
                    $title,
                    $message,
                    $orderId
                );
            } else {
                Log::warning("User not found or device token is missing
                 for user ID: {$userId}");
            }
        } catch (Exception $e) {
            Log::error("Error sending order status notification 
            for user ID: {$userId}. Error: " . $e->getMessage());
        }
    }

    /**
     * Send a push notification for order status update.
     */
    public function sendOrderStatusNotification(
        $userId,
        $status,
        $orderId,
    ) {
        try {
            $user = User::find($userId);
            if ($user && $user->device_token) {
                $title = "Order Status Updated";
                $message = "Your order status has
                 been updated to: " . ucfirst($status);

                $this->saveNotification(
                    $userId,
                    $title,
                    $message,
                    $orderId
                );

                $this->sendPushNotification(
                    $user->device_token,
                    $title,
                    $message,
                    $orderId
                );
            } else {
                Log::warning("User not found or device token is missing
                 for user ID: {$userId}");
            }
        } catch (Exception $e) {
            Log::error("Error sending order status notification 
            for user ID: {$userId}. Error: " . $e->getMessage());
        }
    }

    public function sendOrderNotificationToPharmacy(
        $pharmacyId,
        $orderTrackingId,
        $orderId
    ) {
        try {
            $pharmacy = Pharmacy::find($pharmacyId);

            if ($pharmacy && $pharmacy->user_id) {
                $pharmacyUser = User::find($pharmacy->user_id);

                if ($pharmacyUser && $pharmacyUser->device_token) {
                    $title = "New Order Placed";
                    $message = "A new order has been placed with
                     Tracking ID: " . $orderTrackingId . ". Please review the order details.";

                    $this->saveNotification(
                        $pharmacyUser->id,
                        $title,
                        $message,
                        $orderId
                    );

                    $this->sendPushNotification(
                        $pharmacyUser->device_token,
                        $title,
                        $message,
                        $orderId
                    );
                } else {
                    Log::warning("Pharmacy user not found or device token 
                    is missing for pharmacy ID: {$pharmacyId}");
                }
            } else {
                Log::warning("Pharmacy not found for pharmacy ID: {$pharmacyId}");
            }
        } catch (Exception $e) {
            Log::error("Error sending order notification to store
             for pharmacy ID: {$pharmacyId}. Error: " . $e->getMessage());
        }
    }

    /**
     * Save notification to the database.
     */
    private function saveNotification(
        $userId,
        $title,
        $message,
        $orderId
    ) {
        try {
            Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'order_id' => $orderId,
                'status' => 'pending',
            ]);
        } catch (Exception $e) {
            Log::error("Error saving notification for user
             ID: {$userId}. Error: " . $e->getMessage());
        }
    }

    /**
     * Send push notification using Firebase Cloud Messaging (FCM).
     */
    private function sendPushNotification(
        $deviceToken,
        $title,
        $message,
        $orderId
    ) {
        try {
            $firebaseServerKey = env('FIREBASE_SERVER_KEY');

            $payload = [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                    'sound' => 'default'
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'status' => 'done',
                    'order_id' => $orderId
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $firebaseServerKey,
                'Content-Type' => 'application/json'
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->failed()) {
                Log::error("Error sending push notification for
                 order ID: {$orderId}. Response: " . $response->body());
            }
        } catch (Exception $e) {
            Log::error("Error sending push notification for order 
            ID: {$orderId}. Error: " . $e->getMessage());
        }
    }
}

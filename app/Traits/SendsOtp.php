<?php
namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

trait SendsOtp
{
    public function sendOtpToEmail($user, $otp)
    {
        try {
            $subject = 'Your OTP for Password Reset';
            $message = "Your OTP for resetting your password is: {$otp}. This OTP will expire in 10 minutes.";

            // Directly send the OTP email
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                    ->subject($subject);
            });

            // Optionally log the OTP sent for debugging purposes
            Log::info("OTP sent to email: {$user->email} - OTP: {$otp}");

        } catch (Exception $e) {
            Log::error('Failed to send OTP via email: ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendOtpToPhone($user, $otp)
    {
        try {
            // Twilio credentials
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_PHONE_NUMBER'); // Twilio phone number

            $client = new Client($sid, $token);

            $client->messages->create(
                $user->phone,
                [
                    'from' => $from,
                    'body' => "Your OTP is: {$otp}"
                ]
            );

            Log::info("OTP sent to phone: {$user->phone} - OTP: {$otp}");

        } catch (Exception $e) {
            Log::error('Failed to send OTP via phone: ' . $e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommonAuthController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });
            $request->user()->update(['device_token' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ], 200);

        } catch (Exception $e) {
            Log::error('User logout failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'identifier' => 'required', // email or phone
                'method' => 'required|in:email,phone' // OTP sending method
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())->map(fn($error) => $error)->toArray(),
                ], 422);
            }
            $user = User::where('email', $request->identifier)
                ->orWhere('phone', $request->identifier)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }



            $user->otp = rand(100000, 999999);
            $user->otp_expiry = now()->addMinutes(10);
            $user->save();

            // Send OTP based on selected method
            if ($request->method == 'email') {
                $this->sendOtpToEmail($user, $user->otp);
            } else {
                $this->sendOtpToPhone($user, $user->otp);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.'
            ], 200);

        } catch (Exception $e) {
            Log::error('Forget password failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'otp' => 'required|numeric|digits:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())->map(fn($error) => $error)->toArray(),
                ], 422);
            }
            $user = User::find($request->user_id);

            if (!$user || !$user->otp || $user->otp_expiry < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired or is invalid.'
                ], 400);
            }

            if ($user->otp !== $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], 400);
            }

            // Clear OTP and expiry
            $user->update(['otp' => null, 'otp_expiry' => null]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.'
            ], 200);

        } catch (Exception $e) {
            Log::error('OTP verification failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'new_password' => 'required|string|min:8|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())->map(fn($error) => $error)->toArray(),
                ], 422);
            }
            $user = User::find($request->user_id);

            // Reset password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.'
            ], 200);

        } catch (Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|max:6|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())->map(fn($error) => $error)->toArray(),
                ], 422);
            }
            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 400);
            }

            // Update password
            $user->update(['password' => Hash::make($request->new_password)]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.'
            ], 200);

        } catch (Exception $e) {
            Log::error('Password change failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function setDeviceToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => collect($validator->errors())->map(fn($error) => $error)->toArray(),
                ], 422);
            }
            $user = auth()->user();

            $user->update(['device_token' => $request->device_token]);

            return response()->json([
                'success' => true,
                'message' => 'Device token updated successfully.'
            ], 200);

        } catch (Exception $e) {
            Log::error('Failed to set device token: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }

    }
}

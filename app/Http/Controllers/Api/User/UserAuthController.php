<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ImageHelper;
use App\Traits\SendsOtp;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserAuthController extends Controller
{
    use ImageHelper, SendsOtp;

    /**
     * Register a new user
     */
    public function register(RegistrationRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->lat = $request->lat;
            $user->long = $request->long;
            $user->password = bcrypt($request->password);
            $user->role = 'user';

            // Save image if provided
            if ($request->hasFile('image')) {
                $user->image = $this->saveNewImage($request->file('image'), 'profile_images');
            }

            $user->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => new UserResource($user)
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        try {
            if (
                !Auth::attempt([
                    filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone' => $request->input('email'),
                    'password' => $request->input('password')
                ])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }


            $user = Auth::user();

            if ($user->role !== 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only users can log in.'
                ], 403);
            }
            $token = $user->createToken('UserAuthToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'data' => new UserResource($user)
            ], 200);

        } catch (Exception $e) {
            Log::error('User login failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    public function profileDetails(Request $request)
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Check if user exists
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Return user profile data
            return response()->json([
                'success' => true,
                'message' => 'User profile retrieved successfully.',
                'data' => new UserResource($user), // Use UserResource for consistent response formatting
            ], 200);

        } catch (Exception $e) {
            Log::error('User profile retrieval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile. Please try again.',
            ], 500);
        }
    }

    public function updateProfile(UpdateUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user(); // Get the currently authenticated user

            // Update user details
            $user->name = $request->name ?? $user->name;
            $user->phone = $request->phone ?? $user->phone;
            $user->email = $request->email ?? $user->email;
            $user->address = $request->address ?? $user->address;
            $user->lat = $request->lat ?? $user->lat;
            $user->long = $request->long ?? $user->long;

            // Save new image if provided
            if ($request->hasFile('image')) {
                $user->image = $this->updateImage(
                    $request->file('image'),
                    $user->image,
                    'profile_images'
                );
            }

            $user->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User profile updated successfully',
                'data' => new UserResource($user),
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('User profile update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Profile update failed. Please try again.',
            ], 500);
        }
    }


}
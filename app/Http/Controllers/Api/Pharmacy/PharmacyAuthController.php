<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PharmacyRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdatePharmacyRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\UserResource;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class PharmacyAuthController extends Controller
{
    use ImageHelper;

    /**
     * Register a new user
     */
    public function register(RegistrationRequest $userRequest, PharmacyRequest $pharmacyRequest)
    {
        DB::beginTransaction();

        try {
            $pharmacyOwner = new User();
            $pharmacyOwner->name = $userRequest->name;
            $pharmacyOwner->phone = $userRequest->phone;
            $pharmacyOwner->email = $userRequest->email;
            $pharmacyOwner->address = $userRequest->address;
            $pharmacyOwner->lat = $userRequest->lat;
            $pharmacyOwner->long = $userRequest->long;
            $pharmacyOwner->password = bcrypt($userRequest->password);
            $pharmacyOwner->role = 'pharmacy_owner';

            if ($userRequest->hasFile('image')) {
                $pharmacyOwner->image = $this->saveNewImage($userRequest->file('image'), 'profile_images');
            }

            $pharmacyOwner->save();

            $pharmacy = new Pharmacy();
            $pharmacy->user_id = $pharmacyOwner->id;
            $pharmacy->name = $pharmacyRequest->pharmacy_name;
            $pharmacy->address = $pharmacyRequest->pharmacy_address;
            $pharmacy->lat = $pharmacyRequest->pharmacy_lat;
            $pharmacy->long = $pharmacyRequest->pharmacy_long;
            if ($pharmacyRequest->hasFile('logo')) {
                $pharmacy->logo = $this->saveNewImage($pharmacyRequest->file('logo'), 'pharmacies');
            }
            if ($pharmacyRequest->hasFile('banner')) {
                $pharmacy->banner = $this->saveNewImage($pharmacyRequest->file('banner'), 'pharmacies');
            }
            $pharmacy->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pharmacy registered successfully',
                'data' => [
                    'pharmacy_owner' => new UserResource($pharmacyOwner),
                    'pharmacy' => new PharmacyResource($pharmacy),
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Pharmacy registration failed: ' . $e->getMessage());

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
            // Attempt to log the user in using email and password
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


            // Retrieve the authenticated pharmacy owner and eager load the pharmacy relationship
            $pharmacyOwner = Auth::user()->load('pharmacy');

            if ($pharmacyOwner->role !== 'pharmacy_owner') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only pharmacy owners can log in.'
                ], 403);
            }

            // Generate a token for the user
            $token = $pharmacyOwner->createToken('PharmacyAuthToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'data' => new UserResource($pharmacyOwner)
            ], 200);

        } catch (Exception $e) {
            Log::error('Pharmacy login failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    public function profileDetails(Request $request)
    {
        try {
            // Get the authenticated user (pharmacy owner)
            $user = $request->user();

            // Check if user exists and is a pharmacy owner
            if (!$user || $user->role !== 'pharmacy_owner') {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or not authorized.',
                ], 404);
            }

            // Get the associated pharmacy for the pharmacy owner
            $pharmacy = Pharmacy::where('user_id', $user->id)->first();

            if (!$pharmacy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pharmacy not found.',
                ], 404);
            }

            // Return pharmacy owner and pharmacy profile data
            return response()->json([
                'success' => true,
                'message' => 'Pharmacy profile retrieved successfully.',
                'data' => [
                    'pharmacy_owner' => new UserResource($user), // UserResource for consistent format
                    'pharmacy' => new PharmacyResource($pharmacy), // PharmacyResource for consistent format
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error('Pharmacy profile retrieval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile. Please try again.',
            ], 500);
        }
    }

    public function updateProfile(UpdateUserRequest $userRequest, UpdatePharmacyRequest $pharmacyRequest)
    {
        DB::beginTransaction();

        try {
            $pharmacyOwner = auth()->user(); // Get the currently authenticated pharmacy owner

            // Update pharmacy owner details
            $pharmacyOwner->name = $userRequest->name ?? $pharmacyOwner->name;
            $pharmacyOwner->phone = $userRequest->phone ?? $pharmacyOwner->phone;
            $pharmacyOwner->address = $userRequest->address ?? $pharmacyOwner->address;
            $pharmacyOwner->lat = $userRequest->lat ?? $pharmacyOwner->lat;
            $pharmacyOwner->long = $userRequest->long ?? $pharmacyOwner->long;


            // Save new image if provided
            if ($userRequest->hasFile('image')) {
                $pharmacyOwner->image = $this->updateImage(
                    $userRequest->file('image'),
                    $pharmacyOwner->image,
                    'profile_images'
                );
            }

            $pharmacyOwner->save();

            // Update pharmacy details
            $pharmacy = $pharmacyOwner->pharmacy; // Assuming the user has one pharmacy related to them
            $pharmacy->name = $pharmacyRequest->pharmacy_name ?? $pharmacy->name;
            $pharmacy->address = $pharmacyRequest->pharmacy_address ?? $pharmacy->address;
            $pharmacy->lat = $pharmacyRequest->pharmacy_lat ?? $pharmacy->lat;
            $pharmacy->long = $pharmacyRequest->pharmacy_long ?? $pharmacy->long;

            // Save new pharmacy images if provided
            if ($pharmacyRequest->hasFile('logo')) {
                $pharmacy->logo = $this->updateImage(
                    $pharmacyRequest->file('logo'),
                    $pharmacy->logo,
                    'pharmacies'
                );
            }
            if ($pharmacyRequest->hasFile('banner')) {
                $pharmacy->banner = $this->updateImage(
                    $pharmacyRequest->file('banner'),
                    $pharmacy->banner,
                    'pharmacies'
                );
            }

            $pharmacy->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pharmacy profile updated successfully',
                'data' => [
                    'pharmacy_owner' => new UserResource($pharmacyOwner),
                    'pharmacy' => new PharmacyResource($pharmacy),
                ],
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Pharmacy profile update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Profile update failed. Please try again.',
            ], 500);
        }
    }

}

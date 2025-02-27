<?php

use App\Http\Controllers\Api\CommonAuthController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\Pharmacy\ManagePharmacyController;
use App\Http\Controllers\Api\Pharmacy\PharmacyOrderController;
use App\Http\Controllers\Api\Pharmacy\PharmacyAuthController;
use App\Http\Controllers\Api\User\OrderController;
use App\Http\Controllers\Api\User\UserAuthController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



//user management
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);

Route::get('/all-medicines', [MedicineController::class, 'allMedicines']);
Route::get('/medicine-details/{medicine}/{pharmacy}', [MedicineController::class, 'medicineDetails']);
Route::get('/medicine-wise-pharmacies/{medicine}', [MedicineController::class, 'medicineWisePharmacies']);

//gym tips
Route::get('/tip-details/{tip}', [GeneralController::class, 'tipsDetails']);
Route::get('/tips/{type}', [GeneralController::class, 'gymTipTitles']);

//all pharmacies
Route::get('all-pharmacies', [GeneralController::class, 'allPharmacies']);
Route::get('pharmacy-wise-medicine/{pharmacy}', [GeneralController::class, 'getMedicinesByPharmacy']);

//forget password
Route::post('/user-forget-password', [CommonAuthController::class, 'forgetPassword']);
Route::post('/user-verify-otp', [CommonAuthController::class, 'verifyOtp']);
Route::post('/user-reset-password', [CommonAuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/logout', [CommonAuthController::class, 'logout']);
    Route::get('/user-profile-details', [UserAuthController::class, 'profileDetails']);

    //set device token
    Route::post('/set-user-device-token', [CommonAuthController::class, 'setDeviceToken']);

    Route::post('/user-change-password', [CommonAuthController::class, 'changePassword']);

    Route::post('/user-update-profile', [UserAuthController::class, 'updateProfile']);

    Route::get('/sliders', [GeneralController::class, 'sliders']);

    Route::post('/place-order', [OrderController::class, 'placeOrder']);
    Route::get('/track-order/{orderId}', [OrderController::class, 'trackOrder']);

    Route::get('/user-all-orders/{type}', [OrderController::class, 'allOrders']);

    //get delivery charge according to the distance
    Route::get('/get-delivery-charge/{pharmacy}', [OrderController::class, 'getDeliveryCharge']);

    //not complete
    Route::post('/add-drug-request', [MedicineController::class, 'addDrugRequest']);
    Route::get('/add-drug-dropdown-data', [MedicineController::class, 'addDrugDropdownData']);

});



//pharmacy management
Route::post('/pharmacy-register', [PharmacyAuthController::class, 'register']);
Route::post('/pharmacy-login', [PharmacyAuthController::class, 'login']);

//forget password
Route::post('/pharmacy-forget-password', [CommonAuthController::class, 'forgetPassword']);
Route::post('/pharmacy-verify-otp', [CommonAuthController::class, 'verifyOtp']);

Route::post('/pharmacy-reset-password', [CommonAuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', 'role:pharmacy_owner'])->group(function () {
    Route::post('/pharmacy-logout', [CommonAuthController::class, 'logout']);

    Route::get('/pharmacy-profile-details', [PharmacyAuthController::class, 'profileDetails']);

    //set device token
    Route::post('/set-pharmacy-device-token', [CommonAuthController::class, 'setDeviceToken']);

    Route::post('/pharmacy-change-password', [CommonAuthController::class, 'changePassword']);

    Route::post('/pharmacy-update-profile', [PharmacyAuthController::class, 'updateProfile']);

    //add medicine to shop
    Route::post('/add-medicines-to-pharmacy', [ManagePharmacyController::class, 'addMedicines']);
    Route::post('/remove-medicines-from-pharmacy', [ManagePharmacyController::class, 'removeMedicines']);

    //get medicine that are not inside the pharmacy 
    Route::get('/get-medicine-for-pharmacy/{pharmacy}', [ManagePharmacyController::class, 'getMedicinesNotSynced']);

    Route::get('/new-orders/{type}', [PharmacyOrderController::class, 'newOrders']);

    Route::get('/all-orders/{type}/{status}', [PharmacyOrderController::class, 'allOrders']);
    Route::get('/order-details/{order}', [PharmacyOrderController::class, 'orderDetails']);
    Route::post('/update-status-order/{order}', [PharmacyOrderController::class, 'updateStatus']);
    Route::post('/accept-reject-medicine-of-order/{orderDetail}', [PharmacyOrderController::class, 'acceptRejectMedicineOfOrder']);

    Route::get('/phamracies-medicines', [PharmacyOrderController::class, 'getMedicinesByPharmacy']);

    Route::post('/add-details-of-order/{order}', [PharmacyOrderController::class, 'addDetailsToManualOrder']);

});
<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Medicine\CategoryController;
use App\Http\Controllers\Admin\Medicine\ConcentrationController;
use App\Http\Controllers\Admin\Medicine\MedicineCompanyController;
use App\Http\Controllers\Admin\Medicine\MedicineController;
use App\Http\Controllers\Admin\Medicine\MedicineGenericController;
use App\Http\Controllers\Admin\Medicine\UnitController;
use App\Http\Controllers\Admin\Orders\OrderController;
use App\Http\Controllers\Admin\Orders\ManualOrderController;
use App\Http\Controllers\Admin\PharmacyController;
use App\Http\Controllers\Admin\TipController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('admin/login-post', [AuthController::class, 'loginPost'])->name('admin.login.post');
Route::get('/', [AuthController::class, 'login'])->name('admin.login');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::get('admin/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('settings/update', [DashboardController::class, 'updateSettings'])->name('settings.update');

    //order management 
    Route::resource('orders', OrderController::class)->except(['create', 'edit', 'store', 'update']);
    Route::post('/orders/change-status/{order}', [OrderController::class, 'changeStatus'])->name('orders.changeStatus');
    Route::post('/orders/change-payment-status/{order}', [OrderController::class, 'changePaymentStatus'])->name('orders.changePaymentStatus');

    //order management 
    Route::resource('manual_orders', ManualOrderController::class)->except(['create', 'edit', 'store', 'update']);
    Route::post('/manual_orders/change-status/{manual_order}', [ManualOrderController::class, 'changeStatus'])->name('manual_orders.changeStatus');

    //user management
    Route::resource('users', UserController::class)->except(['create', 'edit', 'store', 'update']);
    Route::post('/users/change-status/{user}', [UserController::class, 'changeStatus'])->name('users.changeStatus');

    //pharmacy management
    Route::resource('pharmacies', PharmacyController::class)->except(['create', 'edit', 'store', 'update']);
    Route::post('/pharmacies/change-status/{pharmacy}', [PharmacyController::class, 'changeStatus'])->name('pharmacies.changeStatus');

    //profile management & change password
    Route::get('admin/profile', [ProfileController::class, 'profile'])->name('admin.profile');
    Route::post('profile/change', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('password/update', [ProfileController::class, 'updatePassword'])->name('password.update');

    //category management
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::post('/categories/change-status/{category}', [CategoryController::class, 'changeStatus'])->name('categories.changeStatus');

    //medicine Company management
    Route::resource('medicine_companies', MedicineCompanyController::class)->except(['create', 'edit', 'show']);

    //meidicne generics
    Route::resource('medicine_generics', MedicineGenericController::class)->except(['create', 'edit', 'show']);

    //concentrations
    Route::resource('concentrations', ConcentrationController::class)->except(['create', 'edit', 'show']);

    //units
    Route::resource('units', UnitController::class)->except(['create', 'edit', 'show']);

    //medicine
    Route::resource('medicines', MedicineController::class);
    Route::post('/medicines/change-status/{medicine}', [MedicineController::class, 'changeStatus'])->name('medicines.changeStatus');
    Route::get('requested-medicines', [MedicineController::class, 'requestedMedicines'])->name('requested_medicines');

    //tips
    Route::resource('tips', TipController::class);
    Route::post('/tips/change-status/{tip}', [TipController::class, 'changeStatus'])->name('tips.changeStatus');


});
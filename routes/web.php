<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\PermissionManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - permission-based access control
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserManagementController::class);
    Route::resource('roles', RoleManagementController::class);
    Route::resource('permissions', PermissionManagementController::class);
});

Route::middleware('auth')->group(function () {
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('resellers', \App\Http\Controllers\ResellerController::class);
    Route::resource('cities', \App\Http\Controllers\CityController::class);
});


// Reseller Management Routes
Route::middleware(['auth'])->prefix('reseller-management')->name('resellers.')->group(function () {
    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\ResellerManagementController::class, 'dashboard'])->name('dashboard');

    // User Management (Resellers, Direct Resellers, Sub Users)
    Route::get('users', [\App\Http\Controllers\ResellerManagementController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\ResellerManagementController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\ResellerManagementController::class, 'store'])->name('users.store');
    // Add edit/update/destroy if needed

    // Targets
    Route::resource('targets', \App\Http\Controllers\ResellerTargetController::class);

    // Payments
    Route::get('payments', [\App\Http\Controllers\ResellerPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [\App\Http\Controllers\ResellerPaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [\App\Http\Controllers\ResellerPaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/dues', [\App\Http\Controllers\ResellerPaymentController::class, 'dues'])->name('payments.dues');
});

require __DIR__.'/auth.php';

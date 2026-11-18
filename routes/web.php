<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchaseProductController;
use App\Http\Controllers\Admin\SaleProductController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::prefix('setting')->name('setting.')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // System & Business Settings
    Route::get('settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // Password Change
    Route::get('password-change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('password-change', [PasswordController::class, 'update'])->name('password.change.update');

    // Artisan Cache Clear
    Route::get('cache-clear', [AdminController::class, 'clearCache'])->name('admin.cache.clear');

    // Hatchery Suppliers (হ্যাচারি সরবরাহকারী)
    Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('admin.supplier.toggle-status');
    Route::resource('suppliers', SupplierController::class)->names('admin.supplier');

    // Purchase Products (ক্রয় পণ্য তালিকা - Modal CRUD)
    Route::resource('purchase-products', PurchaseProductController::class)->only(['index', 'store', 'update', 'destroy'])->names('admin.purchase-product');

    // Sale Products (বিক্রয় পণ্য তালিকা - Modal CRUD)
    Route::resource('sale-products', SaleProductController::class)->only(['index', 'store', 'update', 'destroy'])->names('admin.sale-product');

    // Sales (মাছ ও পণ্য বিক্রয় ও চালান)
    Route::post('sales/{sale}/payments', [SalesController::class, 'addPayment'])->name('admin.sales.payment.store');
    Route::resource('sales', SalesController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->names('admin.sales');

    // Purchases (মাছ ও পণ্য ক্রয় ও বকেয়া হিসাব)
    Route::post('purchases/{purchase}/payments', [PurchaseController::class, 'addPayment'])->name('admin.purchase.payment.store');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->names('admin.purchase');

    // Location Management (Division, District, Zone)
    Route::patch('divisions/{division}/toggle-status', [DivisionController::class, 'toggleStatus'])->name('admin.division.toggle-status');
    Route::resource('divisions', DivisionController::class)->names('admin.division');

    Route::patch('districts/{district}/toggle-status', [DistrictController::class, 'toggleStatus'])->name('admin.district.toggle-status');
    Route::resource('districts', DistrictController::class)->names('admin.district');

    Route::patch('zones/{zone}/toggle-status', [ZoneController::class, 'toggleStatus'])->name('admin.zone.toggle-status');
    Route::resource('zones', ZoneController::class)->names('admin.zone');

    Route::get('locations/districts/{divisionId}', [LocationController::class, 'getDistricts'])->name('admin.locations.districts');
    Route::get('locations/zones/{districtId}', [LocationController::class, 'getZones'])->name('admin.locations.zones');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::redirect('/dashboard', '/admin/dashboard');

require __DIR__.'/auth.php';

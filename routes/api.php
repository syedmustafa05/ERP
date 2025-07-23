<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RequisitionController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\GoodsReceiptController;
use App\Http\Controllers\Api\InvoiceController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Routes (for demo purposes - in production these should be protected)
Route::prefix('v1')->name('api.')->group(function () {
    // Dashboard Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics/overview', [DashboardController::class, 'overview'])->name('analytics.overview');
    Route::get('/analytics/trends', [DashboardController::class, 'trends'])->name('analytics.trends');
    
    // Procurement Management API Resources
    Route::apiResource('requisitions', RequisitionController::class);
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    Route::apiResource('goods-receipts', GoodsReceiptController::class);
    Route::apiResource('invoices', InvoiceController::class);
    
    // Additional API Actions
    Route::prefix('requisitions/{requisition}')->name('requisitions.')->group(function () {
        Route::patch('/approve', [RequisitionController::class, 'approve'])->name('approve');
        Route::patch('/reject', [RequisitionController::class, 'reject'])->name('reject');
    });
    
    Route::prefix('purchase-orders/{purchaseOrder}')->name('purchase-orders.')->group(function () {
        Route::patch('/approve', [PurchaseOrderController::class, 'approve'])->name('approve');
        Route::patch('/issue', [PurchaseOrderController::class, 'issue'])->name('issue');
        Route::patch('/complete', [PurchaseOrderController::class, 'complete'])->name('complete');
        Route::patch('/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
    });
    
    Route::prefix('invoices/{invoice}')->name('invoices.')->group(function () {
        Route::patch('/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-paid');
        Route::patch('/cancel', [InvoiceController::class, 'cancel'])->name('cancel');
    });
    
    Route::prefix('vendors/{vendor}')->name('vendors.')->group(function () {
        Route::patch('/activate', [VendorController::class, 'activate'])->name('activate');
        Route::patch('/deactivate', [VendorController::class, 'deactivate'])->name('deactivate');
        Route::get('/purchase-orders', [VendorController::class, 'purchaseOrders'])->name('purchase-orders');
        Route::get('/performance', [VendorController::class, 'performance'])->name('performance');
    });
    
    // Reporting and Export Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/requisitions', [RequisitionController::class, 'report'])->name('requisitions');
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'report'])->name('purchase-orders');
        Route::get('/vendors', [VendorController::class, 'report'])->name('vendors');
        Route::get('/invoices', [InvoiceController::class, 'report'])->name('invoices');
    });
    
    // Search and Filter Routes
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/requisitions', [RequisitionController::class, 'search'])->name('requisitions');
        Route::get('/vendors', [VendorController::class, 'search'])->name('vendors');
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'search'])->name('purchase-orders');
        Route::get('/invoices', [InvoiceController::class, 'search'])->name('invoices');
    });
});
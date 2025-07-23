<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

// Procurement Management API Routes
Route::apiResource('requisitions', RequisitionController::class);
Route::apiResource('vendors', VendorController::class);
Route::apiResource('purchase-orders', PurchaseOrderController::class);
Route::apiResource('goods-receipts', GoodsReceiptController::class);
Route::apiResource('invoices', InvoiceController::class);

// Dashboard route for summary data
Route::get('/dashboard', function () {
    return response()->json([
        'requisitions_count' => \App\Models\Requisition::count(),
        'vendors_count' => \App\Models\Vendor::count(),
        'purchase_orders_count' => \App\Models\PurchaseOrder::count(),
        'goods_receipts_count' => \App\Models\GoodsReceipt::count(),
        'invoices_count' => \App\Models\Invoice::count(),
        'recent_requisitions' => \App\Models\Requisition::latest()->limit(5)->get(),
        'pending_purchase_orders' => \App\Models\PurchaseOrder::where('status', 'Pending Approval')->limit(5)->get(),
        'pending_invoices' => \App\Models\Invoice::where('status', 'Pending')->limit(5)->get(),
    ]);
});
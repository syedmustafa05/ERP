<?php

use App\Http\Controllers\API\RequisitionController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\PurchaseOrderController;
use App\Http\Controllers\API\GoodsReceiptController;
use App\Http\Controllers\API\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Resource Routes for ERP System
Route::apiResource('requisitions', RequisitionController::class);
Route::apiResource('vendors', VendorController::class);
Route::apiResource('purchase-orders', PurchaseOrderController::class);
Route::apiResource('goods-receipts', GoodsReceiptController::class);
Route::apiResource('invoices', InvoiceController::class);

// Additional routes for enhanced functionality
Route::get('/dashboard/stats', function () {
    return response()->json([
        'status' => 'success',
        'data' => [
            'requisitions_count' => \App\Models\Requisition::count(),
            'vendors_count' => \App\Models\Vendor::count(),
            'purchase_orders_count' => \App\Models\PurchaseOrder::count(),
            'goods_receipts_count' => \App\Models\GoodsReceipt::count(),
            'invoices_count' => \App\Models\Invoice::count(),
            'pending_requisitions' => \App\Models\Requisition::where('status', 'Pending')->count(),
            'pending_invoices' => \App\Models\Invoice::where('status', 'Pending')->count(),
        ]
    ]);
});
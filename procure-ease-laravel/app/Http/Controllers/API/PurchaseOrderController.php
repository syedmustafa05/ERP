<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $purchaseOrders = PurchaseOrder::with(['requisition', 'vendor', 'goodsReceipts', 'invoices'])->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $purchaseOrders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requisition_id' => 'required|exists:requisitions,id',
            'vendor_id' => 'required|exists:vendors,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => ['sometimes', Rule::in(['Issued', 'Pending Approval', 'Completed', 'Cancelled'])],
            'order_date' => 'required|date'
        ]);

        $purchaseOrder = PurchaseOrder::create($validated);
        $purchaseOrder->load(['requisition', 'vendor']);

        return response()->json([
            'status' => 'success',
            'message' => 'Purchase Order created successfully',
            'data' => $purchaseOrder
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with(['requisition', 'vendor', 'goodsReceipts', 'invoices'])->find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase Order not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase Order not found'
            ], 404);
        }

        $validated = $request->validate([
            'requisition_id' => 'sometimes|exists:requisitions,id',
            'vendor_id' => 'sometimes|exists:vendors,id',
            'total_amount' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in(['Issued', 'Pending Approval', 'Completed', 'Cancelled'])],
            'order_date' => 'sometimes|date'
        ]);

        $purchaseOrder->update($validated);
        $purchaseOrder->load(['requisition', 'vendor']);

        return response()->json([
            'status' => 'success',
            'message' => 'Purchase Order updated successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);

        if (!$purchaseOrder) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase Order not found'
            ], 404);
        }

        $purchaseOrder->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Purchase Order deleted successfully'
        ]);
    }
}

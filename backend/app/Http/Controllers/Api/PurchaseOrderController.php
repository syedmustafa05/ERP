<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $purchaseOrders = PurchaseOrder::with(['requisition', 'vendor', 'goodsReceipts', 'invoices'])->get();
        return response()->json($purchaseOrders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'requisition_id' => 'required|exists:requisitions,id',
                'vendor_id' => 'required|exists:vendors,id',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|in:Issued,Pending Approval,Completed,Cancelled',
                'order_date' => 'required|date'
            ]);

            $purchaseOrder = PurchaseOrder::create($validated);
            return response()->json($purchaseOrder->load(['requisition', 'vendor']), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::with(['requisition', 'vendor', 'goodsReceipts', 'invoices'])->find($id);
        
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase Order not found'], 404);
        }
        
        return response()->json($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);
        
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase Order not found'], 404);
        }

        try {
            $validated = $request->validate([
                'requisition_id' => 'sometimes|exists:requisitions,id',
                'vendor_id' => 'sometimes|exists:vendors,id',
                'total_amount' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:Issued,Pending Approval,Completed,Cancelled',
                'order_date' => 'sometimes|date'
            ]);

            $purchaseOrder->update($validated);
            return response()->json($purchaseOrder->load(['requisition', 'vendor']));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::find($id);
        
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase Order not found'], 404);
        }

        $purchaseOrder->delete();
        return response()->json(['message' => 'Purchase Order deleted successfully']);
    }
}

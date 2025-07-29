<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GoodsReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $goodsReceipts = GoodsReceipt::with(['purchaseOrder.requisition', 'purchaseOrder.vendor'])->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $goodsReceipts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'quantity_received' => 'required|integer|min:1',
            'item' => 'required|string|max:255'
        ]);

        $goodsReceipt = GoodsReceipt::create($validated);
        $goodsReceipt->load(['purchaseOrder.requisition', 'purchaseOrder.vendor']);

        return response()->json([
            'status' => 'success',
            'message' => 'Goods Receipt created successfully',
            'data' => $goodsReceipt
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::with(['purchaseOrder.requisition', 'purchaseOrder.vendor'])->find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goods Receipt not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $goodsReceipt
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goods Receipt not found'
            ], 404);
        }

        $validated = $request->validate([
            'purchase_order_id' => 'sometimes|exists:purchase_orders,id',
            'received_date' => 'sometimes|date',
            'quantity_received' => 'sometimes|integer|min:1',
            'item' => 'sometimes|string|max:255'
        ]);

        $goodsReceipt->update($validated);
        $goodsReceipt->load(['purchaseOrder.requisition', 'purchaseOrder.vendor']);

        return response()->json([
            'status' => 'success',
            'message' => 'Goods Receipt updated successfully',
            'data' => $goodsReceipt
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::find($id);

        if (!$goodsReceipt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Goods Receipt not found'
            ], 404);
        }

        $goodsReceipt->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Goods Receipt deleted successfully'
        ]);
    }
}

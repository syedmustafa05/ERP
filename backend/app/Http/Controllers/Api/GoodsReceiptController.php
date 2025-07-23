<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GoodsReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $goodsReceipts = GoodsReceipt::with('purchaseOrder')->get();
        return response()->json($goodsReceipts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'received_date' => 'required|date',
                'quantity_received' => 'required|integer|min:1',
                'item' => 'required|string|max:255'
            ]);

            $goodsReceipt = GoodsReceipt::create($validated);
            return response()->json($goodsReceipt->load('purchaseOrder'), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::with('purchaseOrder')->find($id);
        
        if (!$goodsReceipt) {
            return response()->json(['message' => 'Goods Receipt not found'], 404);
        }
        
        return response()->json($goodsReceipt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::find($id);
        
        if (!$goodsReceipt) {
            return response()->json(['message' => 'Goods Receipt not found'], 404);
        }

        try {
            $validated = $request->validate([
                'purchase_order_id' => 'sometimes|exists:purchase_orders,id',
                'received_date' => 'sometimes|date',
                'quantity_received' => 'sometimes|integer|min:1',
                'item' => 'sometimes|string|max:255'
            ]);

            $goodsReceipt->update($validated);
            return response()->json($goodsReceipt->load('purchaseOrder'));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::find($id);
        
        if (!$goodsReceipt) {
            return response()->json(['message' => 'Goods Receipt not found'], 404);
        }

        $goodsReceipt->delete();
        return response()->json(['message' => 'Goods Receipt deleted successfully']);
    }
}

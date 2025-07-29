<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $requisitions = Requisition::with('purchaseOrders')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $requisitions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'status' => ['sometimes', Rule::in(['Approved', 'Pending', 'Rejected'])],
            'requestedBy' => 'required|string|max:255',
            'date' => 'required|date'
        ]);

        $requisition = Requisition::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Requisition created successfully',
            'data' => $requisition
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $requisition = Requisition::with(['purchaseOrders.vendor', 'purchaseOrders.goodsReceipts', 'purchaseOrders.invoices'])->find($id);

        if (!$requisition) {
            return response()->json([
                'status' => 'error',
                'message' => 'Requisition not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $requisition
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $requisition = Requisition::find($id);

        if (!$requisition) {
            return response()->json([
                'status' => 'error',
                'message' => 'Requisition not found'
            ], 404);
        }

        $validated = $request->validate([
            'item' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
            'status' => ['sometimes', Rule::in(['Approved', 'Pending', 'Rejected'])],
            'requestedBy' => 'sometimes|string|max:255',
            'date' => 'sometimes|date'
        ]);

        $requisition->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Requisition updated successfully',
            'data' => $requisition
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $requisition = Requisition::find($id);

        if (!$requisition) {
            return response()->json([
                'status' => 'error',
                'message' => 'Requisition not found'
            ], 404);
        }

        $requisition->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Requisition deleted successfully'
        ]);
    }
}

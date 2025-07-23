<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $requisitions = Requisition::with('purchaseOrders')->get();
        return response()->json($requisitions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'status' => 'required|in:Approved,Pending,Rejected',
                'requestedBy' => 'required|string|max:255',
                'date' => 'required|date'
            ]);

            $requisition = Requisition::create($validated);
            return response()->json($requisition, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $requisition = Requisition::with('purchaseOrders')->find($id);
        
        if (!$requisition) {
            return response()->json(['message' => 'Requisition not found'], 404);
        }
        
        return response()->json($requisition);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $requisition = Requisition::find($id);
        
        if (!$requisition) {
            return response()->json(['message' => 'Requisition not found'], 404);
        }

        try {
            $validated = $request->validate([
                'item' => 'sometimes|string|max:255',
                'quantity' => 'sometimes|integer|min:1',
                'status' => 'sometimes|in:Approved,Pending,Rejected',
                'requestedBy' => 'sometimes|string|max:255',
                'date' => 'sometimes|date'
            ]);

            $requisition->update($validated);
            return response()->json($requisition);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $requisition = Requisition::find($id);
        
        if (!$requisition) {
            return response()->json(['message' => 'Requisition not found'], 404);
        }

        $requisition->delete();
        return response()->json(['message' => 'Requisition deleted successfully']);
    }
}

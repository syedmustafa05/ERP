<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $vendors = Vendor::with('purchaseOrders')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $vendors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required|string|max:20'
        ]);

        $vendor = Vendor::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor created successfully',
            'data' => $vendor
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $vendor = Vendor::with(['purchaseOrders.requisition', 'purchaseOrders.goodsReceipts', 'purchaseOrders.invoices'])->find($id);

        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $vendor
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:vendors,email,' . $id,
            'phone' => 'sometimes|string|max:20'
        ]);

        $vendor->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor updated successfully',
            'data' => $vendor
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        $vendor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor deleted successfully'
        ]);
    }
}

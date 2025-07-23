<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $vendors = Vendor::with('purchaseOrders')->get();
        return response()->json($vendors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'email' => 'required|email|unique:vendors,email',
                'phone' => 'required|string|max:20'
            ]);

            $vendor = Vendor::create($validated);
            return response()->json($vendor, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $vendor = Vendor::with('purchaseOrders')->find($id);
        
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }
        
        return response()->json($vendor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $vendor = Vendor::find($id);
        
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'contact' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:vendors,email,' . $id,
                'phone' => 'sometimes|string|max:20'
            ]);

            $vendor->update($validated);
            return response()->json($vendor);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $vendor = Vendor::find($id);
        
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted successfully']);
    }
}

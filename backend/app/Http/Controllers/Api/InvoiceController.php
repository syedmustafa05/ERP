<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $invoices = Invoice::with('purchaseOrder')->get();
        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'invoice_number' => 'required|string|unique:invoices,invoice_number',
                'amount' => 'required|numeric|min:0',
                'status' => 'required|in:Paid,Pending',
                'invoice_date' => 'required|date'
            ]);

            $invoice = Invoice::create($validated);
            return response()->json($invoice->load('purchaseOrder'), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $invoice = Invoice::with('purchaseOrder')->find($id);
        
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::find($id);
        
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        try {
            $validated = $request->validate([
                'purchase_order_id' => 'sometimes|exists:purchase_orders,id',
                'invoice_number' => 'sometimes|string|unique:invoices,invoice_number,' . $id,
                'amount' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:Paid,Pending',
                'invoice_date' => 'sometimes|date'
            ]);

            $invoice->update($validated);
            return response()->json($invoice->load('purchaseOrder'));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::find($id);
        
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}

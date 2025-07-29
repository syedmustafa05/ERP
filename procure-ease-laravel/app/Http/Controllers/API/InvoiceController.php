<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $invoices = Invoice::with(['purchaseOrder.requisition', 'purchaseOrder.vendor'])->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $invoices
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'status' => ['sometimes', Rule::in(['Paid', 'Pending'])],
            'invoice_date' => 'required|date'
        ]);

        $invoice = Invoice::create($validated);
        $invoice->load(['purchaseOrder.requisition', 'purchaseOrder.vendor']);

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice created successfully',
            'data' => $invoice
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $invoice = Invoice::with(['purchaseOrder.requisition', 'purchaseOrder.vendor'])->find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $invoice
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice not found'
            ], 404);
        }

        $validated = $request->validate([
            'purchase_order_id' => 'sometimes|exists:purchase_orders,id',
            'invoice_number' => 'sometimes|string|unique:invoices,invoice_number,' . $id,
            'amount' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in(['Paid', 'Pending'])],
            'invoice_date' => 'sometimes|date'
        ]);

        $invoice->update($validated);
        $invoice->load(['purchaseOrder.requisition', 'purchaseOrder.vendor']);

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice updated successfully',
            'data' => $invoice
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice not found'
            ], 404);
        }

        $invoice->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends BaseController
{
    public function index()
    {
        try {
            $invoices = Invoice::all();
            return $this->json($invoices);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch invoices: ' . $e->getMessage(), 500);
        }
    }
    
    public function show($id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->error('Invoice not found', 404);
            }
            return $this->json($invoice);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch invoice: ' . $e->getMessage(), 500);
        }
    }
    
    public function store()
    {
        try {
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            $errors = $this->validate($data, [
                'purchase_order_id' => 'required',
                'invoice_number' => 'required',
                'amount' => 'required',
                'invoice_date' => 'required'
            ]);
            
            if (!empty($errors)) {
                return $this->error('Validation failed', 422, $errors);
            }
            
            if (!isset($data['status'])) {
                $data['status'] = Invoice::STATUS_PENDING;
            }
            
            $invoice = Invoice::create($data);
            return $this->success($invoice, 'Invoice created successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to create invoice: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->error('Invoice not found', 404);
            }
            
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            $model = new Invoice($invoice);
            $model->fill($data);
            $model->id = $id;
            $updated = $model->save();
            
            return $this->success($updated, 'Invoice updated successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to update invoice: ' . $e->getMessage(), 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->error('Invoice not found', 404);
            }
            
            Invoice::destroy($id);
            return $this->success([], 'Invoice deleted successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete invoice: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Mark invoice as paid
     */
    public function markAsPaid($id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->error('Invoice not found', 404);
            }
            
            $data = $this->getInput();
            $paymentMethod = $data['payment_method'] ?? null;
            $referenceNumber = $data['reference_number'] ?? null;
            
            $model = new Invoice($invoice);
            $model->id = $id;
            $updated = $model->markAsPaid($paymentMethod, $referenceNumber);
            
            return $this->success($updated, 'Invoice marked as paid successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to mark invoice as paid: ' . $e->getMessage(), 500);
        }
    }
}
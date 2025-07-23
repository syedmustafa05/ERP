<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;

class PurchaseOrderController extends BaseController
{
    public function index()
    {
        try {
            $orders = PurchaseOrder::all();
            return $this->json($orders);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch purchase orders: ' . $e->getMessage(), 500);
        }
    }
    
    public function show($id)
    {
        try {
            $order = PurchaseOrder::find($id);
            if (!$order) {
                return $this->error('Purchase order not found', 404);
            }
            return $this->json($order);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch purchase order: ' . $e->getMessage(), 500);
        }
    }
    
    public function store()
    {
        try {
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            $errors = $this->validate($data, [
                'requisition_id' => 'required',
                'vendor_id' => 'required',
                'total_amount' => 'required',
                'order_date' => 'required'
            ]);
            
            if (!empty($errors)) {
                return $this->error('Validation failed', 422, $errors);
            }
            
            if (!isset($data['status'])) {
                $data['status'] = PurchaseOrder::STATUS_PENDING_APPROVAL;
            }
            
            $order = PurchaseOrder::create($data);
            return $this->success($order, 'Purchase order created successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to create purchase order: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id)
    {
        try {
            $order = PurchaseOrder::find($id);
            if (!$order) {
                return $this->error('Purchase order not found', 404);
            }
            
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            $model = new PurchaseOrder($order);
            $model->fill($data);
            $model->id = $id;
            $updated = $model->save();
            
            return $this->success($updated, 'Purchase order updated successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to update purchase order: ' . $e->getMessage(), 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $order = PurchaseOrder::find($id);
            if (!$order) {
                return $this->error('Purchase order not found', 404);
            }
            
            PurchaseOrder::destroy($id);
            return $this->success([], 'Purchase order deleted successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete purchase order: ' . $e->getMessage(), 500);
        }
    }
}
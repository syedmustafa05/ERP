<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;

class GoodsReceiptController extends BaseController
{
    public function index()
    {
        try {
            $receipts = GoodsReceipt::all();
            return $this->json($receipts);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch goods receipts: ' . $e->getMessage(), 500);
        }
    }
    
    public function show($id)
    {
        try {
            $receipt = GoodsReceipt::find($id);
            if (!$receipt) {
                return $this->error('Goods receipt not found', 404);
            }
            return $this->json($receipt);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch goods receipt: ' . $e->getMessage(), 500);
        }
    }
    
    public function store()
    {
        try {
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            $errors = $this->validate($data, [
                'purchase_order_id' => 'required',
                'received_date' => 'required',
                'quantity_received' => 'required',
                'item' => 'required'
            ]);
            
            if (!empty($errors)) {
                return $this->error('Validation failed', 422, $errors);
            }
            
            if (!isset($data['condition'])) {
                $data['condition'] = GoodsReceipt::CONDITION_GOOD;
            }
            
            $receipt = GoodsReceipt::create($data);
            return $this->success($receipt, 'Goods receipt created successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to create goods receipt: ' . $e->getMessage(), 500);
        }
    }
    
    public function update($id)
    {
        try {
            $receipt = GoodsReceipt::find($id);
            if (!$receipt) {
                return $this->error('Goods receipt not found', 404);
            }
            
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            $model = new GoodsReceipt($receipt);
            $model->fill($data);
            $model->id = $id;
            $updated = $model->save();
            
            return $this->success($updated, 'Goods receipt updated successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to update goods receipt: ' . $e->getMessage(), 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $receipt = GoodsReceipt::find($id);
            if (!$receipt) {
                return $this->error('Goods receipt not found', 404);
            }
            
            GoodsReceipt::destroy($id);
            return $this->success([], 'Goods receipt deleted successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete goods receipt: ' . $e->getMessage(), 500);
        }
    }
}
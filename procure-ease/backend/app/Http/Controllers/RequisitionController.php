<?php

namespace App\Http\Controllers;

use App\Models\Requisition;

/**
 * Requisition Controller
 * Handles CRUD operations for requisitions
 */
class RequisitionController extends BaseController
{
    /**
     * Get all requisitions
     */
    public function index()
    {
        try {
            $requisitions = Requisition::all();
            return $this->json($requisitions);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch requisitions: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get single requisition
     */
    public function show($id)
    {
        try {
            $requisition = Requisition::find($id);
            
            if (!$requisition) {
                return $this->error('Requisition not found', 404);
            }
            
            return $this->json($requisition);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch requisition: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Create new requisition
     */
    public function store()
    {
        try {
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            // Validate required fields
            $errors = $this->validate($data, [
                'item' => 'required',
                'quantity' => 'required',
                'requestedBy' => 'required',
                'date' => 'required'
            ]);
            
            if (!empty($errors)) {
                return $this->error('Validation failed', 422, $errors);
            }
            
            // Set default values
            if (!isset($data['status'])) {
                $data['status'] = Requisition::STATUS_PENDING;
            }
            
            if (!isset($data['priority'])) {
                $data['priority'] = Requisition::PRIORITY_MEDIUM;
            }
            
            $requisition = Requisition::create($data);
            
            return $this->success($requisition, 'Requisition created successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to create requisition: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Update requisition
     */
    public function update($id)
    {
        try {
            $requisition = Requisition::find($id);
            
            if (!$requisition) {
                return $this->error('Requisition not found', 404);
            }
            
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            // Fill and save
            $model = new Requisition($requisition);
            $model->fill($data);
            $model->id = $id;
            $updated = $model->save();
            
            return $this->success($updated, 'Requisition updated successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to update requisition: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Delete requisition
     */
    public function destroy($id)
    {
        try {
            $requisition = Requisition::find($id);
            
            if (!$requisition) {
                return $this->error('Requisition not found', 404);
            }
            
            Requisition::destroy($id);
            
            return $this->success([], 'Requisition deleted successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete requisition: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Approve requisition
     */
    public function approve($id)
    {
        try {
            $requisition = Requisition::find($id);
            
            if (!$requisition) {
                return $this->error('Requisition not found', 404);
            }
            
            $model = new Requisition($requisition);
            $model->id = $id;
            $updated = $model->approve();
            
            return $this->success($updated, 'Requisition approved successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to approve requisition: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Reject requisition
     */
    public function reject($id)
    {
        try {
            $requisition = Requisition::find($id);
            
            if (!$requisition) {
                return $this->error('Requisition not found', 404);
            }
            
            $model = new Requisition($requisition);
            $model->id = $id;
            $updated = $model->reject();
            
            return $this->success($updated, 'Requisition rejected successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to reject requisition: ' . $e->getMessage(), 500);
        }
    }
}
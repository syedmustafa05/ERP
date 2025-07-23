<?php

namespace App\Http\Controllers;

use App\Models\Vendor;

/**
 * Vendor Controller
 * Handles CRUD operations for vendors
 */
class VendorController extends BaseController
{
    /**
     * Get all vendors
     */
    public function index()
    {
        try {
            $vendors = Vendor::all();
            return $this->json($vendors);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch vendors: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get single vendor
     */
    public function show($id)
    {
        try {
            $vendor = Vendor::find($id);
            
            if (!$vendor) {
                return $this->error('Vendor not found', 404);
            }
            
            return $this->json($vendor);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch vendor: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Create new vendor
     */
    public function store()
    {
        try {
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            // Validate required fields
            $errors = $this->validate($data, [
                'name' => 'required'
            ]);
            
            if (!empty($errors)) {
                return $this->error('Validation failed', 422, $errors);
            }
            
            // Set default values
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            
            $vendor = Vendor::create($data);
            
            return $this->success($vendor, 'Vendor created successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to create vendor: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Update vendor
     */
    public function update($id)
    {
        try {
            $vendor = Vendor::find($id);
            
            if (!$vendor) {
                return $this->error('Vendor not found', 404);
            }
            
            $data = $this->getInput();
            $data = $this->sanitize($data);
            
            // Fill and save
            $model = new Vendor($vendor);
            $model->fill($data);
            $model->id = $id;
            $updated = $model->save();
            
            return $this->success($updated, 'Vendor updated successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to update vendor: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Delete vendor
     */
    public function destroy($id)
    {
        try {
            $vendor = Vendor::find($id);
            
            if (!$vendor) {
                return $this->error('Vendor not found', 404);
            }
            
            Vendor::destroy($id);
            
            return $this->success([], 'Vendor deleted successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete vendor: ' . $e->getMessage(), 500);
        }
    }
}
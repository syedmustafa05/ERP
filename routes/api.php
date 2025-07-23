<?php

/**
 * API Routes
 * Laravel-style route definitions
 */

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\InvoiceController;

/**
 * Route dispatcher
 */
function handleRequest($method, $path, $id = null)
{
    try {
        // Dashboard routes
        if ($path === 'dashboard' && $method === 'GET') {
            $controller = new DashboardController();
            return $controller->index();
        }
        
        // Requisition routes
        if ($path === 'requisitions') {
            $controller = new RequisitionController();
            switch ($method) {
                case 'GET':
                    return $id ? $controller->show($id) : $controller->index();
                case 'POST':
                    return $controller->store();
                case 'PUT':
                    return $controller->update($id);
                case 'DELETE':
                    return $controller->destroy($id);
            }
        }
        
        // Vendor routes
        if ($path === 'vendors') {
            $controller = new VendorController();
            switch ($method) {
                case 'GET':
                    return $id ? $controller->show($id) : $controller->index();
                case 'POST':
                    return $controller->store();
                case 'PUT':
                    return $controller->update($id);
                case 'DELETE':
                    return $controller->destroy($id);
            }
        }
        
        // Purchase Order routes
        if ($path === 'purchase-orders') {
            $controller = new PurchaseOrderController();
            switch ($method) {
                case 'GET':
                    return $id ? $controller->show($id) : $controller->index();
                case 'POST':
                    return $controller->store();
                case 'PUT':
                    return $controller->update($id);
                case 'DELETE':
                    return $controller->destroy($id);
            }
        }
        
        // Goods Receipt routes
        if ($path === 'goods-receipts') {
            $controller = new GoodsReceiptController();
            switch ($method) {
                case 'GET':
                    return $id ? $controller->show($id) : $controller->index();
                case 'POST':
                    return $controller->store();
                case 'PUT':
                    return $controller->update($id);
                case 'DELETE':
                    return $controller->destroy($id);
            }
        }
        
        // Invoice routes
        if ($path === 'invoices') {
            $controller = new InvoiceController();
            switch ($method) {
                case 'GET':
                    return $id ? $controller->show($id) : $controller->index();
                case 'POST':
                    return $controller->store();
                case 'PUT':
                    return $controller->update($id);
                case 'DELETE':
                    return $controller->destroy($id);
            }
        }
        
        // Special invoice action: mark as paid
        if ($path === 'invoices' && isset($_GET['action']) && $_GET['action'] === 'pay' && $method === 'PUT') {
            $controller = new InvoiceController();
            return $controller->markAsPaid($id);
        }
        
        // 404 Not Found
        http_response_code(404);
        return json_encode(['error' => 'Route not found']);
        
    } catch (Exception $e) {
        http_response_code(500);
        return json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
}
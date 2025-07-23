<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Invoice;

/**
 * Dashboard Controller
 * Handles dashboard statistics and overview data
 */
class DashboardController extends BaseController
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        try {
            $data = [
                'requisitions_count' => Requisition::count(),
                'vendors_count' => Vendor::count(),
                'purchase_orders_count' => PurchaseOrder::count(),
                'goods_receipts_count' => GoodsReceipt::count(),
                'invoices_count' => Invoice::count(),
                'total_orders_value' => $this->getTotalOrdersValue(),
                'pending_approvals' => count(PurchaseOrder::pendingApproval()),
                'recent_requisitions' => $this->getRecentRequisitions(),
                'pending_purchase_orders' => PurchaseOrder::pendingApproval(),
                'overdue_invoices' => count(Invoice::overdue())
            ];
            
            return $this->json($data);
            
        } catch (\Exception $e) {
            return $this->error('Failed to load dashboard data: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get total orders value
     */
    private function getTotalOrdersValue()
    {
        $orders = PurchaseOrder::all();
        $total = 0;
        
        foreach ($orders as $order) {
            $total += $order['total_amount'] ?? 0;
        }
        
        return $total;
    }
    
    /**
     * Get recent requisitions (last 5)
     */
    private function getRecentRequisitions()
    {
        $requisitions = Requisition::all();
        return array_slice($requisitions, 0, 5);
    }
}
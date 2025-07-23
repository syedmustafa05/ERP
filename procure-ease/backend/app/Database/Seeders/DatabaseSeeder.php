<?php

namespace App\Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Requisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Invoice;

/**
 * Database Seeder
 * Seeds the database with sample data
 */
class DatabaseSeeder
{
    public static function run()
    {
        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ]);
        
        // Create vendors
        $vendors = [
            [
                'name' => 'TechSupply Corp',
                'contact' => 'John Doe',
                'email' => 'contact@techsupply.com',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street, Silicon Valley, CA',
                'is_active' => 1,
                'rating' => 4.5
            ],
            [
                'name' => 'OfficeMax Solutions',
                'contact' => 'Jane Smith',
                'email' => 'sales@officemax.com',
                'phone' => '+1-555-0456',
                'address' => '456 Office Blvd, Business City, NY',
                'is_active' => 1,
                'rating' => 4.2
            ],
            [
                'name' => 'Global Suppliers Inc',
                'contact' => 'Mike Johnson',
                'email' => 'info@globalsuppliers.com',
                'phone' => '+1-555-0789',
                'address' => '789 Supply Lane, Commerce Town, TX',
                'is_active' => 1,
                'rating' => 4.8
            ]
        ];
        
        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
        
        // Create requisitions
        $requisitions = [
            [
                'item' => 'Laptops Dell XPS 13',
                'quantity' => 10,
                'status' => 'Approved',
                'requestedBy' => 'IT Department',
                'date' => date('Y-m-d', strtotime('-5 days')),
                'description' => 'High-performance laptops for development team',
                'estimated_cost' => 15000.00,
                'priority' => 'High'
            ],
            [
                'item' => 'Office Chairs Ergonomic',
                'quantity' => 25,
                'status' => 'Pending',
                'requestedBy' => 'HR Department',
                'date' => date('Y-m-d', strtotime('-3 days')),
                'description' => 'Ergonomic chairs for new office setup',
                'estimated_cost' => 6250.00,
                'priority' => 'Medium'
            ],
            [
                'item' => 'Printer Paper A4 Premium',
                'quantity' => 100,
                'status' => 'Approved',
                'requestedBy' => 'Admin Department',
                'date' => date('Y-m-d', strtotime('-2 days')),
                'description' => 'Premium quality paper for office printing',
                'estimated_cost' => 250.00,
                'priority' => 'Low'
            ],
            [
                'item' => 'Network Switches Cisco',
                'quantity' => 5,
                'status' => 'Rejected',
                'requestedBy' => 'IT Department',
                'date' => date('Y-m-d', strtotime('-1 day')),
                'description' => 'Network infrastructure upgrade',
                'estimated_cost' => 2500.00,
                'priority' => 'High'
            ],
            [
                'item' => 'Standing Desks',
                'quantity' => 15,
                'status' => 'Approved',
                'requestedBy' => 'HR Department',
                'date' => date('Y-m-d'),
                'description' => 'Height-adjustable desks for wellness program',
                'estimated_cost' => 7500.00,
                'priority' => 'Medium'
            ]
        ];
        
        foreach ($requisitions as $requisition) {
            Requisition::create($requisition);
        }
        
        // Create purchase orders
        $orders = [
            [
                'requisition_id' => 1,
                'vendor_id' => 1,
                'order_number' => 'PO202501001',
                'total_amount' => 15000.00,
                'status' => 'Issued',
                'order_date' => date('Y-m-d', strtotime('-3 days')),
                'expected_delivery_date' => date('Y-m-d', strtotime('+7 days')),
                'notes' => 'Urgent delivery required'
            ],
            [
                'requisition_id' => 3,
                'vendor_id' => 2,
                'order_number' => 'PO202501002',
                'total_amount' => 250.00,
                'status' => 'Completed',
                'order_date' => date('Y-m-d', strtotime('-2 days')),
                'expected_delivery_date' => date('Y-m-d', strtotime('-1 day')),
                'notes' => 'Standard delivery'
            ],
            [
                'requisition_id' => 5,
                'vendor_id' => 1,
                'order_number' => 'PO202501003',
                'total_amount' => 7500.00,
                'status' => 'Pending Approval',
                'order_date' => date('Y-m-d'),
                'expected_delivery_date' => date('Y-m-d', strtotime('+10 days')),
                'notes' => 'Waiting for budget approval'
            ]
        ];
        
        foreach ($orders as $order) {
            PurchaseOrder::create($order);
        }
        
        // Create goods receipts
        GoodsReceipt::create([
            'purchase_order_id' => 2,
            'receipt_number' => 'GR202501001',
            'received_date' => date('Y-m-d', strtotime('-1 day')),
            'quantity_received' => 100,
            'item' => 'Printer Paper A4 Premium',
            'condition' => 'Good',
            'received_by' => 'Warehouse Staff'
        ]);
        
        // Create invoices
        Invoice::create([
            'purchase_order_id' => 2,
            'invoice_number' => 'INV202501001',
            'amount' => 250.00,
            'status' => 'Paid',
            'invoice_date' => date('Y-m-d', strtotime('-1 day')),
            'due_date' => date('Y-m-d', strtotime('+29 days')),
            'paid_date' => date('Y-m-d'),
            'payment_method' => 'Bank Transfer'
        ]);
        
        Invoice::create([
            'purchase_order_id' => 1,
            'invoice_number' => 'INV202501002',
            'amount' => 15000.00,
            'status' => 'Pending',
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days'))
        ]);
    }
}
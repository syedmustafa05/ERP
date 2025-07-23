<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Requisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create sample vendors
        $vendors = [
            [
                'name' => 'TechSupply Corp',
                'contact' => 'John Doe',
                'email' => 'contact@techsupply.com',
                'phone' => '+1-555-0123'
            ],
            [
                'name' => 'OfficeMax Solutions',
                'contact' => 'Jane Smith',
                'email' => 'sales@officemax.com',
                'phone' => '+1-555-0456'
            ],
            [
                'name' => 'Global Suppliers Inc',
                'contact' => 'Mike Johnson',
                'email' => 'info@globalsuppliers.com',
                'phone' => '+1-555-0789'
            ]
        ];

        foreach ($vendors as $vendorData) {
            Vendor::create($vendorData);
        }

        // Create sample requisitions
        $requisitions = [
            [
                'item' => 'Laptops Dell XPS 13',
                'quantity' => 10,
                'status' => 'Approved',
                'requestedBy' => 'IT Department',
                'date' => now()->subDays(5)
            ],
            [
                'item' => 'Office Chairs',
                'quantity' => 25,
                'status' => 'Pending',
                'requestedBy' => 'HR Department',
                'date' => now()->subDays(3)
            ],
            [
                'item' => 'Printer Paper A4',
                'quantity' => 100,
                'status' => 'Approved',
                'requestedBy' => 'Admin Department',
                'date' => now()->subDays(2)
            ],
            [
                'item' => 'Network Switches',
                'quantity' => 5,
                'status' => 'Rejected',
                'requestedBy' => 'IT Department',
                'date' => now()->subDays(1)
            ]
        ];

        foreach ($requisitions as $reqData) {
            Requisition::create($reqData);
        }

        // Create sample purchase orders for approved requisitions
        $approvedRequisitions = Requisition::where('status', 'Approved')->get();
        foreach ($approvedRequisitions as $index => $requisition) {
            $vendor = Vendor::skip($index % 3)->first();
            PurchaseOrder::create([
                'requisition_id' => $requisition->id,
                'vendor_id' => $vendor->id,
                'total_amount' => rand(1000, 50000),
                'status' => ['Issued', 'Pending Approval', 'Completed'][rand(0, 2)],
                'order_date' => now()->subDays(rand(1, 4))
            ]);
        }

        // Create sample goods receipts for some purchase orders
        $completedOrders = PurchaseOrder::where('status', 'Completed')->take(2)->get();
        foreach ($completedOrders as $order) {
            GoodsReceipt::create([
                'purchase_order_id' => $order->id,
                'received_date' => now()->subDays(rand(1, 3)),
                'quantity_received' => $order->requisition->quantity,
                'item' => $order->requisition->item
            ]);
        }

        // Create sample invoices for completed purchase orders
        foreach ($completedOrders as $order) {
            Invoice::create([
                'purchase_order_id' => $order->id,
                'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'amount' => $order->total_amount,
                'status' => rand(0, 1) ? 'Paid' : 'Pending',
                'invoice_date' => now()->subDays(rand(1, 2))
            ]);
        }
    }
}

<?php

namespace App\Database\Migrations;

class CreatePurchaseOrdersTable
{
    public static function up($pdo)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS purchase_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                requisition_id INTEGER,
                vendor_id INTEGER,
                order_number TEXT,
                total_amount DECIMAL(10,2),
                status TEXT DEFAULT 'Pending Approval',
                order_date DATE,
                expected_delivery_date DATE,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (requisition_id) REFERENCES requisitions(id),
                FOREIGN KEY (vendor_id) REFERENCES vendors(id)
            )
        ";
        
        $pdo->exec($sql);
    }
    
    public static function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS purchase_orders");
    }
}
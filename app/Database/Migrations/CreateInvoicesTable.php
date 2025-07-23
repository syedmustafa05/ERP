<?php

namespace App\Database\Migrations;

class CreateInvoicesTable
{
    public static function up($pdo)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS invoices (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                purchase_order_id INTEGER,
                invoice_number TEXT,
                amount DECIMAL(10,2),
                status TEXT DEFAULT 'Pending',
                invoice_date DATE,
                due_date DATE,
                paid_date DATE,
                payment_method TEXT,
                reference_number TEXT,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
            )
        ";
        
        $pdo->exec($sql);
    }
    
    public static function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS invoices");
    }
}
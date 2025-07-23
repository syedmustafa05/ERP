<?php

namespace App\Database\Migrations;

class CreateGoodsReceiptsTable
{
    public static function up($pdo)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS goods_receipts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                purchase_order_id INTEGER,
                receipt_number TEXT,
                received_date DATE,
                quantity_received INTEGER,
                item TEXT,
                condition TEXT DEFAULT 'Good',
                notes TEXT,
                received_by TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
            )
        ";
        
        $pdo->exec($sql);
    }
    
    public static function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS goods_receipts");
    }
}
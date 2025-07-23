<?php

namespace App\Database\Migrations;

class CreateRequisitionsTable
{
    public static function up($pdo)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS requisitions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item TEXT NOT NULL,
                quantity INTEGER NOT NULL,
                status TEXT DEFAULT 'Pending',
                requestedBy TEXT,
                date DATE,
                description TEXT,
                estimated_cost DECIMAL(10,2),
                priority TEXT DEFAULT 'Medium',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $pdo->exec($sql);
    }
    
    public static function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS requisitions");
    }
}
<?php

namespace App\Database\Migrations;

/**
 * Create Users Table Migration
 */
class CreateUsersTable
{
    public static function up($pdo)
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email_verified_at DATETIME,
                remember_token TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        $pdo->exec($sql);
    }
    
    public static function down($pdo)
    {
        $pdo->exec("DROP TABLE IF EXISTS users");
    }
}
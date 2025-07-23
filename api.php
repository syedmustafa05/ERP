<?php

/**
 * ProcureEase API Entry Point
 * Laravel-style MVC Architecture
 * 
 * Features:
 * - Models with Eloquent-style methods
 * - Controllers with proper separation of concerns
 * - Migrations for database schema
 * - Seeders for sample data
 * - Route handling with proper REST structure
 */

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Bootstrap the application
require_once __DIR__ . '/bootstrap/app.php';

// Import routes and migrations
require_once __DIR__ . '/routes/api.php';

use App\Database\Migrations\CreateUsersTable;
use App\Database\Migrations\CreateVendorsTable;
use App\Database\Migrations\CreateRequisitionsTable;
use App\Database\Migrations\CreatePurchaseOrdersTable;
use App\Database\Migrations\CreateGoodsReceiptsTable;
use App\Database\Migrations\CreateInvoicesTable;
use App\Database\Seeders\DatabaseSeeder;

// Get database connection
$pdo = $GLOBALS['pdo'];

// Run migrations
try {
    CreateUsersTable::up($pdo);
    CreateVendorsTable::up($pdo);
    CreateRequisitionsTable::up($pdo);
    CreatePurchaseOrdersTable::up($pdo);
    CreateGoodsReceiptsTable::up($pdo);
    CreateInvoicesTable::up($pdo);
    
    // Check if database is empty and seed if needed
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        DatabaseSeeder::run();
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database setup failed: ' . $e->getMessage()]);
    exit;
}

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';
$parts = explode('/', trim($path, '/'));
$endpoint = $parts[0] ?? '';
$id = $parts[1] ?? null;

// Handle the request through routes
echo handleRequest($method, $endpoint, $id);
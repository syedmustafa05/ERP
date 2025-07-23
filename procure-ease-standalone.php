<?php
/**
 * ProcureEase - Complete Procurement Management System
 * Standalone Single-File Application
 * 
 * Features:
 * - Complete Laravel-style API backend
 * - Professional frontend interface
 * - SQLite database
 * - Bootstrap 5 UI
 * - Full CRUD operations for all entities
 */

// Configuration
define('DB_FILE', __DIR__ . '/procure_ease.db');
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

// Initialize database
function initDatabase() {
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS vendors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            contact TEXT,
            email TEXT,
            phone TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS requisitions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            item TEXT NOT NULL,
            quantity INTEGER NOT NULL,
            status TEXT DEFAULT 'Pending',
            requestedBy TEXT,
            date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            requisition_id INTEGER,
            vendor_id INTEGER,
            total_amount DECIMAL(10,2),
            status TEXT DEFAULT 'Pending Approval',
            order_date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (requisition_id) REFERENCES requisitions(id),
            FOREIGN KEY (vendor_id) REFERENCES vendors(id)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS goods_receipts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            purchase_order_id INTEGER,
            received_date DATE,
            quantity_received INTEGER,
            item TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS invoices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            purchase_order_id INTEGER,
            invoice_number TEXT,
            amount DECIMAL(10,2),
            status TEXT DEFAULT 'Pending',
            invoice_date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
        )
    ");
    
    // Insert sample data if tables are empty
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        seedData($pdo);
    }
    
    return $pdo;
}

function seedData($pdo) {
    // Insert test user
    $pdo->exec("INSERT INTO users (name, email, password) VALUES ('Test User', 'test@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "')");
    
    // Insert vendors
    $vendors = [
        ['TechSupply Corp', 'John Doe', 'contact@techsupply.com', '+1-555-0123'],
        ['OfficeMax Solutions', 'Jane Smith', 'sales@officemax.com', '+1-555-0456'],
        ['Global Suppliers Inc', 'Mike Johnson', 'info@globalsuppliers.com', '+1-555-0789']
    ];
    
    foreach ($vendors as $vendor) {
        $pdo->exec("INSERT INTO vendors (name, contact, email, phone) VALUES ('{$vendor[0]}', '{$vendor[1]}', '{$vendor[2]}', '{$vendor[3]}')");
    }
    
    // Insert requisitions
    $requisitions = [
        ['Laptops Dell XPS 13', 10, 'Approved', 'IT Department', date('Y-m-d', strtotime('-5 days'))],
        ['Office Chairs', 25, 'Pending', 'HR Department', date('Y-m-d', strtotime('-3 days'))],
        ['Printer Paper A4', 100, 'Approved', 'Admin Department', date('Y-m-d', strtotime('-2 days'))],
        ['Network Switches', 5, 'Rejected', 'IT Department', date('Y-m-d', strtotime('-1 day'))]
    ];
    
    foreach ($requisitions as $req) {
        $pdo->exec("INSERT INTO requisitions (item, quantity, status, requestedBy, date) VALUES ('{$req[0]}', {$req[1]}, '{$req[2]}', '{$req[3]}', '{$req[4]}')");
    }
    
    // Insert purchase orders
    $pdo->exec("INSERT INTO purchase_orders (requisition_id, vendor_id, total_amount, status, order_date) VALUES (1, 1, 15000.00, 'Issued', '" . date('Y-m-d', strtotime('-3 days')) . "')");
    $pdo->exec("INSERT INTO purchase_orders (requisition_id, vendor_id, total_amount, status, order_date) VALUES (3, 2, 2500.00, 'Completed', '" . date('Y-m-d', strtotime('-2 days')) . "')");
    
    // Insert goods receipts
    $pdo->exec("INSERT INTO goods_receipts (purchase_order_id, received_date, quantity_received, item) VALUES (2, '" . date('Y-m-d', strtotime('-1 day')) . "', 100, 'Printer Paper A4')");
    
    // Insert invoices
    $pdo->exec("INSERT INTO invoices (purchase_order_id, invoice_number, amount, status, invoice_date) VALUES (2, 'INV-000001', 2500.00, 'Paid', '" . date('Y-m-d') . "')");
}

// API Handler
function handleAPI() {
    $pdo = initDatabase();
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = str_replace($_SERVER['SCRIPT_NAME'], '', $path);
    $segments = explode('/', trim($path, '/'));
    
    if ($segments[0] !== 'api') {
        return false;
    }
    
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    $endpoint = $segments[1] ?? '';
    $id = $segments[2] ?? null;
    
    try {
        switch ($endpoint) {
            case 'dashboard':
                $data = [
                    'requisitions_count' => $pdo->query("SELECT COUNT(*) FROM requisitions")->fetchColumn(),
                    'vendors_count' => $pdo->query("SELECT COUNT(*) FROM vendors")->fetchColumn(),
                    'purchase_orders_count' => $pdo->query("SELECT COUNT(*) FROM purchase_orders")->fetchColumn(),
                    'goods_receipts_count' => $pdo->query("SELECT COUNT(*) FROM goods_receipts")->fetchColumn(),
                    'invoices_count' => $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn(),
                    'recent_requisitions' => $pdo->query("SELECT * FROM requisitions ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
                    'pending_purchase_orders' => $pdo->query("SELECT * FROM purchase_orders WHERE status = 'Pending Approval' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC)
                ];
                echo json_encode($data);
                break;
                
            case 'vendors':
                handleCRUD($pdo, 'vendors', $method, $id);
                break;
                
            case 'requisitions':
                handleCRUD($pdo, 'requisitions', $method, $id);
                break;
                
            case 'purchase-orders':
                handleCRUD($pdo, 'purchase_orders', $method, $id);
                break;
                
            case 'goods-receipts':
                handleCRUD($pdo, 'goods_receipts', $method, $id);
                break;
                
            case 'invoices':
                handleCRUD($pdo, 'invoices', $method, $id);
                break;
                
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    
    return true;
}

function handleCRUD($pdo, $table, $method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$data) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Record not found']);
                    return;
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM {$table} ORDER BY created_at DESC");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            echo json_encode($data);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $fields = array_keys($input);
            $values = array_values($input);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            
            $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            
            $newId = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
            $stmt->execute([$newId]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            break;
            
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required for update']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $fields = array_keys($input);
            $values = array_values($input);
            $values[] = $id;
            
            $setClause = implode(' = ?, ', $fields) . ' = ?';
            $sql = "UPDATE {$table} SET {$setClause} WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            break;
            
        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required for delete']);
                return;
            }
            
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['message' => 'Record deleted successfully']);
            break;
    }
}

// Check if this is an API request
if (handleAPI()) {
    exit;
}

// Frontend HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProcureEase - Procurement Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3B82F6;
            --secondary-color: #2DD4BF;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .content {
            padding: 2rem;
            flex: 1;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2563EB;
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: var(--dark-color);
        }

        /* Table */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
            background-color: #f8f9fa;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .modal {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: auto;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        /* Form */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .text-danger { color: var(--danger-color); }
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        .gap-2 { gap: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .me-2 { margin-right: 0.5rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 3000;
        }

        .toast {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-left: 4px solid var(--primary-color);
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left-color: var(--success-color);
        }

        .toast.error {
            border-left-color: var(--danger-color);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Loading spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .login-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Login Screen -->
    <div id="login-screen" class="login-container">
        <div class="login-card">
            <div class="text-center mb-3">
                <h2 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                    <i class="fas fa-shopping-cart me-2"></i>ProcureEase
                </h2>
                <p style="color: #6b7280;">Procurement Management System</p>
            </div>
            
            <form id="login-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" value="test@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" value="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Main Application -->
    <div id="main-app" class="app-container hidden">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="#" class="logo">
                    <i class="fas fa-shopping-cart"></i>
                    ProcureEase
                </a>
            </div>
            
            <div class="sidebar-nav">
                <div class="nav-item">
                    <a href="#" class="nav-link" data-page="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-page="requisitions">
                        <i class="fas fa-file-alt"></i>
                        Requisitions
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-page="vendors">
                        <i class="fas fa-building"></i>
                        Vendors
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-page="purchase-orders">
                        <i class="fas fa-shopping-bag"></i>
                        Purchase Orders
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-page="goods-receipts">
                        <i class="fas fa-boxes"></i>
                        Goods Receipts
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" data-page="invoices">
                        <i class="fas fa-file-invoice"></i>
                        Invoices
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <h1 class="header-title" id="page-title">Dashboard</h1>
                
                <div class="user-dropdown">
                    <div class="user-avatar">TU</div>
                    <div class="user-info">
                        <div class="user-name">Test User</div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down ms-2"></i>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <div id="page-content">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Modal -->
    <div class="modal-overlay" id="generic-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Modal Title</h3>
                <button class="modal-close">&times;</button>
            </div>
            <form id="modal-form">
                <div class="modal-body" id="modal-body">
                    <!-- Dynamic form content -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline modal-close">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ProcureEase Application
        class ProcureEaseApp {
            constructor() {
                this.apiBaseUrl = '<?php echo BASE_URL; ?>/api';
                this.currentUser = null;
                this.currentPage = 'dashboard';
                this.data = {};
                this.init();
            }

            init() {
                this.bindEvents();
                this.checkAuth();
            }

            bindEvents() {
                // Login form
                document.getElementById('login-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.login();
                });

                // Navigation
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const page = e.currentTarget.dataset.page;
                        this.loadPage(page);
                    });
                });

                // Modal events
                document.querySelectorAll('.modal-close').forEach(btn => {
                    btn.addEventListener('click', () => this.hideModal());
                });

                document.getElementById('modal-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleModalSubmit();
                });
            }

            async login() {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                // Simple mock authentication
                if (email === 'test@example.com' && password === 'password') {
                    this.currentUser = { name: 'Test User', email: email };
                    this.showApp();
                    this.loadPage('dashboard');
                } else {
                    this.showToast('Invalid credentials', 'error');
                }
            }

            checkAuth() {
                // For this demo, we'll just show the login screen
                this.showLogin();
            }

            showLogin() {
                document.getElementById('login-screen').classList.remove('hidden');
                document.getElementById('main-app').classList.add('hidden');
            }

            showApp() {
                document.getElementById('login-screen').classList.add('hidden');
                document.getElementById('main-app').classList.remove('hidden');
            }

            async loadPage(page) {
                this.currentPage = page;
                
                // Update navigation
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelector(`[data-page="${page}"]`).classList.add('active');
                
                // Update page title
                const titles = {
                    'dashboard': 'Dashboard',
                    'requisitions': 'Requisitions',
                    'vendors': 'Vendors',
                    'purchase-orders': 'Purchase Orders',
                    'goods-receipts': 'Goods Receipts',
                    'invoices': 'Invoices'
                };
                document.getElementById('page-title').textContent = titles[page];

                // Load page content
                const content = document.getElementById('page-content');
                content.innerHTML = '<div class="text-center"><div class="spinner"></div></div>';

                try {
                    switch (page) {
                        case 'dashboard':
                            await this.loadDashboard();
                            break;
                        case 'requisitions':
                            await this.loadRequisitions();
                            break;
                        case 'vendors':
                            await this.loadVendors();
                            break;
                        case 'purchase-orders':
                            await this.loadPurchaseOrders();
                            break;
                        case 'goods-receipts':
                            await this.loadGoodsReceipts();
                            break;
                        case 'invoices':
                            await this.loadInvoices();
                            break;
                    }
                } catch (error) {
                    content.innerHTML = `<div class="text-center text-danger">Error loading page: ${error.message}</div>`;
                }
            }

            async makeRequest(endpoint, options = {}) {
                const response = await fetch(`${this.apiBaseUrl}${endpoint}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...options.headers
                    },
                    ...options
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                return await response.json();
            }

            async loadDashboard() {
                const data = await this.makeRequest('/dashboard');
                
                const content = `
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: #dbeafe; color: #1e40af;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-value">${data.requisitions_count}</div>
                            <div class="stat-label">Total Requisitions</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: #dcfce7; color: #166534;">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="stat-value">${data.vendors_count}</div>
                            <div class="stat-label">Active Vendors</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: #fef3c7; color: #92400e;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="stat-value">${data.purchase_orders_count}</div>
                            <div class="stat-label">Purchase Orders</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon" style="background-color: #fee2e2; color: #991b1b;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="stat-value">${data.invoices_count}</div>
                            <div class="stat-label">Invoices</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Requisitions</h5>
                                </div>
                                <div class="card-body">
                                    ${data.recent_requisitions.length > 0 ? 
                                        data.recent_requisitions.map(req => `
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <strong>${req.item}</strong><br>
                                                    <small class="text-muted">Qty: ${req.quantity} | ${req.requestedBy}</small>
                                                </div>
                                                <span class="badge badge-${this.getStatusClass(req.status)}">${req.status}</span>
                                            </div>
                                        `).join('') : 
                                        '<p class="text-muted">No recent requisitions</p>'
                                    }
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Pending Purchase Orders</h5>
                                </div>
                                <div class="card-body">
                                    ${data.pending_purchase_orders.length > 0 ? 
                                        data.pending_purchase_orders.map(po => `
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <strong>PO #${po.id}</strong><br>
                                                    <small class="text-muted">Amount: $${parseFloat(po.total_amount).toLocaleString()}</small>
                                                </div>
                                                <span class="badge badge-warning">${po.status}</span>
                                            </div>
                                        `).join('') : 
                                        '<p class="text-muted">No pending purchase orders</p>'
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('page-content').innerHTML = content;
            }

            async loadRequisitions() {
                const requisitions = await this.makeRequest('/requisitions');
                this.data.requisitions = requisitions;

                const content = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Requisition Management</h3>
                            <button class="btn btn-primary" onclick="app.showAddModal('requisition')">
                                <i class="fas fa-plus me-2"></i>
                                Create Requisition
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                            <th>Requested By</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${requisitions.map(req => `
                                            <tr>
                                                <td>${req.item}</td>
                                                <td>${req.quantity}</td>
                                                <td><span class="badge badge-${this.getStatusClass(req.status)}">${req.status}</span></td>
                                                <td>${req.requestedBy}</td>
                                                <td>${this.formatDate(req.date)}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline me-2" onclick="app.showEditModal('requisition', ${req.id})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline text-danger" onclick="app.deleteRecord('requisitions', ${req.id})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('page-content').innerHTML = content;
            }

            async loadVendors() {
                const vendors = await this.makeRequest('/vendors');
                this.data.vendors = vendors;

                const content = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Vendor Management</h3>
                            <button class="btn btn-primary" onclick="app.showAddModal('vendor')">
                                <i class="fas fa-plus me-2"></i>
                                Add Vendor
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${vendors.map(vendor => `
                                            <tr>
                                                <td>${vendor.name}</td>
                                                <td>${vendor.contact || '-'}</td>
                                                <td>${vendor.email || '-'}</td>
                                                <td>${vendor.phone || '-'}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline me-2" onclick="app.showEditModal('vendor', ${vendor.id})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline text-danger" onclick="app.deleteRecord('vendors', ${vendor.id})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('page-content').innerHTML = content;
            }

            async loadPurchaseOrders() {
                const orders = await this.makeRequest('/purchase-orders');
                const requisitions = await this.makeRequest('/requisitions');
                const vendors = await this.makeRequest('/vendors');
                
                this.data.purchaseOrders = orders;
                this.data.requisitions = requisitions;
                this.data.vendors = vendors;

                const content = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Purchase Order Management</h3>
                            <button class="btn btn-primary" onclick="app.showAddModal('purchase-order')">
                                <i class="fas fa-plus me-2"></i>
                                Create Purchase Order
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>PO #</th>
                                            <th>Requisition</th>
                                            <th>Vendor</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Order Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${orders.map(order => {
                                            const req = requisitions.find(r => r.id == order.requisition_id);
                                            const vendor = vendors.find(v => v.id == order.vendor_id);
                                            return `
                                                <tr>
                                                    <td>PO-${order.id.toString().padStart(4, '0')}</td>
                                                    <td>${req ? req.item : 'Unknown'}</td>
                                                    <td>${vendor ? vendor.name : 'Unknown'}</td>
                                                    <td>$${parseFloat(order.total_amount).toLocaleString()}</td>
                                                    <td><span class="badge badge-${this.getStatusClass(order.status)}">${order.status}</span></td>
                                                    <td>${this.formatDate(order.order_date)}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline me-2" onclick="app.showEditModal('purchase-order', ${order.id})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline text-danger" onclick="app.deleteRecord('purchase-orders', ${order.id})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('page-content').innerHTML = content;
            }

            async loadGoodsReceipts() {
                const receipts = await this.makeRequest('/goods-receipts');
                const orders = await this.makeRequest('/purchase-orders');
                
                this.data.goodsReceipts = receipts;
                this.data.purchaseOrders = orders;

                const content = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Goods Receipt Management</h3>
                            <button class="btn btn-primary" onclick="app.showAddModal('goods-receipt')">
                                <i class="fas fa-plus me-2"></i>
                                Create Receipt
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Receipt #</th>
                                            <th>Purchase Order</th>
                                            <th>Item</th>
                                            <th>Quantity Received</th>
                                            <th>Received Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${receipts.map(receipt => {
                                            const order = orders.find(o => o.id == receipt.purchase_order_id);
                                            return `
                                                <tr>
                                                    <td>GR-${receipt.id.toString().padStart(4, '0')}</td>
                                                    <td>PO-${order ? order.id.toString().padStart(4, '0') : 'Unknown'}</td>
                                                    <td>${receipt.item}</td>
                                                    <td>${receipt.quantity_received}</td>
                                                    <td>${this.formatDate(receipt.received_date)}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline me-2" onclick="app.showEditModal('goods-receipt', ${receipt.id})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline text-danger" onclick="app.deleteRecord('goods-receipts', ${receipt.id})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('page-content').innerHTML = content;
            }

            async loadInvoices() {
                const invoices = await this.makeRequest('/invoices');
                const orders = await this.makeRequest('/purchase-orders');
                
                this.data.invoices = invoices;
                this.data.purchaseOrders = orders;

                const content = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Invoice Management</h3>
                            <button class="btn btn-primary" onclick="app.showAddModal('invoice')">
                                <i class="fas fa-plus me-2"></i>
                                Create Invoice
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Invoice Number</th>
                                            <th>Purchase Order</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Invoice Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${invoices.map(invoice => {
                                            const order = orders.find(o => o.id == invoice.purchase_order_id);
                                            return `
                                                <tr>
                                                    <td>${invoice.invoice_number}</td>
                                                    <td>PO-${order ? order.id.toString().padStart(4, '0') : 'Unknown'}</td>
                                                    <td>$${parseFloat(invoice.amount).toLocaleString()}</td>
                                                    <td><span class="badge badge-${this.getStatusClass(invoice.status)}">${invoice.status}</span></td>
                                                    <td>${this.formatDate(invoice.invoice_date)}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline me-2" onclick="app.showEditModal('invoice', ${invoice.id})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline text-danger" onclick="app.deleteRecord('invoices', ${invoice.id})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        ${invoice.status === 'Pending' ? `
                                                            <button class="btn btn-sm btn-secondary" onclick="app.markAsPaid(${invoice.id})">
                                                                <i class="fas fa-check"></i> Pay
                                                            </button>
                                                        ` : ''}
                                                    </td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('page-content').innerHTML = content;
            }

            showAddModal(type) {
                this.currentModalType = type;
                this.currentModalId = null;
                
                const titles = {
                    'requisition': 'Create Requisition',
                    'vendor': 'Add Vendor',
                    'purchase-order': 'Create Purchase Order',
                    'goods-receipt': 'Create Goods Receipt',
                    'invoice': 'Create Invoice'
                };
                
                document.getElementById('modal-title').textContent = titles[type];
                document.getElementById('modal-body').innerHTML = this.getModalForm(type);
                this.showModal();
            }

            showEditModal(type, id) {
                this.currentModalType = type;
                this.currentModalId = id;
                
                const titles = {
                    'requisition': 'Edit Requisition',
                    'vendor': 'Edit Vendor',
                    'purchase-order': 'Edit Purchase Order',
                    'goods-receipt': 'Edit Goods Receipt',
                    'invoice': 'Edit Invoice'
                };
                
                document.getElementById('modal-title').textContent = titles[type];
                document.getElementById('modal-body').innerHTML = this.getModalForm(type, id);
                this.showModal();
                
                // Populate form with existing data
                this.populateForm(type, id);
            }

            getModalForm(type, id = null) {
                switch (type) {
                    case 'requisition':
                        return `
                            <div class="form-group">
                                <label for="item" class="form-label">Item</label>
                                <input type="text" id="item" name="item" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="requestedBy" class="form-label">Requested By</label>
                                <input type="text" id="requestedBy" name="requestedBy" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" id="date" name="date" class="form-control" required>
                            </div>
                        `;
                        
                    case 'vendor':
                        return `
                            <div class="form-group">
                                <label for="name" class="form-label">Company Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="contact" class="form-label">Contact Person</label>
                                <input type="text" id="contact" name="contact" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control">
                            </div>
                        `;
                        
                    case 'purchase-order':
                        const requisitionOptions = (this.data.requisitions || [])
                            .filter(r => r.status === 'Approved')
                            .map(r => `<option value="${r.id}">${r.item} (Qty: ${r.quantity})</option>`)
                            .join('');
                        const vendorOptions = (this.data.vendors || [])
                            .map(v => `<option value="${v.id}">${v.name}</option>`)
                            .join('');
                            
                        return `
                            <div class="form-group">
                                <label for="requisition_id" class="form-label">Requisition</label>
                                <select id="requisition_id" name="requisition_id" class="form-control" required>
                                    <option value="">Select a requisition...</option>
                                    ${requisitionOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vendor_id" class="form-label">Vendor</label>
                                <select id="vendor_id" name="vendor_id" class="form-control" required>
                                    <option value="">Select a vendor...</option>
                                    ${vendorOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="total_amount" class="form-label">Total Amount ($)</label>
                                <input type="number" id="total_amount" name="total_amount" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="Pending Approval">Pending Approval</option>
                                    <option value="Issued">Issued</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="order_date" class="form-label">Order Date</label>
                                <input type="date" id="order_date" name="order_date" class="form-control" required>
                            </div>
                        `;
                        
                    case 'goods-receipt':
                        const orderOptions = (this.data.purchaseOrders || [])
                            .filter(o => ['Issued', 'Completed'].includes(o.status))
                            .map(o => `<option value="${o.id}">PO-${o.id.toString().padStart(4, '0')} - $${parseFloat(o.total_amount).toLocaleString()}</option>`)
                            .join('');
                            
                        return `
                            <div class="form-group">
                                <label for="purchase_order_id" class="form-label">Purchase Order</label>
                                <select id="purchase_order_id" name="purchase_order_id" class="form-control" required>
                                    <option value="">Select a purchase order...</option>
                                    ${orderOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="item" class="form-label">Item</label>
                                <input type="text" id="item" name="item" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity_received" class="form-label">Quantity Received</label>
                                <input type="number" id="quantity_received" name="quantity_received" class="form-control" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="received_date" class="form-label">Received Date</label>
                                <input type="date" id="received_date" name="received_date" class="form-control" required>
                            </div>
                        `;
                        
                    case 'invoice':
                        const invoiceOrderOptions = (this.data.purchaseOrders || [])
                            .filter(o => ['Issued', 'Completed'].includes(o.status))
                            .map(o => `<option value="${o.id}">PO-${o.id.toString().padStart(4, '0')} - $${parseFloat(o.total_amount).toLocaleString()}</option>`)
                            .join('');
                            
                        return `
                            <div class="form-group">
                                <label for="purchase_order_id" class="form-label">Purchase Order</label>
                                <select id="purchase_order_id" name="purchase_order_id" class="form-control" required>
                                    <option value="">Select a purchase order...</option>
                                    ${invoiceOrderOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" id="invoice_number" name="invoice_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="amount" class="form-label">Amount ($)</label>
                                <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" id="invoice_date" name="invoice_date" class="form-control" required>
                            </div>
                        `;
                }
            }

            populateForm(type, id) {
                let data;
                
                switch (type) {
                    case 'requisition':
                        data = this.data.requisitions.find(r => r.id == id);
                        break;
                    case 'vendor':
                        data = this.data.vendors.find(v => v.id == id);
                        break;
                    case 'purchase-order':
                        data = this.data.purchaseOrders.find(o => o.id == id);
                        break;
                    case 'goods-receipt':
                        data = this.data.goodsReceipts.find(r => r.id == id);
                        break;
                    case 'invoice':
                        data = this.data.invoices.find(i => i.id == id);
                        break;
                }
                
                if (data) {
                    Object.keys(data).forEach(key => {
                        const input = document.getElementById(key);
                        if (input) {
                            input.value = data[key] || '';
                        }
                    });
                }
            }

            async handleModalSubmit() {
                const form = document.getElementById('modal-form');
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                // Convert numeric fields
                if (data.quantity) data.quantity = parseInt(data.quantity);
                if (data.total_amount) data.total_amount = parseFloat(data.total_amount);
                if (data.amount) data.amount = parseFloat(data.amount);
                if (data.quantity_received) data.quantity_received = parseInt(data.quantity_received);
                if (data.requisition_id) data.requisition_id = parseInt(data.requisition_id);
                if (data.vendor_id) data.vendor_id = parseInt(data.vendor_id);
                if (data.purchase_order_id) data.purchase_order_id = parseInt(data.purchase_order_id);

                try {
                    const endpoint = this.getApiEndpoint(this.currentModalType);
                    
                    if (this.currentModalId) {
                        // Update
                        await this.makeRequest(`${endpoint}/${this.currentModalId}`, {
                            method: 'PUT',
                            body: JSON.stringify(data)
                        });
                        this.showToast('Record updated successfully!', 'success');
                    } else {
                        // Create
                        await this.makeRequest(endpoint, {
                            method: 'POST',
                            body: JSON.stringify(data)
                        });
                        this.showToast('Record created successfully!', 'success');
                    }
                    
                    this.hideModal();
                    this.loadPage(this.currentPage);
                } catch (error) {
                    this.showToast(`Error: ${error.message}`, 'error');
                }
            }

            async deleteRecord(endpoint, id) {
                if (!confirm('Are you sure you want to delete this record?')) {
                    return;
                }
                
                try {
                    await this.makeRequest(`/${endpoint}/${id}`, {
                        method: 'DELETE'
                    });
                    this.showToast('Record deleted successfully!', 'success');
                    this.loadPage(this.currentPage);
                } catch (error) {
                    this.showToast(`Error: ${error.message}`, 'error');
                }
            }

            async markAsPaid(invoiceId) {
                try {
                    await this.makeRequest(`/invoices/${invoiceId}`, {
                        method: 'PUT',
                        body: JSON.stringify({ status: 'Paid' })
                    });
                    this.showToast('Invoice marked as paid!', 'success');
                    this.loadPage('invoices');
                } catch (error) {
                    this.showToast(`Error: ${error.message}`, 'error');
                }
            }

            getApiEndpoint(type) {
                const endpoints = {
                    'requisition': '/requisitions',
                    'vendor': '/vendors',
                    'purchase-order': '/purchase-orders',
                    'goods-receipt': '/goods-receipts',
                    'invoice': '/invoices'
                };
                return endpoints[type];
            }

            showModal() {
                document.getElementById('generic-modal').style.display = 'flex';
            }

            hideModal() {
                document.getElementById('generic-modal').style.display = 'none';
            }

            showToast(message, type = 'info') {
                const toastContainer = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    ${message}
                `;
                
                toastContainer.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }

            getStatusClass(status) {
                const statusClasses = {
                    'Pending': 'warning',
                    'Approved': 'success',
                    'Rejected': 'danger',
                    'Issued': 'primary',
                    'Completed': 'success',
                    'Pending Approval': 'warning',
                    'Paid': 'success'
                };
                return statusClasses[status] || 'primary';
            }

            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString();
            }
        }

        // Initialize application
        const app = new ProcureEaseApp();
    </script>
</body>
</html>
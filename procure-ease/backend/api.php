<?php
/**
 * ProcureEase Backend API
 * Complete procurement management system backend
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database setup
$dbFile = __DIR__ . '/procure_ease.db';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Initialize database if it doesn't exist
if (!file_exists($dbFile)) {
    createDatabase($pdo);
    seedDatabase($pdo);
}

// Route handling
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';
$parts = explode('/', trim($path, '/'));
$endpoint = $parts[0] ?? '';
$id = $parts[1] ?? null;

try {
    switch ($endpoint) {
        case 'dashboard':
            echo json_encode(getDashboardData($pdo));
            break;
        case 'requisitions':
            handleCRUD($pdo, 'requisitions', $method, $id);
            break;
        case 'vendors':
            handleCRUD($pdo, 'vendors', $method, $id);
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

// Database creation function
function createDatabase($pdo) {
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
            address TEXT,
            is_active BOOLEAN DEFAULT 1,
            rating DECIMAL(2,1) DEFAULT 0,
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
            description TEXT,
            estimated_cost DECIMAL(10,2),
            priority TEXT DEFAULT 'Medium',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
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
            FOREIGN KEY (requisition_id) REFERENCES requisitions(id),
            FOREIGN KEY (vendor_id) REFERENCES vendors(id)
        )
    ");
    
    $pdo->exec("
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
            due_date DATE,
            paid_date DATE,
            payment_method TEXT,
            reference_number TEXT,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
        )
    ");
}

// Database seeding function
function seedDatabase($pdo) {
    // Insert test user
    $pdo->exec("INSERT INTO users (name, email, password) VALUES ('Test User', 'test@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "')");
    
    // Insert vendors
    $vendors = [
        ['TechSupply Corp', 'John Doe', 'contact@techsupply.com', '+1-555-0123', '123 Tech Street, Silicon Valley, CA', 1, 4.5],
        ['OfficeMax Solutions', 'Jane Smith', 'sales@officemax.com', '+1-555-0456', '456 Office Blvd, Business City, NY', 1, 4.2],
        ['Global Suppliers Inc', 'Mike Johnson', 'info@globalsuppliers.com', '+1-555-0789', '789 Supply Lane, Commerce Town, TX', 1, 4.8]
    ];
    
    foreach ($vendors as $vendor) {
        $pdo->exec("INSERT INTO vendors (name, contact, email, phone, address, is_active, rating) VALUES ('{$vendor[0]}', '{$vendor[1]}', '{$vendor[2]}', '{$vendor[3]}', '{$vendor[4]}', {$vendor[5]}, {$vendor[6]})");
    }
    
    // Insert requisitions
    $requisitions = [
        ['Laptops Dell XPS 13', 10, 'Approved', 'IT Department', date('Y-m-d', strtotime('-5 days')), 'High-performance laptops for development team', 15000.00, 'High'],
        ['Office Chairs Ergonomic', 25, 'Pending', 'HR Department', date('Y-m-d', strtotime('-3 days')), 'Ergonomic chairs for new office setup', 6250.00, 'Medium'],
        ['Printer Paper A4 Premium', 100, 'Approved', 'Admin Department', date('Y-m-d', strtotime('-2 days')), 'Premium quality paper for office printing', 250.00, 'Low'],
        ['Network Switches Cisco', 5, 'Rejected', 'IT Department', date('Y-m-d', strtotime('-1 day')), 'Network infrastructure upgrade', 2500.00, 'High'],
        ['Standing Desks', 15, 'Approved', 'HR Department', date('Y-m-d'), 'Height-adjustable desks for wellness program', 7500.00, 'Medium']
    ];
    
    foreach ($requisitions as $req) {
        $pdo->exec("INSERT INTO requisitions (item, quantity, status, requestedBy, date, description, estimated_cost, priority) VALUES ('{$req[0]}', {$req[1]}, '{$req[2]}', '{$req[3]}', '{$req[4]}', '{$req[5]}', {$req[6]}, '{$req[7]}')");
    }
    
    // Insert purchase orders
    $orders = [
        [1, 1, 'PO202501001', 15000.00, 'Issued', date('Y-m-d', strtotime('-3 days')), date('Y-m-d', strtotime('+7 days')), 'Urgent delivery required'],
        [3, 2, 'PO202501002', 250.00, 'Completed', date('Y-m-d', strtotime('-2 days')), date('Y-m-d', strtotime('-1 day')), 'Standard delivery'],
        [5, 1, 'PO202501003', 7500.00, 'Pending Approval', date('Y-m-d'), date('Y-m-d', strtotime('+10 days')), 'Waiting for budget approval']
    ];
    
    foreach ($orders as $order) {
        $pdo->exec("INSERT INTO purchase_orders (requisition_id, vendor_id, order_number, total_amount, status, order_date, expected_delivery_date, notes) VALUES ({$order[0]}, {$order[1]}, '{$order[2]}', {$order[3]}, '{$order[4]}', '{$order[5]}', '{$order[6]}', '{$order[7]}')");
    }
    
    // Insert goods receipts
    $pdo->exec("INSERT INTO goods_receipts (purchase_order_id, receipt_number, received_date, quantity_received, item, condition, received_by) VALUES (2, 'GR202501001', '" . date('Y-m-d', strtotime('-1 day')) . "', 100, 'Printer Paper A4 Premium', 'Good', 'Warehouse Staff')");
    
    // Insert invoices
    $pdo->exec("INSERT INTO invoices (purchase_order_id, invoice_number, amount, status, invoice_date, due_date) VALUES (2, 'INV202501001', 250.00, 'Paid', '" . date('Y-m-d', strtotime('-1 day')) . "', '" . date('Y-m-d', strtotime('+29 days')) . "')");
    $pdo->exec("INSERT INTO invoices (purchase_order_id, invoice_number, amount, status, invoice_date, due_date) VALUES (1, 'INV202501002', 15000.00, 'Pending', '" . date('Y-m-d') . "', '" . date('Y-m-d', strtotime('+30 days')) . "')");
}

// CRUD handler function
function handleCRUD($pdo, $table, $method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($data ?: ['error' => 'Record not found']);
            } else {
                $stmt = $pdo->query("SELECT * FROM {$table} ORDER BY created_at DESC");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON input']);
                return;
            }
            
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
            if (!$input) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON input']);
                return;
            }
            
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

// Dashboard data function
function getDashboardData($pdo) {
    return [
        'requisitions_count' => $pdo->query("SELECT COUNT(*) FROM requisitions")->fetchColumn(),
        'vendors_count' => $pdo->query("SELECT COUNT(*) FROM vendors")->fetchColumn(),
        'purchase_orders_count' => $pdo->query("SELECT COUNT(*) FROM purchase_orders")->fetchColumn(),
        'goods_receipts_count' => $pdo->query("SELECT COUNT(*) FROM goods_receipts")->fetchColumn(),
        'invoices_count' => $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn(),
        'total_orders_value' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM purchase_orders")->fetchColumn(),
        'pending_approvals' => $pdo->query("SELECT COUNT(*) FROM purchase_orders WHERE status = 'Pending Approval'")->fetchColumn(),
        'recent_requisitions' => $pdo->query("SELECT * FROM requisitions ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
        'pending_purchase_orders' => $pdo->query("SELECT * FROM purchase_orders WHERE status = 'Pending Approval' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC),
        'overdue_invoices' => $pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'Pending' AND due_date < date('now')")->fetchColumn()
    ];
}
?>
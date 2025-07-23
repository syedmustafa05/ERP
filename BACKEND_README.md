# ProcureEase Backend - Laravel-Style MVC Architecture

A professional, Laravel-inspired backend API with proper separation of concerns, following MVC patterns and best practices.

## 🏗 Architecture Overview

The backend follows Laravel conventions with proper MVC architecture:

```
backend/
├── app/
│   ├── Models/                     # Eloquent-style Models
│   │   ├── BaseModel.php          # Base model with CRUD operations
│   │   ├── User.php               # User model
│   │   ├── Vendor.php             # Vendor model
│   │   ├── Requisition.php        # Requisition model
│   │   ├── PurchaseOrder.php      # Purchase order model
│   │   ├── GoodsReceipt.php       # Goods receipt model
│   │   └── Invoice.php            # Invoice model
│   ├── Http/Controllers/          # Controllers
│   │   ├── BaseController.php     # Base controller functionality
│   │   ├── DashboardController.php # Dashboard statistics
│   │   ├── RequisitionController.php # Requisition CRUD
│   │   ├── VendorController.php   # Vendor CRUD
│   │   ├── PurchaseOrderController.php # Purchase order CRUD
│   │   ├── GoodsReceiptController.php # Goods receipt CRUD
│   │   └── InvoiceController.php  # Invoice CRUD
│   └── Database/
│       ├── Migrations/            # Database migrations
│       │   ├── CreateUsersTable.php
│       │   ├── CreateVendorsTable.php
│       │   ├── CreateRequisitionsTable.php
│       │   ├── CreatePurchaseOrdersTable.php
│       │   ├── CreateGoodsReceiptsTable.php
│       │   └── CreateInvoicesTable.php
│       └── Seeders/               # Database seeders
│           └── DatabaseSeeder.php # Sample data seeder
├── bootstrap/
│   └── app.php                    # Application bootstrap
├── routes/
│   └── api.php                    # API route definitions
├── storage/                       # Database and logs
└── api.php                        # Main API entry point
```

## 🎯 Key Features

### **Eloquent-Style Models**
- BaseModel with CRUD operations (find, create, update, delete)
- Model relationships and business logic
- Data validation and type casting
- Eloquent-style query methods (where, all, count)

### **Resource Controllers**
- Standard RESTful CRUD operations
- Proper error handling and validation
- JSON API responses with status codes
- Input sanitization and validation

### **Database Layer**
- Migration system for schema management
- Seeder system for sample data
- SQLite database with automatic setup
- Foreign key constraints and relationships

### **Professional Structure**
- Namespace organization (App\Models, App\Http\Controllers)
- Autoloading with PSR-4 conventions
- Separation of concerns (Models, Controllers, Routes)
- Consistent coding standards

## 📊 Models

### **BaseModel**
```php
// Eloquent-style methods
$users = User::all();
$user = User::find(1);
$user = User::create($data);
$users = User::where('status', 'active');
```

### **Model Features**
- **Fillable/Guarded** - Mass assignment protection
- **Casts** - Automatic type conversion
- **Timestamps** - Automatic created_at/updated_at
- **Relationships** - Model relationships
- **Scopes** - Query scopes for common filters
- **Business Logic** - Model-specific methods

### **Available Models**
- **User** - Authentication and user management
- **Vendor** - Supplier management with ratings
- **Requisition** - Purchase requests with approval workflow
- **PurchaseOrder** - Orders with auto-generated numbers
- **GoodsReceipt** - Delivery confirmations
- **Invoice** - Financial tracking with payment status

## 🎮 Controllers

### **RESTful API Controllers**
Each controller provides standard CRUD operations:

```php
// Standard methods
index()     // GET /api/resource - List all
show($id)   // GET /api/resource/{id} - Get specific
store()     // POST /api/resource - Create new
update($id) // PUT /api/resource/{id} - Update existing
destroy($id)// DELETE /api/resource/{id} - Delete
```

### **Controller Features**
- Input validation and sanitization
- Error handling with proper HTTP status codes
- JSON responses with consistent structure
- Business logic delegation to models

## 🛣 Routes

### **API Endpoints**

#### **Dashboard**
```
GET /api.php?path=dashboard
```

#### **Requisitions**
```
GET    /api.php?path=requisitions      # List all
GET    /api.php?path=requisitions/1    # Get specific
POST   /api.php?path=requisitions      # Create new
PUT    /api.php?path=requisitions/1    # Update
DELETE /api.php?path=requisitions/1    # Delete
```

#### **Vendors**
```
GET    /api.php?path=vendors           # List all
GET    /api.php?path=vendors/1         # Get specific
POST   /api.php?path=vendors           # Create new
PUT    /api.php?path=vendors/1         # Update
DELETE /api.php?path=vendors/1         # Delete
```

#### **Purchase Orders**
```
GET    /api.php?path=purchase-orders
GET    /api.php?path=purchase-orders/1
POST   /api.php?path=purchase-orders
PUT    /api.php?path=purchase-orders/1
DELETE /api.php?path=purchase-orders/1
```

#### **Goods Receipts**
```
GET    /api.php?path=goods-receipts
GET    /api.php?path=goods-receipts/1
POST   /api.php?path=goods-receipts
PUT    /api.php?path=goods-receipts/1
DELETE /api.php?path=goods-receipts/1
```

#### **Invoices**
```
GET    /api.php?path=invoices
GET    /api.php?path=invoices/1
POST   /api.php?path=invoices
PUT    /api.php?path=invoices/1
DELETE /api.php?path=invoices/1
PUT    /api.php?path=invoices/1?action=pay  # Mark as paid
```

## 💾 Database

### **Migrations**
Migrations handle database schema creation:

```php
// Create tables with proper structure
CreateUsersTable::up($pdo);
CreateVendorsTable::up($pdo);
// ... other migrations
```

### **Seeders**
Seeders populate the database with sample data:

```php
// Run all seeders
DatabaseSeeder::run();
```

### **Schema**
- **Foreign Key Constraints** - Proper relationships
- **Data Types** - Appropriate field types
- **Indexes** - Performance optimization
- **Timestamps** - Automatic tracking

## 🔧 Usage Examples

### **Model Usage**
```php
// Create new vendor
$vendor = Vendor::create([
    'name' => 'Tech Corp',
    'email' => 'contact@techcorp.com',
    'is_active' => true
]);

// Find and update
$vendor = Vendor::find(1);
$vendor->fill(['rating' => 4.5]);
$vendor->save();

// Query with conditions
$activeVendors = Vendor::where('is_active', true);
$pendingOrders = PurchaseOrder::pendingApproval();
```

### **Controller Usage**
```php
// In controller
public function store()
{
    $data = $this->getInput();
    $errors = $this->validate($data, [
        'name' => 'required',
        'email' => 'required'
    ]);
    
    if (!empty($errors)) {
        return $this->error('Validation failed', 422, $errors);
    }
    
    $vendor = Vendor::create($data);
    return $this->success($vendor, 'Created successfully');
}
```

## 🚀 Getting Started

### **1. Directory Structure**
The backend is already properly structured following Laravel conventions.

### **2. Auto-Setup**
- Database tables are created automatically
- Sample data is seeded on first run
- No manual setup required

### **3. API Testing**
Test endpoints using curl or Postman:

```bash
# Get dashboard data
curl http://localhost:8000/backend/api.php?path=dashboard

# Create new requisition
curl -X POST http://localhost:8000/backend/api.php?path=requisitions \
  -H "Content-Type: application/json" \
  -d '{"item":"Test Item","quantity":5,"requestedBy":"Test User","date":"2025-01-23"}'
```

## 🔒 Security Features

- **SQL Injection Protection** - Prepared statements
- **Input Validation** - Required field validation
- **Data Sanitization** - XSS prevention
- **Error Handling** - Graceful error responses
- **Type Casting** - Automatic data type conversion

## 📈 Performance

- **Efficient Queries** - Optimized database operations
- **Connection Reuse** - Single PDO instance
- **Lazy Loading** - Models load data when needed
- **Minimal Overhead** - Lightweight implementation

## 🎨 Code Standards

### **Naming Conventions**
- **Models** - PascalCase (User, PurchaseOrder)
- **Controllers** - PascalCase + Controller suffix
- **Methods** - camelCase (index, store, update)
- **Variables** - camelCase ($userData, $vendorId)

### **File Organization**
- One class per file
- Proper namespace usage
- PSR-4 autoloading
- Logical directory structure

### **Response Format**
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { /* response data */ }
}
```

## 🔧 Extending the Backend

### **Adding New Models**
1. Create model file in `app/Models/`
2. Extend BaseModel
3. Define fillable fields and relationships

### **Adding New Controllers**
1. Create controller in `app/Http/Controllers/`
2. Extend BaseController
3. Implement CRUD methods

### **Adding New Routes**
1. Add route handling in `routes/api.php`
2. Map to appropriate controller method

### **Adding Migrations**
1. Create migration in `app/Database/Migrations/`
2. Implement up() and down() methods
3. Add to api.php migration runner

## 📊 Comparison: Before vs After

### **Before (Single File)**
- All code in one large file
- Mixed concerns (database, routing, logic)
- Hard to maintain and extend
- No separation of responsibilities

### **After (Laravel-Style MVC)**
- Proper separation of concerns
- Models handle data logic
- Controllers handle HTTP requests
- Easy to maintain and extend
- Professional development patterns

## 🎯 Benefits

- **Maintainability** - Clean code organization
- **Scalability** - Easy to add new features
- **Testability** - Isolated components
- **Reusability** - Shared base classes
- **Professional** - Industry-standard patterns

---

**ProcureEase Backend** - Professional Laravel-inspired MVC architecture for enterprise-grade procurement management.
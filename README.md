# ProcureEase - Professional Procurement Management System

A modern, full-featured procurement management system built with Laravel following professional development standards and best practices.

## 🚀 Features

### Core Functionality
- **Requisition Management** - Complete request lifecycle with approval workflows
- **Vendor Management** - Comprehensive supplier database with performance tracking
- **Purchase Order Management** - Automated order generation and status tracking
- **Goods Receipt Management** - Efficient receiving and inventory tracking
- **Invoice Processing** - Automated matching and payment processing
- **Dashboard Analytics** - Real-time insights and procurement metrics

### Technical Features
- **RESTful API** - Complete API coverage for all modules
- **Professional UI** - Modern, responsive Bootstrap 5 interface
- **Laravel Best Practices** - Following Laravel conventions and standards
- **Model Relationships** - Proper Eloquent relationships and constraints
- **Request Validation** - Comprehensive form and API validation
- **Resource Classes** - Structured API responses
- **Query Scopes** - Reusable query methods
- **Factory & Seeders** - Test data generation

## 🏗️ Architecture

### Backend (Laravel)
```
app/
├── Http/
│   ├── Controllers/          # Web Controllers
│   │   ├── DashboardController.php
│   │   ├── RequisitionController.php
│   │   ├── VendorController.php
│   │   ├── PurchaseOrderController.php
│   │   ├── GoodsReceiptController.php
│   │   └── InvoiceController.php
│   ├── Controllers/Api/      # API Controllers
│   ├── Requests/            # Form Request Classes
│   ├── Resources/           # API Resource Classes
│   └── Middleware/          # Custom Middleware
├── Models/                  # Eloquent Models
│   ├── User.php
│   ├── Requisition.php
│   ├── Vendor.php
│   ├── PurchaseOrder.php
│   ├── GoodsReceipt.php
│   └── Invoice.php
└── Providers/              # Service Providers
```

### Database Schema
```sql
-- Core procurement entities with proper relationships
requisitions -> purchase_orders -> goods_receipts
                     ↓
vendors -> purchase_orders -> invoices
```

### Frontend Structure
- **Landing Page** - Professional welcome page with feature overview
- **Dashboard** - Analytics and key metrics
- **Module Pages** - Dedicated interfaces for each procurement module
- **API Integration** - JSON API communication
- **Responsive Design** - Mobile-first Bootstrap 5 implementation

## 📋 Requirements

- **PHP** 8.2+
- **Composer** 2.0+
- **Laravel** 11.0+
- **SQLite** (default) or MySQL/PostgreSQL
- **Node.js** 18+ (for asset compilation)

## 🛠️ Installation

### 1. Clone Repository
```bash
git clone <repository-url>
cd procure-ease
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed
```

### 5. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 🔗 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Core Endpoints

#### Dashboard & Analytics
```http
GET /dashboard              # Dashboard summary
GET /analytics/overview     # Analytics overview
GET /analytics/trends       # Trend analysis
```

#### Requisitions
```http
GET    /requisitions        # List all requisitions
POST   /requisitions        # Create requisition
GET    /requisitions/{id}   # Get specific requisition
PUT    /requisitions/{id}   # Update requisition
DELETE /requisitions/{id}   # Delete requisition
PATCH  /requisitions/{id}/approve  # Approve requisition
PATCH  /requisitions/{id}/reject   # Reject requisition
```

#### Vendors
```http
GET    /vendors             # List all vendors
POST   /vendors             # Create vendor
GET    /vendors/{id}        # Get specific vendor
PUT    /vendors/{id}        # Update vendor
DELETE /vendors/{id}        # Delete vendor
PATCH  /vendors/{id}/activate      # Activate vendor
PATCH  /vendors/{id}/deactivate    # Deactivate vendor
GET    /vendors/{id}/purchase-orders  # Vendor's purchase orders
GET    /vendors/{id}/performance      # Vendor performance metrics
```

#### Purchase Orders
```http
GET    /purchase-orders     # List all purchase orders
POST   /purchase-orders     # Create purchase order
GET    /purchase-orders/{id}  # Get specific purchase order
PUT    /purchase-orders/{id}  # Update purchase order
DELETE /purchase-orders/{id}  # Delete purchase order
PATCH  /purchase-orders/{id}/approve   # Approve order
PATCH  /purchase-orders/{id}/issue     # Issue order
PATCH  /purchase-orders/{id}/complete  # Complete order
PATCH  /purchase-orders/{id}/cancel    # Cancel order
```

#### Goods Receipts
```http
GET    /goods-receipts      # List all goods receipts
POST   /goods-receipts      # Create goods receipt
GET    /goods-receipts/{id} # Get specific goods receipt
PUT    /goods-receipts/{id} # Update goods receipt
DELETE /goods-receipts/{id} # Delete goods receipt
```

#### Invoices
```http
GET    /invoices            # List all invoices
POST   /invoices            # Create invoice
GET    /invoices/{id}       # Get specific invoice
PUT    /invoices/{id}       # Update invoice
DELETE /invoices/{id}       # Delete invoice
PATCH  /invoices/{id}/mark-paid  # Mark invoice as paid
PATCH  /invoices/{id}/cancel     # Cancel invoice
```

### Response Format
```json
{
    "data": {
        "id": 1,
        "attribute": "value",
        // ... other attributes
    },
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

## 🗃️ Database Schema

### Key Models & Relationships

#### Requisition
- **Purpose**: Purchase requests from departments
- **Fields**: item, quantity, status, requestedBy, date, priority
- **Relationships**: hasMany(PurchaseOrder)

#### Vendor
- **Purpose**: Supplier information and management
- **Fields**: name, contact, email, phone, address, rating
- **Relationships**: hasMany(PurchaseOrder)

#### PurchaseOrder
- **Purpose**: Formal orders issued to vendors
- **Fields**: order_number, total_amount, status, order_date
- **Relationships**: belongsTo(Requisition, Vendor), hasMany(GoodsReceipt, Invoice)

#### GoodsReceipt
- **Purpose**: Record of received items
- **Fields**: receipt_number, received_date, quantity_received
- **Relationships**: belongsTo(PurchaseOrder)

#### Invoice
- **Purpose**: Payment requests from vendors
- **Fields**: invoice_number, amount, status, due_date
- **Relationships**: belongsTo(PurchaseOrder)

## 🎨 Design System

### Color Palette
- **Primary**: #3B82F6 (Blue)
- **Secondary**: #2DD4BF (Teal)
- **Success**: #10B981 (Green)
- **Warning**: #F59E0B (Amber)
- **Danger**: #EF4444 (Red)

### Typography
- **Font Family**: Inter (Google Fonts)
- **Weights**: 400, 500, 600, 700

### Components
- **Bootstrap 5** base framework
- **FontAwesome 6** icons
- **Custom CSS** variables and utilities

## 🧪 Testing

### Run Tests
```bash
php artisan test
```

### Database Testing
```bash
php artisan migrate:fresh --env=testing
php artisan test --env=testing
```

## 📦 Deployment

### Production Setup
1. **Environment Configuration**
```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql  # or postgresql
```

2. **Cache Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Asset Compilation**
```bash
npm run production
```

### Docker Deployment
```dockerfile
# Dockerfile included for containerized deployment
# See docker-compose.yml for full stack setup
```

## 🚦 Performance

### Optimization Features
- **Query Optimization**: Eager loading relationships
- **Caching**: Route, config, and view caching
- **Pagination**: Efficient data loading
- **Indexing**: Database indexes on key fields
- **API Resources**: Structured JSON responses

### Monitoring
- **Laravel Telescope** (dev)
- **Database Query Logging**
- **Performance Metrics**

## 🔐 Security

### Security Features
- **CSRF Protection**: Laravel's built-in CSRF
- **SQL Injection**: Eloquent ORM protection
- **Input Validation**: Request validation classes
- **Authentication**: Laravel Sanctum ready
- **Authorization**: Policy-based permissions

## 🤝 Contributing

### Development Guidelines
1. Follow PSR-12 coding standards
2. Write comprehensive tests
3. Use Laravel conventions
4. Document API changes
5. Update CHANGELOG.md

### Code Style
```bash
# Run Laravel Pint for code formatting
./vendor/bin/pint
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [API Documentation](docs/api.md)
- [Deployment Guide](docs/deployment.md)

### Issues
For bug reports and feature requests, please use the GitHub issues tracker.

---

**ProcureEase** - Professional Procurement Management Made Simple

Built with ❤️ using Laravel {{ app()->version() }} and modern web technologies.
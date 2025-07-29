# ProcureEase - Complete ERP Procurement Management System

A full-stack Enterprise Resource Planning (ERP) system focused on procurement management, built with Laravel backend and vanilla JavaScript frontend.

## 🚀 Features

### Backend (Laravel)
- **RESTful API** with full CRUD operations
- **Database Models**: Requisitions, Vendors, Purchase Orders, Goods Receipts, Invoices
- **SQLite Database** for easy setup and portability
- **Eloquent Relationships** between all entities
- **Input Validation** and error handling
- **JSON API responses** with consistent structure

### Frontend (Vanilla JavaScript + Bootstrap 5)
- **Modern UI Design** with professional theme
- **Responsive Layout** that works on all devices
- **Authentication System** with login/logout functionality
- **Interactive Dashboard** with real-time statistics
- **CRUD Operations** for all entities
- **Custom Styling** with Inter font and professional color scheme

## 📁 Project Structure

```
/workspace/
├── procure-ease-laravel/          # Laravel Backend
│   ├── app/
│   │   ├── Models/                # Eloquent Models
│   │   └── Http/Controllers/API/  # API Controllers
│   ├── database/
│   │   └── migrations/            # Database Migrations
│   ├── routes/
│   │   └── api.php               # API Routes
│   └── ...
└── procure-ease-frontend/         # Frontend
    ├── css/
    │   └── style.css             # Custom Styling
    ├── js/
    │   ├── auth.js               # Authentication Logic
    │   └── dashboard.js          # Dashboard Functionality
    ├── login.html                # Login Page
    ├── dashboard.html            # Main Dashboard
    └── ...
```

## 🛠 Installation & Setup

### Prerequisites
- PHP 8.4+
- Composer
- SQLite support for PHP
- Python 3 (for frontend server)

### Backend Setup

1. **Navigate to Laravel directory:**
   ```bash
   cd /workspace/procure-ease-laravel
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

5. **Start Laravel server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

### Frontend Setup

1. **Navigate to frontend directory:**
   ```bash
   cd /workspace/procure-ease-frontend
   ```

2. **Start HTTP server:**
   ```bash
   python3 -m http.server 3000
   ```

## 🎯 Usage

### Accessing the Application

1. **Frontend**: Open `http://localhost:3000/login.html`
2. **Backend API**: Available at `http://localhost:8000/api`

### Demo Credentials

```
Email: admin@procureease.com
Password: admin123
```

### API Endpoints

#### Requisitions
- `GET /api/requisitions` - List all requisitions
- `POST /api/requisitions` - Create new requisition
- `GET /api/requisitions/{id}` - Get specific requisition
- `PUT /api/requisitions/{id}` - Update requisition
- `DELETE /api/requisitions/{id}` - Delete requisition

#### Vendors
- `GET /api/vendors` - List all vendors
- `POST /api/vendors` - Create new vendor
- `GET /api/vendors/{id}` - Get specific vendor
- `PUT /api/vendors/{id}` - Update vendor
- `DELETE /api/vendors/{id}` - Delete vendor

#### Purchase Orders
- `GET /api/purchase-orders` - List all purchase orders
- `POST /api/purchase-orders` - Create new purchase order
- `GET /api/purchase-orders/{id}` - Get specific purchase order
- `PUT /api/purchase-orders/{id}` - Update purchase order
- `DELETE /api/purchase-orders/{id}` - Delete purchase order

#### Goods Receipts
- `GET /api/goods-receipts` - List all goods receipts
- `POST /api/goods-receipts` - Create new goods receipt
- `GET /api/goods-receipts/{id}` - Get specific goods receipt
- `PUT /api/goods-receipts/{id}` - Update goods receipt
- `DELETE /api/goods-receipts/{id}` - Delete goods receipt

#### Invoices
- `GET /api/invoices` - List all invoices
- `POST /api/invoices` - Create new invoice
- `GET /api/invoices/{id}` - Get specific invoice
- `PUT /api/invoices/{id}` - Update invoice
- `DELETE /api/invoices/{id}` - Delete invoice

#### Dashboard Stats
- `GET /api/dashboard/stats` - Get dashboard statistics

## 📊 Database Schema

### Requisitions
- `id` (Primary Key)
- `item` (String)
- `quantity` (Integer)
- `status` (Enum: Approved, Pending, Rejected)
- `requestedBy` (String)
- `date` (Date)
- `timestamps`

### Vendors
- `id` (Primary Key)
- `name` (String)
- `contact` (String)
- `email` (String, Unique)
- `phone` (String)
- `timestamps`

### Purchase Orders
- `id` (Primary Key)
- `requisition_id` (Foreign Key)
- `vendor_id` (Foreign Key)
- `total_amount` (Decimal)
- `status` (Enum: Issued, Pending Approval, Completed, Cancelled)
- `order_date` (Date)
- `timestamps`

### Goods Receipts
- `id` (Primary Key)
- `purchase_order_id` (Foreign Key)
- `received_date` (Date)
- `quantity_received` (Integer)
- `item` (String)
- `timestamps`

### Invoices
- `id` (Primary Key)
- `purchase_order_id` (Foreign Key)
- `invoice_number` (String, Unique)
- `amount` (Decimal)
- `status` (Enum: Paid, Pending)
- `invoice_date` (Date)
- `timestamps`

## 🎨 UI/UX Features

### Design System
- **Primary Color**: #3B82F6 (Professional Blue)
- **Accent Color**: #2DD4BF (Teal)
- **Typography**: Inter font family
- **Components**: Bootstrap 5 with custom overrides

### Key UI Elements
- **Responsive Sidebar** with collapsible navigation
- **Dashboard Stats Cards** with animated counters
- **Data Tables** with hover effects and status badges
- **Modal Forms** for CRUD operations
- **Toast Notifications** for user feedback
- **Loading States** and skeleton screens

### User Experience
- **Auto-filled Demo Credentials** for easy testing
- **Client-side Authentication** with local storage
- **Responsive Design** for mobile and desktop
- **Keyboard Shortcuts** for power users
- **Auto-refresh** dashboard every 5 minutes

## 🔧 Technical Features

### Backend
- **Laravel 12** with latest PHP 8.4
- **SQLite Database** for portability
- **Eloquent ORM** with relationships
- **API Resource Controllers** with consistent responses
- **Input Validation** with custom rules
- **CORS Enabled** for frontend integration

### Frontend
- **Vanilla JavaScript ES6+** for maximum compatibility
- **Bootstrap 5** for responsive design
- **Modular Architecture** with reusable components
- **API Wrapper** for consistent HTTP requests
- **Authentication Manager** with token handling
- **Utility Functions** for common operations

## 🔒 Security Features

- **Input Validation** on all API endpoints
- **SQL Injection Protection** via Eloquent ORM
- **CSRF Protection** (when using Laravel session auth)
- **XSS Protection** through proper data sanitization
- **Authentication Required** for protected routes

## 🧪 Testing

### API Testing
```bash
# Test requisitions endpoint
curl -X GET http://localhost:8000/api/requisitions

# Create a new requisition
curl -X POST http://localhost:8000/api/requisitions \
  -H "Content-Type: application/json" \
  -d '{"item":"Test Item","quantity":5,"requestedBy":"John Doe","date":"2024-01-15"}'
```

### Frontend Testing
1. Open the application in browser
2. Login with demo credentials
3. Navigate through different sections
4. Test CRUD operations
5. Verify responsive design on mobile

## 📈 Future Enhancements

- **User Roles & Permissions** system
- **Email Notifications** for workflow steps
- **PDF Report Generation** for orders and invoices
- **Advanced Search & Filtering** capabilities
- **Audit Trail** for all operations
- **Integration** with external accounting systems
- **Barcode Scanning** for goods receipt
- **Mobile App** for field operations

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the API endpoints
- Test with the demo credentials

---

**Built with ❤️ using Laravel & Bootstrap 5**
# ProcureEase - Complete Multi-Page Procurement Management System

A professional, multi-page procurement management application with separate backend API and frontend interface, built with PHP, SQLite, and modern web technologies.

## 🚀 Features

- **Complete Backend API** - RESTful PHP API with SQLite database
- **Multi-Page Frontend** - Separate HTML pages for each module
- **Professional UI** - Modern responsive design with Bootstrap 5
- **Real-time Data** - Dynamic loading with JavaScript
- **Complete Procurement Workflow** - From requisitions to invoices

## 📋 Project Structure

```
procure-ease/
├── backend/
│   └── api.php                 # Complete RESTful API
├── frontend/
│   ├── index.html              # Dashboard (Main page)
│   ├── css/
│   │   └── style.css           # Main stylesheet
│   ├── js/
│   │   └── app.js              # JavaScript application logic
│   └── pages/
│       ├── requisitions.html   # Requisitions management
│       ├── vendors.html        # Vendor management
│       ├── purchase-orders.html # Purchase orders
│       ├── goods-receipts.html # Goods receipts
│       └── invoices.html       # Invoice management
└── README.md
```

## 📱 Application Pages

### **Dashboard (`index.html`)**
- Overview with key metrics and statistics
- Recent requisitions display
- Real-time data cards
- Navigation hub to all modules

### **Requisitions (`pages/requisitions.html`)**
- Create, edit, and manage purchase requisitions
- Status tracking (Pending, Approved, Rejected)
- Priority levels and approval workflows
- Detailed item specifications

### **Vendors (`pages/vendors.html`)**
- Vendor database management
- Contact information and ratings
- Active/inactive status tracking
- Performance metrics

### **Purchase Orders (`pages/purchase-orders.html`)**
- Convert requisitions to purchase orders
- Vendor assignment and order tracking
- Amount and delivery date management
- Status monitoring

### **Goods Receipts (`pages/goods-receipts.html`)**
- Record incoming deliveries
- Quality control and condition tracking
- Quantity verification
- Receipt documentation

### **Invoices (`pages/invoices.html`)**
- Invoice creation and management
- Payment tracking and status
- Due date monitoring
- Payment method recording

## 🛠 Installation & Setup

### Prerequisites
- **PHP 7.4+** with PDO SQLite extension
- **Web Server** (Apache, Nginx, or PHP built-in server)
- **Modern Browser** (Chrome, Firefox, Safari, Edge)

### Quick Start

1. **Clone/Download the project**
   ```bash
   # Download or clone to your desired location
   cd /path/to/procure-ease
   ```

2. **Start the PHP Server**
   ```bash
   # Navigate to frontend directory
   cd frontend
   
   # Start PHP built-in server
   php -S localhost:8000
   ```

3. **Access the Application**
   - Open browser to `http://localhost:8000`
   - Start with the Dashboard
   - Navigate between pages using the sidebar

### Database Setup
- **Automatic Creation**: Database and tables are created automatically on first API call
- **Sample Data**: Pre-loaded with realistic sample data
- **Location**: `backend/procure_ease.db` (SQLite file)

## 🔌 API Endpoints

The backend API provides complete RESTful endpoints:

### Dashboard
- `GET /backend/api.php?path=dashboard` - Get dashboard statistics

### Requisitions
- `GET /backend/api.php?path=requisitions` - List all requisitions
- `GET /backend/api.php?path=requisitions/{id}` - Get specific requisition
- `POST /backend/api.php?path=requisitions` - Create new requisition
- `PUT /backend/api.php?path=requisitions/{id}` - Update requisition
- `DELETE /backend/api.php?path=requisitions/{id}` - Delete requisition

### Vendors
- `GET /backend/api.php?path=vendors` - List all vendors
- `GET /backend/api.php?path=vendors/{id}` - Get specific vendor
- `POST /backend/api.php?path=vendors` - Create new vendor
- `PUT /backend/api.php?path=vendors/{id}` - Update vendor
- `DELETE /backend/api.php?path=vendors/{id}` - Delete vendor

### Purchase Orders
- `GET /backend/api.php?path=purchase-orders` - List all purchase orders
- `GET /backend/api.php?path=purchase-orders/{id}` - Get specific order
- `POST /backend/api.php?path=purchase-orders` - Create new order
- `PUT /backend/api.php?path=purchase-orders/{id}` - Update order
- `DELETE /backend/api.php?path=purchase-orders/{id}` - Delete order

### Goods Receipts
- `GET /backend/api.php?path=goods-receipts` - List all receipts
- `GET /backend/api.php?path=goods-receipts/{id}` - Get specific receipt
- `POST /backend/api.php?path=goods-receipts` - Create new receipt
- `PUT /backend/api.php?path=goods-receipts/{id}` - Update receipt
- `DELETE /backend/api.php?path=goods-receipts/{id}` - Delete receipt

### Invoices
- `GET /backend/api.php?path=invoices` - List all invoices
- `GET /backend/api.php?path=invoices/{id}` - Get specific invoice
- `POST /backend/api.php?path=invoices` - Create new invoice
- `PUT /backend/api.php?path=invoices/{id}` - Update invoice
- `DELETE /backend/api.php?path=invoices/{id}` - Delete invoice

## 💻 Usage Guide

### Navigation
- **Sidebar Navigation**: Click on any module to switch pages
- **Active States**: Current page highlighted in sidebar
- **Responsive Design**: Works on desktop, tablet, and mobile

### Data Management
- **Create Records**: Click "+" buttons to add new items
- **Edit Records**: Click edit (pencil) icons in tables
- **Delete Records**: Click delete (trash) icons with confirmation
- **Real-time Updates**: Changes reflected immediately

### Special Features
- **Invoice Payment**: Mark invoices as paid with one click
- **Status Badges**: Color-coded status indicators
- **Priority Levels**: Visual priority indicators
- **Date Formatting**: Automatic date formatting
- **Currency Display**: Proper currency formatting

## 🎨 Customization

### Themes & Colors
Modify CSS variables in `frontend/css/style.css`:
```css
:root {
    --primary-color: #3B82F6;
    --secondary-color: #2DD4BF;
    --success-color: #10B981;
    /* Customize colors here */
}
```

### API Extensions
Add new endpoints in `backend/api.php`:
```php
case 'my-endpoint':
    echo json_encode(myCustomFunction($pdo));
    break;
```

### Frontend Features
Extend functionality in `frontend/js/app.js`:
```javascript
class ProcureEaseApp {
    // Add custom methods here
}
```

## 📊 Database Schema

### Tables
- **users** - User accounts and authentication
- **vendors** - Supplier information and ratings
- **requisitions** - Purchase requests and approvals
- **purchase_orders** - Orders with vendor assignments
- **goods_receipts** - Delivery confirmations
- **invoices** - Financial records and payments

### Relationships
- Purchase Orders → Requisitions (many-to-one)
- Purchase Orders → Vendors (many-to-one)
- Goods Receipts → Purchase Orders (many-to-one)
- Invoices → Purchase Orders (many-to-one)

## 🔒 Security Features

- **SQL Injection Protection** - Prepared statements
- **XSS Prevention** - Output escaping
- **Input Validation** - Client and server-side validation
- **Error Handling** - Graceful error responses
- **CORS Headers** - Proper cross-origin handling

## 📈 Performance

- **Optimized Queries** - Efficient database operations
- **Client-side Caching** - Reduced server requests
- **Lazy Loading** - Pages load on demand
- **Responsive Images** - Adaptive media loading
- **Minified Assets** - Optimized CSS and JavaScript

## 🐛 Troubleshooting

### Common Issues

**Database Errors**
- Ensure PHP PDO SQLite extension is installed
- Check file permissions for database creation
- Verify web server has write access to backend directory

**API Connection Issues**
- Check if PHP server is running on correct port
- Verify API endpoint URLs in JavaScript
- Check browser console for JavaScript errors

**Page Loading Issues**
- Ensure all HTML files are in correct directories
- Check CSS and JavaScript file paths
- Verify server is serving static files correctly

## 🚀 Deployment

### Development
1. Use PHP built-in server for development
2. Access via `http://localhost:8000`
3. All changes reflected immediately

### Production
1. **Upload Files**: Transfer all files to web server
2. **Set Permissions**: Ensure write access for database
3. **Configure Server**: Set up virtual host if needed
4. **SSL Certificate**: Enable HTTPS for security
5. **Backup Strategy**: Regular database backups

### Environment Configuration
For production, consider environment variables:
```php
$config = [
    'db_path' => $_ENV['DB_PATH'] ?? __DIR__ . '/procure_ease.db',
    'debug' => $_ENV['DEBUG'] ?? false
];
```

## 📄 Sample Data

The application comes pre-loaded with realistic sample data:
- **3 Vendors** with contact information and ratings
- **5 Requisitions** with different statuses and priorities
- **3 Purchase Orders** in various stages
- **1 Goods Receipt** and **2 Invoices**

## 🎯 Use Cases

- **Small to Medium Businesses** - Complete procurement solution
- **Department Procurement** - Manage department-specific purchases
- **Project-based Procurement** - Track project-related purchases
- **Educational Institutions** - Manage institutional procurement
- **Non-profit Organizations** - Budget-conscious procurement management

## 🔧 Technical Details

### Frontend Architecture
- **Multi-page Application** - Traditional page-based structure
- **Progressive Enhancement** - Works without JavaScript
- **Responsive Design** - Mobile-first approach
- **Modern CSS** - Flexbox and Grid layouts

### Backend Architecture
- **RESTful API** - Standard HTTP methods
- **Database Layer** - PDO with prepared statements
- **Error Handling** - Comprehensive error responses
- **Routing System** - Clean URL structure

## 📞 Support

For support and questions:
- Review the troubleshooting section
- Check browser console for errors
- Verify server requirements
- Test API endpoints directly

---

**ProcureEase** - Professional procurement management with multi-page architecture. Simple setup, powerful features, enterprise-ready functionality.
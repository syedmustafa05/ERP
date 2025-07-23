# ProcureEase - Complete Procurement Management System

A fully functional, single-file procurement management application built with PHP, SQLite, and modern web technologies.

## 🚀 Features

- **Complete Backend & Frontend** - All in one file
- **SQLite Database** - No external database setup required
- **RESTful API** - Full CRUD operations for all entities
- **Professional UI** - Modern responsive design with Bootstrap 5
- **Real-time Data** - Dynamic dashboard with live statistics
- **Complete Procurement Workflow** - From requisitions to invoices

## 📋 What's Included

### Core Modules
- **Dashboard** - Overview with key metrics and recent activities
- **Requisitions** - Request management with approval workflows
- **Vendors** - Supplier management with ratings and contact info
- **Purchase Orders** - Order creation and tracking
- **Goods Receipts** - Delivery confirmation and quality control
- **Invoices** - Financial tracking and payment management

### Key Features
- ✅ **Single File Deployment** - Everything in one PHP file
- ✅ **Auto Database Setup** - Creates tables and sample data automatically
- ✅ **Professional Design** - Modern, responsive interface
- ✅ **Full CRUD Operations** - Create, read, update, delete for all entities
- ✅ **Real-time Statistics** - Dynamic dashboard metrics
- ✅ **Modal Forms** - Streamlined data entry experience
- ✅ **Toast Notifications** - User feedback for actions
- ✅ **Mobile Responsive** - Works on all device sizes

## 🛠 Installation

### Option 1: Direct Download
1. Download the `index.php` file
2. Place it in your web server directory
3. Open in browser - that's it!

### Option 2: Quick Setup
```bash
# Clone or download the file
wget https://your-server.com/procure-ease/index.php

# Make sure PHP is installed
php -v

# Run with PHP built-in server
php -S localhost:8000

# Open browser to http://localhost:8000
```

## 💻 Requirements

- **PHP 7.4+** with PDO SQLite extension
- **Web Server** (Apache, Nginx, or PHP built-in server)
- **Modern Browser** (Chrome, Firefox, Safari, Edge)

## 🎯 Usage

### Getting Started
1. **Access the Application** - Open the file in your browser
2. **Explore the Dashboard** - View system overview and statistics
3. **Navigate Modules** - Use the sidebar to access different features
4. **Create Records** - Click "+" buttons to add new data
5. **Manage Data** - Edit or delete records using action buttons

### Sample Data
The application comes with pre-loaded sample data including:
- 3 vendors with contact information
- 5 requisitions with different statuses
- 3 purchase orders in various stages
- 1 goods receipt and 2 invoices

### API Access
Access the RESTful API directly:
```
GET    /index.php?api=dashboard
GET    /index.php?api=requisitions
POST   /index.php?api=requisitions
PUT    /index.php?api=requisitions/1
DELETE /index.php?api=requisitions/1
```

## 🏗 Architecture

### Backend (PHP)
- **Database Layer** - SQLite with PDO
- **API Layer** - RESTful endpoints with JSON responses
- **Business Logic** - CRUD operations and data validation
- **Auto-setup** - Database creation and sample data seeding

### Frontend (JavaScript)
- **SPA Architecture** - Single-page application experience
- **Modular Design** - Component-based structure
- **State Management** - Client-side data caching
- **Responsive Design** - Mobile-first approach

### Design System
- **Bootstrap 5** - Modern CSS framework
- **FontAwesome 6** - Professional iconography
- **Inter Font** - Clean, readable typography
- **Custom CSS Variables** - Consistent theming

## 📊 Database Schema

### Tables
- `users` - User accounts and authentication
- `vendors` - Supplier information and ratings
- `requisitions` - Purchase requests and approvals
- `purchase_orders` - Orders with vendor assignments
- `goods_receipts` - Delivery confirmations
- `invoices` - Financial records and payments

### Relationships
- Purchase Orders → Requisitions (many-to-one)
- Purchase Orders → Vendors (many-to-one)
- Goods Receipts → Purchase Orders (many-to-one)
- Invoices → Purchase Orders (many-to-one)

## 🎨 Customization

### Themes
Modify CSS variables in the `<style>` section:
```css
:root {
    --primary-color: #3B82F6;
    --secondary-color: #2DD4BF;
    --success-color: #10B981;
    /* ... more variables */
}
```

### API Extensions
Add new endpoints in the `handleAPI()` function:
```php
case 'my-endpoint':
    echo json_encode(myCustomFunction($pdo));
    break;
```

### UI Components
Extend the JavaScript app class:
```javascript
class ProcureEaseApp {
    // Add new methods
    async loadMyCustomPage() {
        // Custom page logic
    }
}
```

## 🔒 Security Features

- **SQL Injection Protection** - Prepared statements
- **XSS Prevention** - Output escaping
- **CSRF Protection** - Token validation (can be added)
- **Input Validation** - Client and server-side validation
- **Error Handling** - Graceful error responses

## 📈 Performance

- **Optimized Queries** - Efficient database operations
- **Client-side Caching** - Reduced server requests
- **Lazy Loading** - Pages load on demand
- **Compressed Assets** - CDN-delivered resources
- **Responsive Images** - Adaptive media loading

## 🐛 Troubleshooting

### Common Issues

**Database Errors**
- Ensure PHP PDO SQLite extension is installed
- Check file permissions for database creation
- Verify web server has write access to directory

**PHP Errors**
- Enable error reporting: `error_reporting(E_ALL)`
- Check PHP version compatibility (7.4+)
- Verify all required extensions are loaded

**Browser Issues**
- Clear browser cache and cookies
- Disable browser extensions that might interfere
- Use browser developer tools to check for JavaScript errors

## 🚀 Deployment

### Production Deployment
1. **Upload File** - Transfer `index.php` to web server
2. **Set Permissions** - Ensure write access for database
3. **Configure Server** - Set up virtual host if needed
4. **SSL Certificate** - Enable HTTPS for security
5. **Backup Strategy** - Regular database backups

### Environment Variables
For production, consider extracting settings:
```php
// Add at top of file
$config = [
    'db_path' => $_ENV['DB_PATH'] ?? __DIR__ . '/procure_ease.db',
    'debug' => $_ENV['DEBUG'] ?? false,
    'app_name' => $_ENV['APP_NAME'] ?? 'ProcureEase'
];
```

## 📄 License

This project is open source and available under the MIT License.

## 👥 Contributing

This is a single-file application designed for simplicity. For contributions:
1. Fork the project
2. Create your feature branch
3. Test thoroughly
4. Submit a pull request

## 📞 Support

For support and questions:
- Check the troubleshooting section
- Review the code comments
- Open an issue on the project repository

---

**ProcureEase** - Professional procurement management made simple. One file, infinite possibilities.
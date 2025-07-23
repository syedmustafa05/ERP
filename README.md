# ProcureEase - Procurement Management System

A complete, full-stack ERP procurement management application built with Laravel backend and vanilla JavaScript frontend.

## 🚀 Features

- **Dashboard**: Overview of procurement metrics and recent activity
- **Requisitions Management**: Create, edit, and track purchase requests
- **Vendor Management**: Maintain supplier information and relationships
- **Purchase Orders**: Generate and track orders from requisitions ✅ **COMPLETED**
- **Goods Receipts**: Record received items and quantities ✅ **COMPLETED**
- **Invoice Management**: Track and process payments ✅ **COMPLETED**
- **Modern UI**: Professional, responsive design with Bootstrap 5

### Complete Procurement Workflow
All pages are now fully implemented with:
- Full CRUD operations (Create, Read, Update, Delete)
- Modal-based forms for data entry
- Responsive data tables with search and pagination
- Professional UI with consistent styling
- Mock authentication and data management
- Ready for backend API integration

## 🏗️ Architecture

### Backend (Laravel)
- **Framework**: Laravel (latest stable version)
- **Database**: SQLite (default) - easily configurable for MySQL/PostgreSQL
- **API**: RESTful API with full CRUD operations
- **Authentication**: Token-based authentication (ready for Laravel Sanctum)
- **Models**: Eloquent ORM with proper relationships

### Frontend (Traditional Stack)
- **Structure**: Multi-page application with separate HTML files
- **Styling**: Bootstrap 5 with custom CSS variables
- **JavaScript**: Vanilla ES6+ with modern async/await patterns
- **Design**: Professional blue (#3B82F6) and teal (#2DD4BF) color scheme
- **Font**: Inter from Google Fonts

## 📁 Project Structure

```
├── backend/                    # Laravel application
│   ├── app/
│   │   ├── Models/            # Eloquent models
│   │   └── Http/Controllers/  # API controllers
│   ├── database/
│   │   ├── migrations/        # Database schema
│   │   └── seeders/          # Sample data
│   └── routes/api.php         # API routes
│
├── frontend/                   # Frontend application
│   ├── css/style.css          # Custom styles
│   ├── js/app.js              # Main JavaScript application
│   └── pages/                 # HTML pages
│       ├── login.html
│       ├── dashboard.html
│       ├── requisitions.html
│       └── vendors.html
│
└── index.html                 # Landing page
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.4+
- Composer
- A web server (Apache/Nginx or PHP built-in server)

### Backend Setup

1. **Install Dependencies**
   ```bash
   cd backend
   composer install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Start Laravel Server**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

### Frontend Setup

1. **Serve Frontend Files**
   - Use any web server to serve the frontend files
   - Or use a simple HTTP server like Python's: `python -m http.server 3000`

2. **Access Application**
   - Frontend: `http://localhost:3000` (or your server URL)
   - Backend API: `http://localhost:8000/api`
   - Use the demo credentials: `test@example.com` / `password`

## 🌐 Application URLs

- **Frontend Application**: `http://localhost:3000`
- **Backend API**: `http://localhost:8000/api`
- **Laravel Backend**: `http://localhost:8000`

### Available Pages
- Login: `/frontend/pages/login.html`
- Dashboard: `/frontend/pages/dashboard.html`
- Requisitions: `/frontend/pages/requisitions.html`
- Vendors: `/frontend/pages/vendors.html`
- Purchase Orders: `/frontend/pages/purchase-orders.html` ✅ **NEW**
- Goods Receipts: `/frontend/pages/goods-receipts.html` ✅ **NEW**
- Invoices: `/frontend/pages/invoices.html` ✅ **NEW**

## 📊 Database Schema

### Tables

1. **requisitions**
   - id, item, quantity, status, requestedBy, date

2. **vendors**
   - id, name, contact, email, phone

3. **purchase_orders**
   - id, requisition_id, vendor_id, total_amount, status, order_date

4. **goods_receipts**
   - id, purchase_order_id, received_date, quantity_received, item

5. **invoices**
   - id, purchase_order_id, invoice_number, amount, status, invoice_date

## 🔌 API Endpoints

All endpoints are prefixed with `/api/`

### Requisitions
- `GET /requisitions` - List all requisitions
- `POST /requisitions` - Create new requisition
- `GET /requisitions/{id}` - Get specific requisition
- `PUT /requisitions/{id}` - Update requisition
- `DELETE /requisitions/{id}` - Delete requisition

### Vendors
- `GET /vendors` - List all vendors
- `POST /vendors` - Create new vendor
- `GET /vendors/{id}` - Get specific vendor
- `PUT /vendors/{id}` - Update vendor
- `DELETE /vendors/{id}` - Delete vendor

### Purchase Orders
- `GET /purchase-orders` - List all purchase orders
- `POST /purchase-orders` - Create new purchase order
- `GET /purchase-orders/{id}` - Get specific purchase order
- `PUT /purchase-orders/{id}` - Update purchase order
- `DELETE /purchase-orders/{id}` - Delete purchase order

### Goods Receipts
- `GET /goods-receipts` - List all goods receipts
- `POST /goods-receipts` - Create new goods receipt
- `GET /goods-receipts/{id}` - Get specific goods receipt
- `PUT /goods-receipts/{id}` - Update goods receipt
- `DELETE /goods-receipts/{id}` - Delete goods receipt

### Invoices
- `GET /invoices` - List all invoices
- `POST /invoices` - Create new invoice
- `GET /invoices/{id}` - Get specific invoice
- `PUT /invoices/{id}` - Update invoice
- `DELETE /invoices/{id}` - Delete invoice

### Dashboard
- `GET /dashboard` - Get dashboard summary data

## 🎨 Design System

### Colors
- **Primary**: #3B82F6 (Professional Blue)
- **Accent**: #2DD4BF (Teal)
- **Text Dark**: #1F2937
- **Text Light**: #6B7280
- **Background**: #F9FAFB

### Typography
- **Font**: Inter (Google Fonts)
- **Weights**: 300, 400, 500, 600, 700

### Components
- **Cards**: Rounded corners with subtle shadows
- **Buttons**: Gradient effects and hover animations
- **Tables**: Responsive with hover effects
- **Modals**: Smooth fade-in animations
- **Forms**: Clean styling with focus states

## 🚀 Features in Detail

### Dashboard
- Real-time statistics cards
- Recent requisitions table
- Pending purchase orders list
- Quick action buttons

### Requisitions Management
- Add/Edit/Delete requisitions
- Status tracking (Pending, Approved, Rejected)
- Date-based filtering
- Responsive data table

### Vendor Management
- Complete vendor information
- Contact details management
- Email validation
- Phone number formatting

### User Interface
- Responsive sidebar navigation
- User dropdown menu
- Toast notifications
- Loading spinners
- Modal dialogs

## 🔐 Authentication

The application includes a demo authentication system:
- **Demo Credentials**: test@example.com / password
- **Session Management**: localStorage-based for demo purposes
- **Ready for**: Laravel Sanctum integration

## 📱 Responsive Design

- **Desktop**: Full sidebar and content layout
- **Tablet**: Adaptive grid systems
- **Mobile**: Collapsible sidebar, stacked layouts

## 🧪 Sample Data

The application comes with pre-populated sample data:
- 4 sample requisitions
- 3 vendor companies
- 2 purchase orders
- Goods receipts and invoices

## 🛡️ Security Features

- Input validation on both frontend and backend
- CSRF protection ready
- SQL injection prevention via Eloquent ORM
- XSS protection through proper data handling

## 🔧 Customization

### Adding New Pages
1. Create HTML file in `frontend/pages/`
2. Add navigation link in sidebar
3. Create corresponding JavaScript class
4. Add API endpoints if needed

### Styling Changes
- Modify CSS variables in `frontend/css/style.css`
- Update color scheme in `:root` selector
- Customize component styles

### API Extensions
- Add new controllers in `backend/app/Http/Controllers/Api/`
- Create corresponding routes in `backend/routes/api.php`
- Update frontend JavaScript for new endpoints

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For questions or support, please open an issue on the repository.

---

**ProcureEase** - Streamlining procurement processes with modern technology.
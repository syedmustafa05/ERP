<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'ProcureEase') }} - Professional Procurement Management</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3B82F6;
            --secondary-color: #2DD4BF;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .btn-hero {
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
            border: 2px solid white;
        }

        .btn-primary-hero:hover {
            background: transparent;
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-hero:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 3rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .btn-hero {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                </div>
                
                <h1 class="hero-title">ProcureEase</h1>
                <p class="hero-subtitle">
                    Professional Procurement Management System built with Laravel
                </p>
                
                <div class="mb-4">
                    <a href="{{ route('dashboard') }}" class="btn-hero btn-primary-hero">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Launch Dashboard
                    </a>
                    <a href="{{ route('api.dashboard') }}" class="btn-hero btn-outline-hero">
                        <i class="fas fa-code me-2"></i>
                        View API
                    </a>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="feature-title">Requisitions</h3>
                        <p>Streamlined request management with approval workflows</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="feature-title">Vendor Management</h3>
                        <p>Comprehensive supplier database and relationship tracking</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h3 class="feature-title">Purchase Orders</h3>
                        <p>Automated order generation and status tracking</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3 class="feature-title">Goods Receipt</h3>
                        <p>Efficient receiving and inventory management</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h3 class="feature-title">Invoice Processing</h3>
                        <p>Automated invoice matching and payment tracking</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Analytics</h3>
                        <p>Real-time insights and procurement analytics</p>
                    </div>
                </div>

                <div class="stats-section">
                    <div class="stats-grid">
                        <div>
                            <span class="stat-number">{{ \App\Models\Requisition::count() }}</span>
                            <span class="stat-label">Requisitions</span>
                        </div>
                        <div>
                            <span class="stat-number">{{ \App\Models\Vendor::count() }}</span>
                            <span class="stat-label">Vendors</span>
                        </div>
                        <div>
                            <span class="stat-number">{{ \App\Models\PurchaseOrder::count() }}</span>
                            <span class="stat-label">Purchase Orders</span>
                        </div>
                        <div>
                            <span class="stat-number">${{ number_format(\App\Models\PurchaseOrder::sum('total_amount'), 0) }}</span>
                            <span class="stat-label">Total Value</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="mb-2">
                        <strong>Tech Stack:</strong> Laravel {{ app()->version() }} • PHP {{ PHP_VERSION }} • Bootstrap 5 • SQLite
                    </p>
                    <p class="mb-0" style="opacity: 0.7;">
                        Professional procurement management solution with modern web technologies
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
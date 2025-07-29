// Dashboard JavaScript for ProcureEase ERP
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    if (!Auth.checkAuth()) {
        return;
    }

    // Initialize dashboard
    initializeDashboard();
    loadDashboardData();
    setupEventListeners();
});

function initializeDashboard() {
    // Set user name
    const user = Auth.getUser();
    if (user) {
        document.getElementById('userName').textContent = user.name;
    }

    // Setup sidebar toggle for mobile
    setupSidebarToggle();
}

function setupSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
}

function setupEventListeners() {
    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Auth.logout();
        });
    }

    // Add click handlers for nav links to set active state
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

async function loadDashboardData() {
    try {
        // Load dashboard statistics
        await loadDashboardStats();
        
        // Load recent requisitions
        await loadRecentRequisitions();
        
        // Load recent activity
        loadRecentActivity();
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        Utils.showToast('Error loading dashboard data', 'error');
    }
}

async function loadDashboardStats() {
    try {
        // For demo purposes, we'll use mock data
        // In a real application, this would fetch from the API
        
        const mockStats = {
            requisitions_count: 45,
            vendors_count: 12,
            purchase_orders_count: 23,
            goods_receipts_count: 18,
            invoices_count: 19,
            pending_requisitions: 7,
            pending_invoices: 5
        };

        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Update stats with animation
        animateCountUp('totalRequisitions', mockStats.requisitions_count);
        animateCountUp('totalVendors', mockStats.vendors_count);
        animateCountUp('pendingOrders', mockStats.pending_requisitions);
        animateCountUp('pendingInvoices', mockStats.pending_invoices);
        
    } catch (error) {
        console.error('Error loading stats:', error);
        // Show placeholder data
        document.getElementById('totalRequisitions').textContent = '--';
        document.getElementById('totalVendors').textContent = '--';
        document.getElementById('pendingOrders').textContent = '--';
        document.getElementById('pendingInvoices').textContent = '--';
    }
}

function animateCountUp(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const startValue = 0;
    const duration = 1500; // 1.5 seconds
    const startTime = Date.now();
    
    function updateCount() {
        const currentTime = Date.now();
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const currentValue = Math.floor(startValue + (targetValue - startValue) * easeOutQuart);
        
        element.textContent = currentValue.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(updateCount);
        } else {
            element.textContent = targetValue.toLocaleString();
        }
    }
    
    requestAnimationFrame(updateCount);
}

async function loadRecentRequisitions() {
    const tableBody = document.querySelector('#recentRequisitionsTable tbody');
    if (!tableBody) return;

    try {
        // Show loading state
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
        
        // Mock data for demonstration
        const mockRequisitions = [
            {
                id: 1,
                item: 'Office Chairs',
                quantity: 10,
                requestedBy: 'John Doe',
                status: 'Pending',
                date: '2024-01-15'
            },
            {
                id: 2,
                item: 'Laptops',
                quantity: 5,
                requestedBy: 'Jane Smith',
                status: 'Approved',
                date: '2024-01-14'
            },
            {
                id: 3,
                item: 'Printer Paper',
                quantity: 100,
                requestedBy: 'Bob Johnson',
                status: 'Pending',
                date: '2024-01-13'
            },
            {
                id: 4,
                item: 'Desk Lamps',
                quantity: 15,
                requestedBy: 'Alice Brown',
                status: 'Approved',
                date: '2024-01-12'
            },
            {
                id: 5,
                item: 'Whiteboard Markers',
                quantity: 50,
                requestedBy: 'Mike Wilson',
                status: 'Rejected',
                date: '2024-01-11'
            }
        ];

        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Clear loading state
        tableBody.innerHTML = '';
        
        // Populate table
        mockRequisitions.forEach(req => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${req.id}</td>
                <td>${req.item}</td>
                <td>${req.quantity}</td>
                <td>${req.requestedBy}</td>
                <td><span class="status-badge status-${req.status.toLowerCase()}">${req.status}</span></td>
                <td>${Utils.formatDate(req.date)}</td>
            `;
            tableBody.appendChild(row);
        });
        
    } catch (error) {
        console.error('Error loading recent requisitions:', error);
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>';
    }
}

function loadRecentActivity() {
    const timeline = document.getElementById('activityTimeline');
    if (!timeline) return;

    const activities = [
        {
            time: '2 hours ago',
            icon: 'bi-clipboard-check',
            color: 'primary',
            title: 'New Requisition Created',
            description: 'John Doe created a requisition for Office Chairs (Qty: 10)'
        },
        {
            time: '4 hours ago',
            icon: 'bi-check-circle',
            color: 'success',
            title: 'Purchase Order Approved',
            description: 'Purchase Order #PO-2024-001 for Laptops has been approved'
        },
        {
            time: '1 day ago',
            icon: 'bi-person-plus',
            color: 'info',
            title: 'New Vendor Added',
            description: 'TechSupply Inc. has been added to the vendor list'
        },
        {
            time: '2 days ago',
            icon: 'bi-box-seam',
            color: 'warning',
            title: 'Goods Received',
            description: 'Received shipment for Purchase Order #PO-2024-002'
        },
        {
            time: '3 days ago',
            icon: 'bi-file-earmark-text',
            color: 'secondary',
            title: 'Invoice Processed',
            description: 'Invoice #INV-2024-001 has been marked as paid'
        }
    ];

    timeline.innerHTML = '';
    
    activities.forEach((activity, index) => {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item mb-4';
        timelineItem.innerHTML = `
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <div class="timeline-icon bg-${activity.color} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi ${activity.icon}"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${activity.title}</h6>
                            <p class="text-muted mb-0">${activity.description}</p>
                        </div>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                </div>
            </div>
        `;
        timeline.appendChild(timelineItem);
    });
}

// Utility function to refresh dashboard data
function refreshDashboard() {
    loadDashboardData();
    Utils.showToast('Dashboard refreshed', 'success');
}

// Auto-refresh dashboard every 5 minutes
setInterval(refreshDashboard, 5 * 60 * 1000);

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + R to refresh dashboard
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        refreshDashboard();
    }
    
    // Ctrl/Cmd + L to logout
    if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
        e.preventDefault();
        Auth.logout();
    }
});

// Export functions for use in other scripts
window.DashboardUtils = {
    refreshDashboard,
    loadDashboardStats,
    loadRecentRequisitions
};
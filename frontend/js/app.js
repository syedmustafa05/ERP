// ProcureEase Application - Main JavaScript File
class ProcureEaseApp {
    constructor() {
        this.apiBaseUrl = 'http://localhost:8000/api';
        this.token = localStorage.getItem('authToken');
        this.currentUser = JSON.parse(localStorage.getItem('currentUser')) || null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.setActiveNavigation();
        if (this.currentUser) {
            this.updateUserInterface();
        }
    }

    // API Utilities
    async makeRequest(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (this.token) {
            defaultOptions.headers['Authorization'] = `Bearer ${this.token}`;
        }

        const config = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        try {
            const response = await fetch(`${this.apiBaseUrl}${endpoint}`, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            this.showNotification('Error: ' + error.message, 'error');
            throw error;
        }
    }

    // Authentication Methods
    async login(email, password) {
        try {
            const response = await this.makeRequest('/login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            this.token = response.token;
            this.currentUser = response.user;
            
            localStorage.setItem('authToken', this.token);
            localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
            
            this.showNotification('Login successful!', 'success');
            window.location.href = 'frontend/pages/dashboard.html';
            
            return response;
        } catch (error) {
            this.showNotification('Login failed: ' + error.message, 'error');
            throw error;
        }
    }

    async logout() {
        try {
            if (this.token) {
                await this.makeRequest('/logout', { method: 'POST' });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.token = null;
            this.currentUser = null;
            localStorage.removeItem('authToken');
            localStorage.removeItem('currentUser');
            window.location.href = '/login.html';
        }
    }

    // Data Management Methods
    async getDashboardData() {
        return await this.makeRequest('/dashboard');
    }

    async getRequisitions() {
        return await this.makeRequest('/requisitions');
    }

    async createRequisition(data) {
        return await this.makeRequest('/requisitions', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateRequisition(id, data) {
        return await this.makeRequest(`/requisitions/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteRequisition(id) {
        return await this.makeRequest(`/requisitions/${id}`, {
            method: 'DELETE'
        });
    }

    async getVendors() {
        return await this.makeRequest('/vendors');
    }

    async createVendor(data) {
        return await this.makeRequest('/vendors', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateVendor(id, data) {
        return await this.makeRequest(`/vendors/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteVendor(id) {
        return await this.makeRequest(`/vendors/${id}`, {
            method: 'DELETE'
        });
    }

    async getPurchaseOrders() {
        return await this.makeRequest('/purchase-orders');
    }

    async createPurchaseOrder(data) {
        return await this.makeRequest('/purchase-orders', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updatePurchaseOrder(id, data) {
        return await this.makeRequest(`/purchase-orders/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deletePurchaseOrder(id) {
        return await this.makeRequest(`/purchase-orders/${id}`, {
            method: 'DELETE'
        });
    }

    async getGoodsReceipts() {
        return await this.makeRequest('/goods-receipts');
    }

    async createGoodsReceipt(data) {
        return await this.makeRequest('/goods-receipts', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateGoodsReceipt(id, data) {
        return await this.makeRequest(`/goods-receipts/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteGoodsReceipt(id) {
        return await this.makeRequest(`/goods-receipts/${id}`, {
            method: 'DELETE'
        });
    }

    async getInvoices() {
        return await this.makeRequest('/invoices');
    }

    async createInvoice(data) {
        return await this.makeRequest('/invoices', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async updateInvoice(id, data) {
        return await this.makeRequest(`/invoices/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteInvoice(id) {
        return await this.makeRequest(`/invoices/${id}`, {
            method: 'DELETE'
        });
    }

    // UI Helper Methods
    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            z-index: 10000;
            max-width: 400px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;

        // Set background color based on type
        switch (type) {
            case 'success':
                notification.style.backgroundColor = '#10B981';
                break;
            case 'error':
                notification.style.backgroundColor = '#EF4444';
                break;
            case 'warning':
                notification.style.backgroundColor = '#F59E0B';
                break;
            default:
                notification.style.backgroundColor = '#3B82F6';
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    showLoading(show = true) {
        let loader = document.getElementById('global-loader');
        if (show) {
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'global-loader';
                loader.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(255, 255, 255, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                `;
                loader.innerHTML = '<div class="spinner"></div>';
                document.body.appendChild(loader);
            }
            loader.style.display = 'flex';
        } else {
            if (loader) {
                loader.style.display = 'none';
            }
        }
    }

    updateUserInterface() {
        if (!this.currentUser) return;

        // Update user dropdown
        const userAvatar = document.querySelector('.user-avatar');
        const userName = document.querySelector('.user-name');
        const userRole = document.querySelector('.user-role');

        if (userAvatar) {
            userAvatar.textContent = this.currentUser.name.charAt(0).toUpperCase();
        }
        if (userName) {
            userName.textContent = this.currentUser.name;
        }
        if (userRole) {
            userRole.textContent = 'Administrator';
        }
    }

    setActiveNavigation() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (href && href.includes(currentPage)) {
                link.classList.add('active');
            }
        });
    }

    bindEvents() {
        // Logout button
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="logout"]')) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    this.logout();
                }
            }
        });

        // Mobile sidebar toggle
        const sidebarToggle = document.querySelector('[data-action="toggle-sidebar"]');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                const sidebar = document.querySelector('.sidebar');
                sidebar.classList.toggle('show');
            });
        }
    }

    // Utility methods for formatting
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    getStatusBadgeClass(status) {
        const statusMap = {
            'Approved': 'badge-success',
            'Pending': 'badge-warning',
            'Rejected': 'badge-danger',
            'Issued': 'badge-info',
            'Pending Approval': 'badge-warning',
            'Completed': 'badge-success',
            'Cancelled': 'badge-danger',
            'Paid': 'badge-success'
        };
        return statusMap[status] || 'badge-info';
    }

    // Modal management
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    bindModalEvents() {
        // Close modal on backdrop click or close button
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
                const modal = e.target.closest('.modal-overlay');
                if (modal) {
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal-overlay.show');
                if (openModal) {
                    openModal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }
        });
    }

    // Form validation
    validateForm(formElement) {
        const errors = [];
        const requiredFields = formElement.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                errors.push(`${field.getAttribute('data-label') || field.name} is required`);
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Email validation
        const emailFields = formElement.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (field.value && !this.isValidEmail(field.value)) {
                errors.push('Please enter a valid email address');
                field.classList.add('is-invalid');
            }
        });

        return errors;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Data table helpers
    renderTable(data, columns, tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');

        // Clear existing content
        thead.innerHTML = '';
        tbody.innerHTML = '';

        // Create header
        const headerRow = thead.insertRow();
        columns.forEach(column => {
            const th = document.createElement('th');
            th.textContent = column.label;
            headerRow.appendChild(th);
        });

        // Create rows
        data.forEach(item => {
            const row = tbody.insertRow();
            columns.forEach(column => {
                const cell = row.insertCell();
                if (column.render) {
                    cell.innerHTML = column.render(item[column.key], item);
                } else {
                    cell.textContent = item[column.key] || '';
                }
            });
        });
    }
}

// Initialize the application
const app = new ProcureEaseApp();

// Export for global access
window.ProcureEaseApp = app;
// ProcureEase Application
class ProcureEaseApp {
    constructor() {
        this.apiBaseUrl = '../backend/api.php?path=';
        this.data = {};
        this.editingId = null;
        this.currentModalType = null;
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Modal events
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => this.hideModal());
        });

        const modalForm = document.getElementById('modal-form');
        if (modalForm) {
            modalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleModalSubmit();
            });
        }
    }

    async makeRequest(endpoint, options = {}) {
        const response = await fetch(this.apiBaseUrl + endpoint, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    showAddModal(type) {
        this.currentModalType = type;
        this.editingId = null;
        
        const titles = {
            'requisition': 'Create Requisition',
            'vendor': 'Add Vendor',
            'purchase-order': 'Create Purchase Order',
            'goods-receipt': 'Create Goods Receipt',
            'invoice': 'Create Invoice'
        };
        
        document.getElementById('modal-title').textContent = titles[type];
        document.getElementById('modal-body').innerHTML = this.getModalForm(type);
        this.showModal();
    }

    showEditModal(type, id) {
        this.currentModalType = type;
        this.editingId = id;
        
        const titles = {
            'requisition': 'Edit Requisition',
            'vendor': 'Edit Vendor',
            'purchase-order': 'Edit Purchase Order',
            'goods-receipt': 'Edit Goods Receipt',
            'invoice': 'Edit Invoice'
        };
        
        document.getElementById('modal-title').textContent = titles[type];
        document.getElementById('modal-body').innerHTML = this.getModalForm(type, id);
        this.showModal();
        
        // Populate form with existing data
        this.populateForm(type, id);
    }

    getModalForm(type, id = null) {
        switch (type) {
            case 'requisition':
                return `
                    <div class="form-group">
                        <label for="item" class="form-label">Item</label>
                        <input type="text" id="item" name="item" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="requestedBy" class="form-label">Requested By</label>
                        <input type="text" id="requestedBy" name="requestedBy" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="priority" class="form-label">Priority</label>
                        <select id="priority" name="priority" class="form-control">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="estimated_cost" class="form-label">Estimated Cost ($)</label>
                        <input type="number" id="estimated_cost" name="estimated_cost" class="form-control" step="0.01" min="0">
                    </div>
                `;
                
            case 'vendor':
                return `
                    <div class="form-group">
                        <label for="name" class="form-label">Company Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="contact" class="form-label">Contact Person</label>
                        <input type="text" id="contact" name="contact" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="rating" class="form-label">Rating (1-5)</label>
                        <input type="number" id="rating" name="rating" class="form-control" min="1" max="5" step="0.1">
                    </div>
                    <div class="form-group">
                        <label for="is_active" class="form-label">Status</label>
                        <select id="is_active" name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                `;
                
            case 'purchase-order':
                return `
                    <div class="form-group">
                        <label for="order_number" class="form-label">Order Number</label>
                        <input type="text" id="order_number" name="order_number" class="form-control" placeholder="Auto-generated if empty">
                    </div>
                    <div class="form-group">
                        <label for="requisition_id" class="form-label">Requisition ID</label>
                        <input type="number" id="requisition_id" name="requisition_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="vendor_id" class="form-label">Vendor ID</label>
                        <input type="number" id="vendor_id" name="vendor_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="total_amount" class="form-label">Total Amount ($)</label>
                        <input type="number" id="total_amount" name="total_amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Pending Approval">Pending Approval</option>
                            <option value="Approved">Approved</option>
                            <option value="Issued">Issued</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_date" class="form-label">Order Date</label>
                        <input type="date" id="order_date" name="order_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                        <input type="date" id="expected_delivery_date" name="expected_delivery_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                `;
                
            case 'goods-receipt':
                return `
                    <div class="form-group">
                        <label for="receipt_number" class="form-label">Receipt Number</label>
                        <input type="text" id="receipt_number" name="receipt_number" class="form-control" placeholder="Auto-generated if empty">
                    </div>
                    <div class="form-group">
                        <label for="purchase_order_id" class="form-label">Purchase Order ID</label>
                        <input type="number" id="purchase_order_id" name="purchase_order_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="item" class="form-label">Item</label>
                        <input type="text" id="item" name="item" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity_received" class="form-label">Quantity Received</label>
                        <input type="number" id="quantity_received" name="quantity_received" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="received_date" class="form-label">Received Date</label>
                        <input type="date" id="received_date" name="received_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="condition" class="form-label">Condition</label>
                        <select id="condition" name="condition" class="form-control">
                            <option value="Good">Good</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Partial">Partial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="received_by" class="form-label">Received By</label>
                        <input type="text" id="received_by" name="received_by" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                `;
                
            case 'invoice':
                return `
                    <div class="form-group">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <input type="text" id="invoice_number" name="invoice_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="purchase_order_id" class="form-label">Purchase Order ID</label>
                        <input type="number" id="purchase_order_id" name="purchase_order_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="amount" class="form-label">Amount ($)</label>
                        <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="invoice_date" class="form-label">Invoice Date</label>
                        <input type="date" id="invoice_date" name="invoice_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" id="due_date" name="due_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-control">
                            <option value="">Select method...</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Check">Check</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                `;
        }
    }

    async populateForm(type, id) {
        try {
            const endpoint = this.getApiEndpoint(type);
            const data = await this.makeRequest(`${endpoint}/${id}`);
            
            if (data) {
                Object.keys(data).forEach(key => {
                    const input = document.getElementById(key);
                    if (input) {
                        input.value = data[key] || '';
                    }
                });
            }
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    async handleModalSubmit() {
        const form = document.getElementById('modal-form');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Convert numeric fields
        ['quantity', 'requisition_id', 'vendor_id', 'purchase_order_id', 'quantity_received', 'is_active'].forEach(field => {
            if (data[field]) data[field] = parseInt(data[field]);
        });
        
        ['total_amount', 'amount', 'estimated_cost', 'rating'].forEach(field => {
            if (data[field]) data[field] = parseFloat(data[field]);
        });

        try {
            const endpoint = this.getApiEndpoint(this.currentModalType);
            
            if (this.editingId) {
                await this.makeRequest(`${endpoint}/${this.editingId}`, {
                    method: 'PUT',
                    body: JSON.stringify(data)
                });
                this.showToast('Record updated successfully!', 'success');
            } else {
                await this.makeRequest(endpoint, {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                this.showToast('Record created successfully!', 'success');
            }
            
            this.hideModal();
            window.location.reload(); // Reload page to show updated data
        } catch (error) {
            this.showToast(`Error: ${error.message}`, 'error');
        }
    }

    async deleteRecord(endpoint, id) {
        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }
        
        try {
            await this.makeRequest(`${endpoint}/${id}`, {
                method: 'DELETE'
            });
            this.showToast('Record deleted successfully!', 'success');
            window.location.reload(); // Reload page to show updated data
        } catch (error) {
            this.showToast(`Error: ${error.message}`, 'error');
        }
    }

    async markAsPaid(invoiceId) {
        try {
            await this.makeRequest(`invoices/${invoiceId}`, {
                method: 'PUT',
                body: JSON.stringify({ status: 'Paid', paid_date: new Date().toISOString().split('T')[0] })
            });
            this.showToast('Invoice marked as paid!', 'success');
            window.location.reload();
        } catch (error) {
            this.showToast(`Error: ${error.message}`, 'error');
        }
    }

    getApiEndpoint(type) {
        const endpoints = {
            'requisition': 'requisitions',
            'vendor': 'vendors',
            'purchase-order': 'purchase-orders',
            'goods-receipt': 'goods-receipts',
            'invoice': 'invoices'
        };
        return endpoints[type];
    }

    showModal() {
        document.getElementById('generic-modal').style.display = 'flex';
    }

    hideModal() {
        document.getElementById('generic-modal').style.display = 'none';
    }

    showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
        `;
        
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    getStatusClass(status) {
        const statusClasses = {
            'Pending': 'warning',
            'Approved': 'success',
            'Rejected': 'danger',
            'Issued': 'primary',
            'Completed': 'success',
            'Pending Approval': 'warning',
            'Paid': 'success',
            'Overdue': 'danger',
            'Cancelled': 'secondary'
        };
        return statusClasses[status] || 'primary';
    }

    getPriorityClass(priority) {
        const priorityClasses = {
            'Low': 'secondary',
            'Medium': 'primary',
            'High': 'warning',
            'Urgent': 'danger'
        };
        return priorityClasses[priority] || 'secondary';
    }

    formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString();
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount || 0);
    }
}

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.app = new ProcureEaseApp();
});
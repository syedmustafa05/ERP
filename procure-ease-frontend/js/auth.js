// Authentication JavaScript for ProcureEase ERP
const API_BASE_URL = 'http://localhost:8000/api';

// Auth utilities
const Auth = {
    // Check if user is authenticated
    isAuthenticated() {
        return localStorage.getItem('authToken') !== null;
    },

    // Get stored auth token
    getToken() {
        return localStorage.getItem('authToken');
    },

    // Store auth token
    setToken(token) {
        localStorage.setItem('authToken', token);
    },

    // Remove auth token
    removeToken() {
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
    },

    // Get stored user info
    getUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },

    // Store user info
    setUser(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },

    // Login function
    async login(email, password) {
        try {
            // For demo purposes, we'll simulate authentication
            // In a real app, this would make an API call to Laravel backend
            
            if (email === 'admin@procureease.com' && password === 'admin123') {
                // Simulate successful login
                const mockUser = {
                    id: 1,
                    name: 'Admin User',
                    email: 'admin@procureease.com',
                    role: 'admin'
                };
                
                const mockToken = 'demo_token_' + Date.now();
                
                this.setToken(mockToken);
                this.setUser(mockUser);
                
                return {
                    success: true,
                    user: mockUser,
                    token: mockToken
                };
            } else {
                return {
                    success: false,
                    message: 'Invalid credentials'
                };
            }
        } catch (error) {
            console.error('Login error:', error);
            return {
                success: false,
                message: 'Login failed. Please try again.'
            };
        }
    },

    // Logout function
    logout() {
        this.removeToken();
        window.location.href = 'login.html';
    },

    // Check auth and redirect if necessary
    checkAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    }
};

// API utilities
const API = {
    // Make authenticated API request
    async request(endpoint, options = {}) {
        const token = Auth.getToken();
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                ...options,
                headers
            });

            if (response.status === 401) {
                Auth.logout();
                return null;
            }

            const data = await response.json();
            return {
                ok: response.ok,
                status: response.status,
                data
            };
        } catch (error) {
            console.error('API request error:', error);
            return {
                ok: false,
                error: error.message
            };
        }
    },

    // GET request
    async get(endpoint) {
        return this.request(endpoint);
    },

    // POST request
    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    // PUT request
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    // DELETE request
    async delete(endpoint) {
        return this.request(endpoint, {
            method: 'DELETE'
        });
    }
};

// Utility functions
const Utils = {
    // Show loading spinner
    showLoading(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.remove('d-none');
        }
    },

    // Hide loading spinner
    hideLoading(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.add('d-none');
        }
    },

    // Show toast notification
    showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    },

    // Create toast container if it doesn't exist
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
        return container;
    },

    // Format date
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },

    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const spinner = document.getElementById('loginSpinner');
            
            // Show loading state
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            
            try {
                const result = await Auth.login(email, password);
                
                if (result.success) {
                    Utils.showToast('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 1500);
                } else {
                    Utils.showToast(result.message || 'Login failed', 'error');
                }
            } catch (error) {
                Utils.showToast('An error occurred during login', 'error');
            } finally {
                // Hide loading state
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    }

    // Auto-fill demo credentials
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    if (emailInput && passwordInput) {
        // Pre-fill demo credentials for convenience
        emailInput.value = 'admin@procureease.com';
        passwordInput.value = 'admin123';
    }

    // Check if already authenticated and redirect
    if (Auth.isAuthenticated() && window.location.pathname.includes('login.html')) {
        window.location.href = 'dashboard.html';
    }
});

// Export for use in other scripts
window.Auth = Auth;
window.API = API;
window.Utils = Utils;
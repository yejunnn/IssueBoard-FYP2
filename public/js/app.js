let currentUser = null;
let isAdmin = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    setupEventListeners();
    setupImageUpload();
    setupFormValidation();
    setupStatusUpdates();
    setupSearchAndFilter();
    setupAnimations();
    setupPrintFunctionality();
    setupLoadingStates();

    const adminElements = document.querySelectorAll('[data-admin]');
    if (adminElements.length > 0) {
        isAdmin = true;
    }
}

// Setup loading states for all forms and buttons
function setupLoadingStates() {
    // Auto-add loading state to all forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.classList.contains('no-loading')) {
                addButtonLoading(submitBtn);
            }
        });
    });

    // Add loading to buttons with data-loading attribute
    document.querySelectorAll('[data-loading]').forEach(btn => {
        btn.addEventListener('click', function() {
            addButtonLoading(this);
        });
    });

    // Add loading to links with data-loading attribute
    document.querySelectorAll('a[data-loading]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('loading')) {
                addLinkLoading(this);
            }
        });
    });
}

function addButtonLoading(button) {
    if (button.classList.contains('loading')) return;

    button.classList.add('loading');
    button.disabled = true;

    // Store original content
    const originalContent = button.innerHTML;
    button.setAttribute('data-original-content', originalContent);

    // Get button text (without icons)
    const buttonText = button.textContent.trim() || 'Processing';

    // Add spinner
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        <span class="loading-text">${buttonText}...</span>
    `;
}

function addLinkLoading(link) {
    link.classList.add('loading');
    const originalContent = link.innerHTML;
    link.setAttribute('data-original-content', originalContent);

    link.innerHTML = `
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        Loading...
    `;
}

function removeButtonLoading(button) {
    button.classList.remove('loading');
    button.disabled = false;

    const originalContent = button.getAttribute('data-original-content');
    if (originalContent) {
        button.innerHTML = originalContent;
    }
}

function setupEventListeners() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            navbarCollapse.classList.toggle('show');
        });
    }

    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

function setupImageUpload() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                previewImage(file, this);
            }
        });

        const uploadArea = input.closest('.image-upload-area');
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    input.files = e.dataTransfer.files;
                    previewImage(file, input);
                }
            });
        }
    });
}

function previewImage(file, input) {
    const reader = new FileReader();
    const previewContainer = input.parentElement.querySelector('.image-preview-container');
    
    if (!previewContainer) {
        const container = document.createElement('div');
        container.className = 'image-preview-container mt-3';
        input.parentElement.appendChild(container);
    }

    reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'image-preview';
        img.style.maxHeight = '200px';
        
        const container = input.parentElement.querySelector('.image-preview-container');
        container.innerHTML = '';
        container.appendChild(img);
    };

    reader.readAsDataURL(file);
}

function setupFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const isValid = field.checkValidity();
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    }
}

/**
 * Setup status update functionality
 */
function setupStatusUpdates() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const ticketId = this.getAttribute('data-ticket-id');
            const newStatus = this.value;
            
            updateTicketStatus(ticketId, newStatus);
        });
    });
}

/**
 * Update ticket status via AJAX
 */
function updateTicketStatus(ticketId, status) {
    const formData = new FormData();
    formData.append('status', status);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'PATCH');

    fetch(`/tickets/${ticketId}/status`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Status updated successfully!', 'success');
            updateStatusBadge(ticketId, status);
        } else {
            showAlert('Failed to update status.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating status.', 'danger');
    });
}

/**
 * Update status badge
 */
function updateStatusBadge(ticketId, status) {
    const badge = document.querySelector(`[data-ticket-id="${ticketId}"] .status-badge`);
    if (badge) {
        badge.className = `badge status-badge bg-${getStatusClass(status)}`;
        badge.textContent = status;
    }
}

/**
 * Get status class for badge
 */
function getStatusClass(status) {
    switch(status) {
        case 'Open': return 'warning';
        case 'In Progress': return 'info';
        case 'Completed': return 'success';
        case 'Cancel': return 'danger';
        default: return 'secondary';
    }
}

/**
 * Setup search and filter functionality
 */
function setupSearchAndFilter() {
    const searchInput = document.querySelector('#search-input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            filterTable(this.value);
        }, 300));
    }

    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });
}

/**
 * Filter table rows
 */
function filterTable(searchTerm) {
    const table = document.querySelector('.filterable-table');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    const term = searchTerm.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(term)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function applyFilters() {
    const filters = {};
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        if (select.value) {
            filters[select.name] = select.value;
        }
    });

    const url = new URL(window.location);
    Object.keys(filters).forEach(key => {
        url.searchParams.set(key, filters[key]);
    });
    window.location.href = url.toString();
}

function setupAnimations() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });

    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        table.classList.add('slide-in');
    });
}

function setupPrintFunctionality() {
    const printButtons = document.querySelectorAll('.print-btn');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.print();
        });
    });
}

function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.alert-container') || document.body;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function showLoading(element) {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm me-2';
    spinner.setAttribute('role', 'status');
    spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
    
    element.disabled = true;
    element.insertBefore(spinner, element.firstChild);
}

function hideLoading(element) {
    const spinner = element.querySelector('.spinner-border');
    if (spinner) {
        spinner.remove();
    }
    element.disabled = false;
}

function debounce(func, wait) {
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

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Copied to clipboard!', 'success');
    }).catch(() => {
        showAlert('Failed to copy to clipboard.', 'danger');
    });
}

function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    let csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.textContent.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function generatePDF(elementId, filename = 'document.pdf') {
    showAlert('PDF generation feature requires additional setup.', 'info');
}

window.CampusIssueBoard = {
    showAlert,
    showLoading,
    hideLoading,
    formatDate,
    copyToClipboard,
    exportTableToCSV,
    generatePDF
}; 
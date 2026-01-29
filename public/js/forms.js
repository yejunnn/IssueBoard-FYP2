/**
 * Campus Issue Board - Form Handling
 */

class FormHandler {
    constructor(formSelector, options = {}) {
        this.form = document.querySelector(formSelector);
        this.options = {
            validateOnInput: true,
            showSuccessMessage: true,
            showErrorMessage: true,
            autoSubmit: false,
            ...options
        };
        
        this.init();
    }
    
    init() {
        if (!this.form) return;
        
        this.setupEventListeners();
        this.setupValidation();
        this.setupAutoSave();
    }
    
    setupEventListeners() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        if (this.options.validateOnInput) {
            const inputs = this.form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => this.clearFieldError(input));
            });
        }
        
        const fileInputs = this.form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.handleFileUpload(e));
        });
    }
    
    setupValidation() {
        this.validators = {
            required: (value) => value && value.trim() !== '',
            email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            phone: (value) => /^[\+]?[1-9][\d]{0,15}$/.test(value.replace(/[\s\-\(\)]/g, '')),
            minLength: (value, min) => value && value.length >= min,
            maxLength: (value, max) => value && value.length <= max,
            numeric: (value) => !isNaN(value) && !isNaN(parseFloat(value)),
            url: (value) => {
                try {
                    new URL(value);
                    return true;
                } catch {
                    return false;
                }
            }
        };
    }
    
    setupAutoSave() {
        if (!this.options.autoSave) return;
        
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => this.autoSave());
        });
    }
    
    validateField(field) {
        const value = field.value;
        const rules = this.getFieldRules(field);
        let isValid = true;
        let errorMessage = '';
        
        for (const [rule, param] of Object.entries(rules)) {
            if (this.validators[rule]) {
                const fieldValid = this.validators[rule](value, param);
                if (!fieldValid) {
                    isValid = false;
                    errorMessage = this.getErrorMessage(rule, param);
                    break;
                }
            }
        }
        
        if (isValid) {
            this.clearFieldError(field);
        } else {
            this.showFieldError(field, errorMessage);
        }
        
        return isValid;
    }
    
    getFieldRules(field) {
        const rules = {};
        
        if (field.hasAttribute('required')) {
            rules.required = true;
        }
        
        if (field.type === 'email') {
            rules.email = true;
        }
        
        if (field.hasAttribute('minlength')) {
            rules.minLength = parseInt(field.getAttribute('minlength'));
        }
        
        if (field.hasAttribute('maxlength')) {
            rules.maxLength = parseInt(field.getAttribute('maxlength'));
        }
        if (field.hasAttribute('data-validate')) {
            const customRules = JSON.parse(field.getAttribute('data-validate'));
            Object.assign(rules, customRules);
        }
        
        return rules;
    }
    
    getErrorMessage(rule, param) {
        const messages = {
            required: 'This field is required.',
            email: 'Please enter a valid email address.',
            phone: 'Please enter a valid phone number.',
            minLength: `Minimum length is ${param} characters.`,
            maxLength: `Maximum length is ${param} characters.`,
            numeric: 'Please enter a valid number.',
            url: 'Please enter a valid URL.'
        };
        
        return messages[rule] || 'Invalid input.';
    }
    
    showFieldError(field, message) {
        this.clearFieldError(field);
        
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }
    
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    validateForm() {
        const fields = this.form.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            this.showFormError('Please correct the errors below.');
            return false;
        }
        
        const submitButton = this.form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
        }
        
        try {
            const formData = new FormData(this.form);
            const response = await this.submitForm(formData);
            
            if (response.success) {
                this.showFormSuccess(response.message || 'Form submitted successfully!');
                if (this.options.autoSubmit) {
                    this.form.reset();
                }
            } else {
                this.showFormError(response.message || 'An error occurred.');
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showFormError('An error occurred while submitting the form.');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Submit';
            }
        }
    }
    
    async submitForm(formData) {
        const url = this.form.action || window.location.href;
        const method = this.form.method || 'POST';
        
        const response = await fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
    
    handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            this.showFieldError(e.target, 'File size must be less than 5MB.');
            e.target.value = '';
            return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            this.showFieldError(e.target, 'Only JPEG, PNG, GIF, and WebP files are allowed.');
            e.target.value = '';
            return;
        }
        
        this.previewImage(file, e.target);
    }
    
    previewImage(file, input) {
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
    
    showFormSuccess(message) {
        if (!this.options.showSuccessMessage) return;
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        this.form.parentNode.insertBefore(alertDiv, this.form);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    showFormError(message) {
        if (!this.options.showErrorMessage) return;
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        this.form.parentNode.insertBefore(alertDiv, this.form);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    autoSave() {
        const formData = new FormData(this.form);
        const data = {};
        
        for (const [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        localStorage.setItem('form_autosave_' + this.form.id, JSON.stringify(data));
    }
    
    loadAutoSave() {
        const saved = localStorage.getItem('form_autosave_' + this.form.id);
        if (!saved) return;
        
        const data = JSON.parse(saved);
        
        for (const [key, value] of Object.entries(data)) {
            const field = this.form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = value;
            }
        }
    }
    
    clearAutoSave() {
        localStorage.removeItem('form_autosave_' + this.form.id);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const ticketForm = document.querySelector('#ticket-form');
    if (ticketForm) {
        new FormHandler('#ticket-form', {
            validateOnInput: true,
            showSuccessMessage: true,
            showErrorMessage: true,
            autoSave: true
        });
    }
    
    const userForm = document.querySelector('#user-form');
    if (userForm) {
        new FormHandler('#user-form', {
            validateOnInput: true,
            showSuccessMessage: true,
            showErrorMessage: true
        });
    }
    
    const loginForm = document.querySelector('#login-form');
    if (loginForm) {
        new FormHandler('#login-form', {
            validateOnInput: true,
            showSuccessMessage: false,
            showErrorMessage: true
        });
    }
    
    const registerForm = document.querySelector('#register-form');
    if (registerForm) {
        new FormHandler('#register-form', {
            validateOnInput: true,
            showSuccessMessage: false,
            showErrorMessage: true
        });
    }
});

window.FormHandler = FormHandler;

// Enhanced form utilities
document.addEventListener('DOMContentLoaded', function() {
    // Add password strength indicator
    document.querySelectorAll('input[type="password"]').forEach(input => {
        if (input.name === 'password' && !input.closest('.password-strength-container')) {
            setupPasswordStrength(input);
        }
    });

    // Add character counters to textareas with maxlength
    document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
        setupCharCounter(textarea);
    });

    // Add required indicators
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        const label = field.closest('.form-group')?.querySelector('label') ||
                      field.previousElementSibling;
        if (label && !label.querySelector('.required-indicator')) {
            const indicator = document.createElement('span');
            indicator.className = 'required-indicator';
            indicator.textContent = '*';
            label.appendChild(indicator);
        }
    });

    // Add shake animation on invalid submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const invalidFields = form.querySelectorAll('.is-invalid');
            if (invalidFields.length > 0) {
                invalidFields.forEach(field => {
                    field.classList.add('shake');
                    setTimeout(() => field.classList.remove('shake'), 500);
                });
            }
        });
    });
});

function setupPasswordStrength(input) {
    const container = document.createElement('div');
    container.className = 'password-strength-container';

    const strengthBar = document.createElement('div');
    strengthBar.className = 'password-strength';
    strengthBar.innerHTML = '<div class="password-strength-bar"></div>';

    const strengthText = document.createElement('div');
    strengthText.className = 'password-strength-text text-muted';

    input.parentNode.appendChild(container);
    container.appendChild(strengthBar);
    container.appendChild(strengthText);

    input.addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        const bar = strengthBar.querySelector('.password-strength-bar');

        bar.className = 'password-strength-bar ' + strength.level;
        strengthText.textContent = strength.text;
        strengthText.className = 'password-strength-text text-' + strength.color;
    });
}

function calculatePasswordStrength(password) {
    let score = 0;

    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;

    if (score <= 2) return { level: 'weak', text: 'Weak password', color: 'danger' };
    if (score <= 3) return { level: 'fair', text: 'Fair password', color: 'warning' };
    if (score <= 4) return { level: 'good', text: 'Good password', color: 'info' };
    return { level: 'strong', text: 'Strong password', color: 'success' };
}

function setupCharCounter(textarea) {
    const maxLength = parseInt(textarea.getAttribute('maxlength'));
    const counter = document.createElement('div');
    counter.className = 'char-counter';

    textarea.parentNode.appendChild(counter);

    function updateCounter() {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${textarea.value.length}/${maxLength} characters`;

        counter.classList.remove('warning', 'danger');
        if (remaining < maxLength * 0.1) {
            counter.classList.add('danger');
        } else if (remaining < maxLength * 0.2) {
            counter.classList.add('warning');
        }
    }

    textarea.addEventListener('input', updateCounter);
    updateCounter();
} 
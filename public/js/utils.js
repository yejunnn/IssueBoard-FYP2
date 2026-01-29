/**
 * Campus Issue Board - Utility Functions
 */

/**
 * Format currency
 */
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Format number with commas
 */
function formatNumber(number) {
    return new Intl.NumberFormat('en-US').format(number);
}

/**
 * Format percentage
 */
function formatPercentage(value, decimals = 2) {
    return `${(value * 100).toFixed(decimals)}%`;
}

/**
 * Generate random ID
 */
function generateId(prefix = 'id') {
    return `${prefix}_${Math.random().toString(36).substr(2, 9)}`;
}

/**
 * Deep clone object
 */
function deepClone(obj) {
    if (obj === null || typeof obj !== 'object') return obj;
    if (obj instanceof Date) return new Date(obj.getTime());
    if (obj instanceof Array) return obj.map(item => deepClone(item));
    if (typeof obj === 'object') {
        const clonedObj = {};
        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                clonedObj[key] = deepClone(obj[key]);
            }
        }
        return clonedObj;
    }
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

/**
 * Check if element is in viewport
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Smooth scroll to element
 */
function smoothScrollTo(element, offset = 0) {
    const targetPosition = element.offsetTop - offset;
    window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
    });
}

/**
 * Get URL parameters
 */
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params) {
        result[key] = value;
    }
    return result;
}

/**
 * Set URL parameter
 */
function setUrlParam(key, value) {
    const url = new URL(window.location);
    url.searchParams.set(key, value);
    window.history.pushState({}, '', url);
}

/**
 * Remove URL parameter
 */
function removeUrlParam(key) {
    const url = new URL(window.location);
    url.searchParams.delete(key);
    window.history.pushState({}, '', url);
}

/**
 * Local storage wrapper
 */
const Storage = {
    set: function(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error('Error saving to localStorage:', e);
        }
    },
    
    get: function(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.error('Error reading from localStorage:', e);
            return defaultValue;
        }
    },
    
    remove: function(key) {
        try {
            localStorage.removeItem(key);
        } catch (e) {
            console.error('Error removing from localStorage:', e);
        }
    },
    
    clear: function() {
        try {
            localStorage.clear();
        } catch (e) {
            console.error('Error clearing localStorage:', e);
        }
    }
};

/**
 * Session storage wrapper
 */
const SessionStorage = {
    set: function(key, value) {
        try {
            sessionStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error('Error saving to sessionStorage:', e);
        }
    },
    
    get: function(key, defaultValue = null) {
        try {
            const item = sessionStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.error('Error reading from sessionStorage:', e);
            return defaultValue;
        }
    },
    
    remove: function(key) {
        try {
            sessionStorage.removeItem(key);
        } catch (e) {
            console.error('Error removing from sessionStorage:', e);
        }
    },
    
    clear: function() {
        try {
            sessionStorage.clear();
        } catch (e) {
            console.error('Error clearing sessionStorage:', e);
        }
    }
};

/**
 * Cookie management
 */
const Cookies = {
    set: function(name, value, days = 7) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    },
    
    get: function(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    },
    
    remove: function(name) {
        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
    }
};

/**
 * Validation helpers
 */
const Validation = {
    email: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    phone: function(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/[\s\-\(\)]/g, ''));
    },
    
    url: function(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    },
    
    required: function(value) {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    },
    
    minLength: function(value, min) {
        return value && value.length >= min;
    },
    
    maxLength: function(value, max) {
        return value && value.length <= max;
    },
    
    numeric: function(value) {
        return !isNaN(value) && !isNaN(parseFloat(value));
    },
    
    integer: function(value) {
        return Number.isInteger(Number(value));
    }
};

/**
 * String utilities
 */
const StringUtils = {
    capitalize: function(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    },
    
    titleCase: function(str) {
        return str.replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    },
    
    truncate: function(str, length, suffix = '...') {
        if (str.length <= length) return str;
        return str.substring(0, length) + suffix;
    },
    
    slugify: function(str) {
        return str
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    },
    
    escapeHtml: function(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },
    
    unescapeHtml: function(str) {
        const div = document.createElement('div');
        div.innerHTML = str;
        return div.textContent;
    }
};

/**
 * Array utilities
 */
const ArrayUtils = {
    unique: function(arr) {
        return [...new Set(arr)];
    },
    
    chunk: function(arr, size) {
        const chunks = [];
        for (let i = 0; i < arr.length; i += size) {
            chunks.push(arr.slice(i, i + size));
        }
        return chunks;
    },
    
    shuffle: function(arr) {
        const shuffled = [...arr];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    },
    
    groupBy: function(arr, key) {
        return arr.reduce((groups, item) => {
            const group = item[key];
            groups[group] = groups[group] || [];
            groups[group].push(item);
            return groups;
        }, {});
    }
};

/**
 * Date utilities
 */
const DateUtils = {
    format: function(date, format = 'YYYY-MM-DD') {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        const seconds = String(d.getSeconds()).padStart(2, '0');
        
        return format
            .replace('YYYY', year)
            .replace('MM', month)
            .replace('DD', day)
            .replace('HH', hours)
            .replace('mm', minutes)
            .replace('ss', seconds);
    },
    
    isToday: function(date) {
        const today = new Date();
        const d = new Date(date);
        return d.toDateString() === today.toDateString();
    },
    
    isYesterday: function(date) {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const d = new Date(date);
        return d.toDateString() === yesterday.toDateString();
    },
    
    addDays: function(date, days) {
        const result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    },
    
    addMonths: function(date, months) {
        const result = new Date(date);
        result.setMonth(result.getMonth() + months);
        return result;
    },
    
    addYears: function(date, years) {
        const result = new Date(date);
        result.setFullYear(result.getFullYear() + years);
        return result;
    }
};

window.Utils = {
    formatCurrency,
    formatNumber,
    formatPercentage,
    generateId,
    deepClone,
    throttle,
    isInViewport,
    smoothScrollTo,
    getUrlParams,
    setUrlParam,
    removeUrlParam,
    Storage,
    SessionStorage,
    Cookies,
    Validation,
    StringUtils,
    ArrayUtils,
    DateUtils
}; 
import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine globally available
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

// Global utilities
window.formatCurrency = function(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};

window.formatDate = function(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    };
    
    return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date));
};

window.formatTime = function(date, options = {}) {
    const defaultOptions = {
        hour: '2-digit',
        minute: '2-digit',
    };
    
    return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date));
};

// Copy to clipboard utility
window.copyToClipboard = async function(text) {
    try {
        await navigator.clipboard.writeText(text);
        window.showToast('Copied to clipboard', 'success');
    } catch (error) {
        console.error('Failed to copy to clipboard:', error);
        window.showToast('Failed to copy to clipboard', 'error');
    }
};

// Confirm dialog utility
window.confirmAction = function(message, callback) {
    if (confirm(message)) {
        callback();
    }
};

// Form submission with loading state
window.submitWithLoading = function(form, options = {}) {
    const submitButton = form.querySelector('[type="submit"]');
    const originalText = submitButton.textContent;
    const loadingText = options.loadingText || 'Processing...';
    
    submitButton.disabled = true;
    submitButton.textContent = loadingText;
    submitButton.classList.add('opacity-75');
    
    return {
        reset: function() {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
            submitButton.classList.remove('opacity-75');
        }
    };
};

// Auto-save functionality
window.enableAutoSave = function(form, url, options = {}) {
    const debounceTime = options.debounceTime || 1000;
    const exclude = options.exclude || [];
    
    let timeout;
    let isSubmitting = false;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        if (exclude.includes(input.name)) return;
        
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            
            timeout = setTimeout(() => {
                if (!isSubmitting) {
                    saveForm();
                }
            }, debounceTime);
        });
    });
    
    async function saveForm() {
        isSubmitting = true;
        
        try {
            const formData = new FormData(form);
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                if (options.onSuccess) {
                    options.onSuccess();
                }
            }
        } catch (error) {
            console.error('Auto-save failed:', error);
            if (options.onError) {
                options.onError(error);
            }
        } finally {
            isSubmitting = false;
        }
    }
};

// Keyboard shortcuts
window.addKeyboardShortcut = function(key, callback, options = {}) {
    const { ctrl = false, alt = false, shift = false } = options;
    
    document.addEventListener('keydown', function(e) {
        if (e.key === key && 
            e.ctrlKey === ctrl && 
            e.altKey === alt && 
            e.shiftKey === shift) {
            
            e.preventDefault();
            callback(e);
        }
    });
};

// Initialize common functionality
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus first input with error
    const firstError = document.querySelector('.form-input-error');
    if (firstError) {
        firstError.focus();
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('[data-auto-hide]');
    alerts.forEach(alert => {
        const hideAfter = parseInt(alert.dataset.autoHide) || 5000;
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, hideAfter);
    });
    
    // Add ripple effect to buttons
    const rippleButtons = document.querySelectorAll('[data-ripple]');
    rippleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// Add CSS for ripple animation
const rippleStyles = `
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = rippleStyles;
document.head.appendChild(styleSheet);
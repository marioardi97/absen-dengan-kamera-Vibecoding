import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add request interceptor to handle CSRF token
window.axios.interceptors.request.use(function (config) {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.getAttribute('content');
    }
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Add response interceptor to handle common errors
window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response) {
        // Handle common HTTP errors
        switch (error.response.status) {
            case 401:
                window.location.href = '/login';
                break;
            case 403:
                if (window.showToast) {
                    window.showToast('You do not have permission to perform this action.', 'error');
                }
                break;
            case 419: // CSRF token mismatch
                if (window.showToast) {
                    window.showToast('Your session has expired. Please refresh the page.', 'warning');
                }
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                break;
            case 422:
                // Validation errors are handled by individual components
                break;
            case 500:
                if (window.showToast) {
                    window.showToast('An unexpected error occurred. Please try again.', 'error');
                }
                break;
            default:
                if (window.showToast) {
                    window.showToast(error.response.data?.message || 'An error occurred.', 'error');
                }
        }
    } else if (error.request) {
        // Network error
        if (window.showToast) {
            window.showToast('Network error. Please check your connection.', 'error');
        }
    }
    
    return Promise.reject(error);
});
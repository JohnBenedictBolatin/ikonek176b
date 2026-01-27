import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Include cookies in requests
window.axios.defaults.timeout = 15000; // 15 second timeout for all requests (backend timeout is 10s, so this gives buffer)

// Function to get CSRF token from meta tag or cookie
function getCsrfToken() {
    // First try to get from meta tag
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (metaToken) {
        return metaToken;
    }
    
    // Fallback to cookie (Laravel stores it as XSRF-TOKEN)
    const cookieToken = getCookie('XSRF-TOKEN');
    if (cookieToken) {
        return decodeURIComponent(cookieToken);
    }
    
    return null;
}

// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Set CSRF token for all axios requests
function setCsrfToken() {
    const token = getCsrfToken();
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
}

// Set initial token
setCsrfToken();

// Add request interceptor to refresh token before each request
window.axios.interceptors.request.use(
    function (config) {
        // Refresh CSRF token before each request
        const token = getCsrfToken();
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);

// Add response interceptor to handle 419 errors and refresh token
window.axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        // If we get a 419 error (CSRF token mismatch), try to refresh and retry once
        if (error.response && error.response.status === 419) {
            // Refresh the page to get a new CSRF token
            // Or try to get a new token via a GET request
            const token = getCsrfToken();
            if (token && error.config) {
                error.config.headers['X-CSRF-TOKEN'] = token;
                // Retry the request once
                return window.axios.request(error.config);
            }
        }
        return Promise.reject(error);
    }
);

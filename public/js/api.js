// This file handles communication with our Laravel API.
// It is designed to be extremely simple.

const API_BASE_URL = 'http://localhost:8000/api';

/**
 * Make an API request automatically attaching the JWT token.
 * 
 * @param {string} endpoint - Example: '/products'
 * @param {string} method - 'GET', 'POST', 'PUT', 'DELETE'
 * @param {object} data - Object containing data to send (optional)
 * @returns {Promise<any>} - JSON response from the server
 */
async function apiCall(endpoint, method = 'GET', data = null) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    const token = localStorage.getItem('smartstock_token');
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options = {
        method: method,
        headers: headers
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_BASE_URL}${endpoint}`, options);
        
        // Some responses like DELETE 204 might be empty, so we safely handle JSON parsing
        const text = await response.text();
        const result = text ? JSON.parse(text) : {};

        if (response.status === 401) {
            console.error("Unauthorized! Logging out...");
            localStorage.removeItem('smartstock_token');
            window.location.href = '/login'; 
            throw new Error("Unauthorized");
        }

        return { status: response.status, data: result };

    } catch (error) {
        console.error('API Error:', error);
        // Provide more helpful error message
        if (error.message === 'Failed to fetch') {
            console.error('CORS Error or Server Not Running. Make sure Laravel server is running on http://localhost:8000');
        }
        throw error;
    }
}

/**
 * Check if the user is currently logged in.
 */
function isAuthenticated() {
    return localStorage.getItem('smartstock_token') !== null;
}

/**
 * Securely completely log out
 */
function logoutUser() {
    localStorage.removeItem('smartstock_token');
    window.location.href = '/login';
}

/**
 * Very Simple Beginner-Friendly Toast Notification
 * @param {string} message - Text to display
 * @param {string} type - 'success' or 'error'
 */
function showToast(message, type = 'success') {
    // 1. Create a div element for our popup
    const toast = document.createElement('div');
    
    // 2. Set colors based on success or error using basic Tailwind classes
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    // 3. Add styling so it floats fixed at the bottom right
    toast.className = `fixed bottom-5 right-5 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50 transition-opacity duration-500 flex items-center gap-2`;
    
    // 4. Set the text
    toast.innerHTML = type === 'success' ? `✅ ${message}` : `⚠️ ${message}`;

    // 5. Inject into the page
    document.body.appendChild(toast);

    // 6. Automatically remove it after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0'; // fade out
        setTimeout(() => toast.remove(), 500); // delete completely
    }, 3000);
}

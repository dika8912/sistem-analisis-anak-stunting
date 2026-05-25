// Base URL FastAPI Backend
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:5601';

/**
 * Custom Fetch API Wrapper dengan JWT Interceptor
 */
export async function apiFetch(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    const token = localStorage.getItem('access_token');

    // Setup headers
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers,
    };

    // Inject JWT token jika ada
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    // Eksekusi fetch
    const response = await fetch(url, {
        ...options,
        headers,
    });

    // Handle 401 Unauthorized (Token Expired / Invalid)
    if (response.status === 401) {
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_role');
        window.location.href = '/login';
        throw new Error('Unauthorized');
    }

    // Coba parse JSON (walaupun error)
    const contentType = response.headers.get('content-type');
    let data = null;
    if (contentType && contentType.includes('application/json')) {
        data = await response.json();
    }

    if (!response.ok) {
        throw { status: response.status, data: data, message: response.statusText };
    }

    return data;
}

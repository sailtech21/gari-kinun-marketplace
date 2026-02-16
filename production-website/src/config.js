// API Configuration
// Automatically detect environment and use appropriate URLs
const isProduction = import.meta.env.MODE === 'production'
const PRODUCTION_URL = 'https://admin.garikinun.com' // Laravel backend on admin subdomain
const LOCAL_URL = 'http://localhost:8000' // Local Laravel server

export const BASE_URL = isProduction ? PRODUCTION_URL : LOCAL_URL
export const API_BASE_URL = `${BASE_URL}/api`

// Admin panel URL (can be subdomain or same domain)
export const ADMIN_URL = isProduction 
  ? 'https://admin.garikinun.com'  // Admin subdomain
  : 'http://localhost:8000/admin'   // Local admin

// Helper function to get full image URL
export const getImageUrl = (imagePath) => {
  if (!imagePath) return 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=500' // Default fallback
  
  // If already a full URL, return as is
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }
  
  // Remove leading slash if present
  const cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath
  
  // If path starts with 'storage/', use it directly
  // Otherwise assume it's in storage and add the prefix
  if (cleanPath.startsWith('storage/')) {
    return `${BASE_URL}/${cleanPath}`
  } else {
    return `${BASE_URL}/storage/${cleanPath}`
  }
}

// Helper function for API calls
export const apiCall = async (endpoint, options = {}) => {
  try {
    const token = localStorage.getItem('auth_token')
    const headers = {
      ...options.headers,
    }
    
    // Only set Content-Type for non-FormData requests
    if (!(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json'
    }
    
    // Add Authorization header if token exists
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }
    
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      credentials: 'include',  // Include credentials for CORS requests
      headers,
      ...options,
    })
    
    // Check if response is JSON before parsing
    const contentType = response.headers.get('content-type')
    const isJson = contentType && contentType.includes('application/json')
    
    let data
    if (isJson) {
      data = await response.json()
    } else {
      // If not JSON, likely an HTML error page
      data = { message: 'Server error occurred' }
    }
    
    if (!response.ok) {
      // Silently handle 401 errors (expected when not authenticated)
      if (response.status === 401) {
        throw new Error('Unauthenticated')
      }
      console.error('API Error:', data.message || 'API request failed')
      console.error('Validation Errors:', data.errors || 'No validation errors')
      console.error('Full Response:', data)
      throw new Error(data.message || 'API request failed')
    }
    
    return data
  } catch (error) {
    // Silently handle authentication errors
    if (error.message === 'Unauthenticated') {
      throw error
    }
    console.error('API Error:', error)
    throw error
  }
}

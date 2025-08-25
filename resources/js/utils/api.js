import { useFetch } from '@vueuse/core'
import { ofetch } from 'ofetch'

// Create base API instance following Vuexy standards
export const $api = ofetch.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  
  async onRequest({ options }) {
    // Get access token from cookie (Vuexy standard)
    const accessToken = useCookie('accessToken').value
    
    if (accessToken) {
      // Set authorization header
      options.headers = options.headers || {}
      options.headers.Authorization = `Bearer ${accessToken}`
    }
    
    // Set default headers
    options.headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers
    }
  },
  
  async onResponseError({ response }) {
    // Handle authentication errors
    if (response.status === 401) {
      // Clear auth data and redirect to login
      useCookie('accessToken').value = null
      useCookie('userData').value = null
      useCookie('userAbilityRules').value = null
      
      // Redirect to login page
      await navigateTo('/login')
    }
  }
})

// Create useApi composable for reactive data fetching following Vuexy standards
export const useApi = (url, options = {}) => {
  return useFetch(url, {
    baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
    
    async beforeFetch({ options: fetchOptions }) {
      // Get access token from cookie
      const accessToken = useCookie('accessToken').value
      
      if (accessToken) {
        fetchOptions.headers = {
          ...fetchOptions.headers,
          Authorization: `Bearer ${accessToken}`
        }
      }
      
      // Set default headers
      fetchOptions.headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...fetchOptions.headers
      }
      
      return { options: fetchOptions }
    },
    
    afterFetch(ctx) {
      // Handle successful responses
      return ctx
    },
    
    onFetchError(ctx) {
      // Handle authentication errors
      if (ctx.response?.status === 401) {
        // Clear auth data and redirect to login
        useCookie('accessToken').value = null
        useCookie('userData').value = null
        useCookie('userAbilityRules').value = null
        
        // Redirect to login page
        navigateTo('/login')
      }
      
      return ctx
    },
    
    ...options
  })
}

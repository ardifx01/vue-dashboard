import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userRoles = computed(() => user.value?.roles || [])
  const userPermissions = computed(() => {
    const permissions = []
    userRoles.value.forEach(role => {
      if (role.permissions) {
        permissions.push(...role.permissions)
      }
    })
    return permissions
  })

  // Actions
  const setUser = (userData) => {
    user.value = userData
  }

  const setToken = (authToken) => {
    token.value = authToken
    if (authToken) {
      localStorage.setItem('auth_token', authToken)
    } else {
      localStorage.removeItem('auth_token')
    }
  }

  const login = async (credentials) => {
    isLoading.value = true
    try {
      const response = await $fetch('/api/auth/login', {
        method: 'POST',
        body: credentials
      })
      
      setToken(response.token)
      setUser(response.user)
      
      return response
    } catch (error) {
      console.error('Login error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    isLoading.value = true
    try {
      await $fetch('/api/auth/logout', {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      setToken(null)
      setUser(null)
      isLoading.value = false
      
      // Redirect to login
      await navigateTo('/login')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return

    isLoading.value = true
    try {
      const response = await $fetch('/api/auth/user', {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      
      setUser(response.user)
      return response.user
    } catch (error) {
      console.error('Fetch user error:', error)
      // If token is invalid, clear auth data
      if (error.status === 401) {
        setToken(null)
        setUser(null)
      }
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const hasRole = (roleName) => {
    return userRoles.value.some(role => role.name === roleName)
  }

  const hasPermission = (permissionName) => {
    return userPermissions.value.some(permission => permission.name === permissionName)
  }

  const hasAnyRole = (roleNames) => {
    return roleNames.some(roleName => hasRole(roleName))
  }

  const hasAnyPermission = (permissionNames) => {
    return permissionNames.some(permissionName => hasPermission(permissionName))
  }

  // Initialize auth state from token
  const initializeAuth = async () => {
    if (token.value && !user.value) {
      try {
        await fetchUser()
      } catch (error) {
        console.error('Failed to initialize auth:', error)
      }
    }
  }

  return {
    // State
    user,
    token,
    isLoading,
    
    // Getters
    isAuthenticated,
    userRoles,
    userPermissions,
    
    // Actions
    setUser,
    setToken,
    login,
    logout,
    fetchUser,
    hasRole,
    hasPermission,
    hasAnyRole,
    hasAnyPermission,
    initializeAuth
  }
})

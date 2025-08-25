import { $api } from '@/utils/api'

/**
 * Auth composable following Vuexy Laravel integration standards
 * Based on: https://demos.pixinvent.com/vuexy-vuejs-admin-template/documentation/guide/laravel-integration/
 */
export const useAuth = () => {
  const router = useRouter()
  const ability = useAbility()

  // Reactive auth state from cookies (Vuexy standard)
  const accessToken = useCookie('accessToken')
  const userData = useCookie('userData')
  const userAbilityRules = useCookie('userAbilityRules')

  // Loading state
  const isLoading = ref(false)

  // Computed properties
  const isLoggedIn = computed(() => !!(accessToken.value && userData.value))
  const user = computed(() => userData.value)

  /**
   * Login user with email and password
   * Following Vuexy Laravel Sanctum authentication standards
   */
  const login = async (credentials) => {
    isLoading.value = true
    
    try {
      const response = await $api('/auth/login', {
        method: 'POST',
        body: {
          email: credentials.email,
          password: credentials.password,
          remember_me: credentials.remember_me || false,
        },
      })

      // Store auth data following Vuexy standards
      accessToken.value = response.accessToken
      userData.value = response.userData
      userAbilityRules.value = response.userAbilityRules

      // Update CASL abilities
      ability.update(response.userAbilityRules)

      return response
    } catch (error) {
      console.error('Login error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Register new user
   * Following Vuexy Laravel Sanctum authentication standards
   */
  const register = async (userData) => {
    isLoading.value = true
    
    try {
      const response = await $api('/auth/register', {
        method: 'POST',
        body: userData,
      })

      // Store auth data following Vuexy standards
      accessToken.value = response.accessToken
      userData.value = response.userData
      userAbilityRules.value = response.userAbilityRules

      // Update CASL abilities
      ability.update(response.userAbilityRules)

      return response
    } catch (error) {
      console.error('Registration error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Logout user and clear auth data
   * Following Vuexy Laravel Sanctum authentication standards
   */
  const logout = async () => {
    isLoading.value = true
    
    try {
      // Call logout API to revoke token
      await $api('/auth/logout', {
        method: 'GET', // Vuexy documentation shows GET for logout
      })
    } catch (error) {
      console.error('Logout API error:', error)
      // Continue with logout even if API call fails
    } finally {
      // Clear auth data (Vuexy standard)
      accessToken.value = null
      userData.value = null
      userAbilityRules.value = null

      // Clear CASL abilities
      ability.update([])

      isLoading.value = false

      // Redirect to login page
      await router.push('/login')
    }
  }

  /**
   * Fetch current user data
   * Following Vuexy Laravel Sanctum authentication standards
   */
  const fetchUser = async () => {
    if (!accessToken.value) {
      throw new Error('No access token available')
    }

    isLoading.value = true
    
    try {
      const response = await $api('/auth/user', {
        method: 'GET',
      })

      // Update user data and abilities
      userData.value = response.userData
      userAbilityRules.value = response.userAbilityRules

      // Update CASL abilities
      ability.update(response.userAbilityRules)

      return response.userData
    } catch (error) {
      console.error('Fetch user error:', error)
      
      // If unauthorized, clear auth data
      if (error.status === 401) {
        accessToken.value = null
        userData.value = null
        userAbilityRules.value = null
        ability.update([])
      }
      
      throw error
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Initialize auth state on app load
   * Check if user is still authenticated and refresh user data
   */
  const initAuth = async () => {
    if (accessToken.value && !userData.value) {
      try {
        await fetchUser()
      } catch (error) {
        console.error('Auth initialization failed:', error)
        // Clear invalid auth data
        accessToken.value = null
        userData.value = null
        userAbilityRules.value = null
        ability.update([])
      }
    } else if (accessToken.value && userData.value && userAbilityRules.value) {
      // Update abilities if user data exists
      ability.update(userAbilityRules.value)
    }
  }

  /**
   * Check if user has specific role
   */
  const hasRole = (role) => {
    return userData.value?.role === role
  }

  /**
   * Check if user has any of the specified roles
   */
  const hasAnyRole = (roles) => {
    return roles.includes(userData.value?.role)
  }

  /**
   * Check if user can perform action on subject using CASL
   */
  const can = (action, subject) => {
    return ability.can(action, subject)
  }

  /**
   * Check if user cannot perform action on subject using CASL
   */
  const cannot = (action, subject) => {
    return ability.cannot(action, subject)
  }

  return {
    // State
    isLoading: readonly(isLoading),
    isLoggedIn,
    user,
    accessToken: readonly(accessToken),
    
    // Actions
    login,
    register,
    logout,
    fetchUser,
    initAuth,
    
    // Permissions
    hasRole,
    hasAnyRole,
    can,
    cannot,
  }
}

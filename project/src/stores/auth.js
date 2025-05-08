import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'
import { API_URL } from '../config'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),
  
  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user,
    isAdmin: (state) => state.user && state.user.role === 'admin',
    isEditor: (state) => state.user && state.user.role === 'editor',
    authHeader: (state) => ({
      Authorization: `Bearer ${state.token}`
    })
  },
  
  actions: {
    async login(email, password) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post(`${API_URL}/auth/login`, {
          email,
          password
        })
        
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        
        localStorage.setItem('token', token)
        
        this.setupAxiosInterceptors()
        
        router.push('/dashboard')
        
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        return false
      } finally {
        this.loading = false
      }
    },
    
    async fetchCurrentUser() {
      if (!this.token) return null
      
      this.loading = true
      
      try {
        const response = await axios.get(`${API_URL}/auth/me`, {
          headers: this.authHeader
        })
        
        this.user = response.data
        return this.user
      } catch (error) {
        this.logout()
        return null
      } finally {
        this.loading = false
      }
    },
    
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      router.push('/login')
    },
    
    setupAxiosInterceptors() {
      axios.interceptors.request.use(
        config => {
          if (this.token) {
            config.headers.Authorization = `Bearer ${this.token}`
          }
          return config
        },
        error => Promise.reject(error)
      )
      
      axios.interceptors.response.use(
        response => response,
        error => {
          if (error.response?.status === 401) {
            this.logout()
          }
          return Promise.reject(error)
        }
      )
    },
    
    clearError() {
      this.error = null
    }
  }
})
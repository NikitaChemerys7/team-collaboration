import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'
import { API_URL } from '../config'

axios.defaults.withCredentials = true

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    error: null
  }),

  getters: {
    isLoggedIn: (state) => !!state.user,
    isAdmin: (state) => state.user && state.user.role === 'admin',
    isEditor: (state) => state.user && state.user.role === 'editor'
  },

  actions: {
    async login(email, password) {
      this.loading = true
      this.error = null

      try {
        await axios.get(`${API_URL}/sanctum/csrf-cookie`)

        const response = await axios.post(`${API_URL}/auth/login`, {
          email,
          password
        })

        this.user = response.data.user
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
      this.loading = true
      try {
        const response = await axios.get(`${API_URL}/auth/me`)
        this.user = response.data
        return this.user
      } catch (error) {
        this.logout()
        return null
      } finally {
        this.loading = false
      }
    },

    async logout() {
      await axios.post(`${API_URL}/auth/logout`)
      this.user = null
      router.push('/login')
    },

    clearError() {
      this.error = null
    }
  }
})

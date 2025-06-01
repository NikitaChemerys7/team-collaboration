import { defineStore } from 'pinia'
import axios from 'axios'
import router from '../router'
import { API_URL } from '../config'

axios.defaults.withCredentials = true

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isAdmin: (state) => state.user && state.user.role === 'admin',
    isEditor: (state) => state.user && state.user.role === 'editor'
  },

  actions: {
    async login(email, password) {
      this.loading = true
      this.error = null

      try {
        await axios.get('http://127.0.0.1:8000/sanctum/csrf-cookie')

        const response = await axios.post(`${API_URL}/auth/login`, {
          email,
          password
        })

        this.user = response.data.user
        this.token = response.data.token
        
        localStorage.setItem('user', JSON.stringify(response.data.user))
        localStorage.setItem('token', response.data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
        
        if (this.user.role === 'admin' || this.user.role === 'editor') {
          router.push('/dashboard')
        } else {
          router.push('/')
        }
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
        localStorage.setItem('user', JSON.stringify(response.data))
        return this.user
      } catch (error) {
        this.logout()
        return null
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await axios.post(`${API_URL}/auth/logout`)
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('user')
        localStorage.removeItem('token')
        router.push('/login')
      }
    },

    setUser(user) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },

    async getUsers() {
      try {
        const response = await axios.get(`${API_URL}/users`, {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })
        return response
      } catch (error) {
        console.error('Error fetching users:', error)
        throw error
      }
    },

    async updateUser(userId, userData) {
      try {
        const response = await axios.put(`${API_URL}/users/${userId}`, userData, {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })
        return response
      } catch (error) {
        console.error('Error updating user:', error)
        throw error
      }
    },

    async deleteUser(userId) {
      try {
        const response = await axios.delete(`${API_URL}/users/${userId}`, {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })
        return response
      } catch (error) {
        console.error('Error deleting user:', error)
        throw error
      }
    },

    async updateProfile(userData) {
      try {
        const response = await axios.put(`${API_URL}/profile`, userData)
        return response
      } catch (error) {
        console.error('Error updating profile:', error)
        throw error
      }
    }
  }
})

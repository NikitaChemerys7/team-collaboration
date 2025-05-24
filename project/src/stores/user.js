import { defineStore } from 'pinia'
import axios from 'axios'
import { API_URL } from '../config'
import { useAuthStore } from './auth'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    loading: false,
    error: null
  }),
  
  getters: {
    getEditors: (state) => {
      return state.users.filter(user => user.role === 'editor')
    },
    getAdmins: (state) => {
      return state.users.filter(user => user.role === 'admin')
    },
    getUserById: (state) => (id) => {
      return state.users.find(user => user.id === id)
    }
  },
  
  actions: {
    async fetchUsers() {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.get(
          `${API_URL}/users`,
          { headers: authStore.authHeader }
        )
        this.users = response.data
        return this.users
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch users'
        console.error('Error fetching users:', error)
        return []
      } finally {
        this.loading = false
      }
    },
    
    async createUser(userData) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.post(
          `${API_URL}/users`,
          userData,
          { headers: authStore.authHeader }
        )
        
        this.users.push(response.data)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create user'
        console.error('Error creating user:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async updateUser(id, userData) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.put(
          `${API_URL}/users/${id}`,
          userData,
          { headers: authStore.authHeader }
        )
        
        const index = this.users.findIndex(user => user.id === id)
        if (index !== -1) {
          this.users[index] = response.data
        }
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update user'
        console.error('Error updating user:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async deleteUser(id) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        await axios.delete(
          `${API_URL}/users/${id}`,
          { headers: authStore.authHeader }
        )
        
        this.users = this.users.filter(user => user.id !== id)
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete user'
        console.error('Error deleting user:', error)
        return false
      } finally {
        this.loading = false
      }
    },
    
    clearError() {
      this.error = null
    }
  }
})
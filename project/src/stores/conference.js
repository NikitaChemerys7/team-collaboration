import { defineStore } from 'pinia'
import axios from 'axios'
import { API_URL } from '../config'
import { useAuthStore } from './auth'

export const useConferenceStore = defineStore('conference', {
  state: () => ({
    conferences: [],
    currentConference: null,
    loading: false,
    error: null
  }),
  
  getters: {
    sortedConferences: (state) => {
      return [...state.conferences].sort((a, b) => b.year - a.year)
    },
    getConferenceById: (state) => (id) => {
      return state.conferences.find(conf => conf.id === id)
    }
  },
  
  actions: {
    async fetchConferences() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`${API_URL}/conferences`)
        this.conferences = response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch conferences'
        console.error('Error fetching conferences:', error)
      } finally {
        this.loading = false
      }
    },
    
    async fetchConferenceById(id) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`${API_URL}/conferences/${id}`)
        this.currentConference = response.data
        return this.currentConference
      } catch (error) {
        this.error = error.response?.data?.message || `Failed to fetch conference with ID ${id}`
        console.error(`Error fetching conference ${id}:`, error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async createConference(conferenceData) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.post(
          `${API_URL}/conferences`,
          conferenceData,
          { headers: authStore.authHeader }
        )
        
        this.conferences.push(response.data)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create conference'
        console.error('Error creating conference:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async updateConference(id, conferenceData) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.put(
          `${API_URL}/conferences/${id}`,
          conferenceData,
          { headers: authStore.authHeader }
        )
        
        const index = this.conferences.findIndex(conf => conf.id === id)
        if (index !== -1) {
          this.conferences[index] = response.data
        }
        
        if (this.currentConference && this.currentConference.id === id) {
          this.currentConference = response.data
        }
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || `Failed to update conference with ID ${id}`
        console.error(`Error updating conference ${id}:`, error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async deleteConference(id) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        await axios.delete(
          `${API_URL}/conferences/${id}`,
          { headers: authStore.authHeader }
        )
        
        this.conferences = this.conferences.filter(conf => conf.id !== id)
        
        if (this.currentConference && this.currentConference.id === id) {
          this.currentConference = null
        }
        
        return true
      } catch (error) {
        this.error = error.response?.data?.message || `Failed to delete conference with ID ${id}`
        console.error(`Error deleting conference ${id}:`, error)
        return false
      } finally {
        this.loading = false
      }
    },
    
    async assignEditor(conferenceId, editorId) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.post(
          `${API_URL}/conferences/${conferenceId}/editors`,
          { editorId },
          { headers: authStore.authHeader }
        )
        
        // Update local state
        const index = this.conferences.findIndex(conf => conf.id === conferenceId)
        if (index !== -1) {
          this.conferences[index] = response.data
        }
        
        if (this.currentConference && this.currentConference.id === conferenceId) {
          this.currentConference = response.data
        }
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to assign editor'
        console.error('Error assigning editor:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    clearError() {
      this.error = null
    }
  }
})
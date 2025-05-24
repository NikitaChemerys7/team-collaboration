import { defineStore } from 'pinia'
import axios from 'axios'
import { API_URL } from '../config'
import { useAuthStore } from './auth'

export const useSubpageStore = defineStore('subpage', {
  state: () => ({
    subpages: [],
    currentSubpage: null,
    loading: false,
    error: null
  }),
  
  getters: {
    getSubpagesByConferenceId: (state) => (conferenceId) => {
      return state.subpages.filter(sp => sp.conferenceId === conferenceId)
    },
    getSubpageById: (state) => (id) => {
      return state.subpages.find(sp => sp.id === id)
    }
  },
  
  actions: {
    async fetchSubpages(conferenceId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`${API_URL}/conferences/${conferenceId}/subpages`)
        const newSubpages = response.data
        
        const existingIds = this.subpages
          .filter(sp => sp.conferenceId === conferenceId)
          .map(sp => sp.id)
        
        const uniqueNewSubpages = newSubpages.filter(sp => !existingIds.includes(sp.id))
        
        this.subpages = [
          ...this.subpages.filter(sp => sp.conferenceId !== conferenceId),
          ...newSubpages
        ]
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch subpages'
        console.error('Error fetching subpages:', error)
        return []
      } finally {
        this.loading = false
      }
    },
    
    async fetchSubpage(conferenceId, subpageId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`${API_URL}/conferences/${conferenceId}/subpages/${subpageId}`)
        this.currentSubpage = response.data
        
        const index = this.subpages.findIndex(sp => sp.id === subpageId)
        if (index !== -1) {
          this.subpages[index] = response.data
        } else {
          this.subpages.push(response.data)
        }
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch subpage'
        console.error('Error fetching subpage:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async createSubpage(conferenceId, subpageData) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.post(
          `${API_URL}/conferences/${conferenceId}/subpages`,
          subpageData,
          { headers: authStore.authHeader }
        )
        
        this.subpages.push(response.data)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create subpage'
        console.error('Error creating subpage:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async updateSubpage(conferenceId, subpageId, subpageData) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.put(
          `${API_URL}/conferences/${conferenceId}/subpages/${subpageId}`,
          subpageData,
          { headers: authStore.authHeader }
        )
        const index = this.subpages.findIndex(sp => sp.id === subpageId)
        if (index !== -1) {
          this.subpages[index] = response.data
        }
        
        if (this.currentSubpage && this.currentSubpage.id === subpageId) {
          this.currentSubpage = response.data
        }
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update subpage'
        console.error('Error updating subpage:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    async deleteSubpage(conferenceId, subpageId) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        await axios.delete(
          `${API_URL}/conferences/${conferenceId}/subpages/${subpageId}`,
          { headers: authStore.authHeader }
        )
      
        this.subpages = this.subpages.filter(sp => sp.id !== subpageId)
        
        if (this.currentSubpage && this.currentSubpage.id === subpageId) {
          this.currentSubpage = null
        }
        
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete subpage'
        console.error('Error deleting subpage:', error)
        return false
      } finally {
        this.loading = false
      }
    },
    
    async uploadFile(conferenceId, subpageId, file) {
      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      const formData = new FormData()
      formData.append('file', file)
      
      try {
        const response = await axios.post(
          `${API_URL}/conferences/${conferenceId}/subpages/${subpageId}/files`,
          formData,
          {
            headers: {
              ...authStore.authHeader,
              'Content-Type': 'multipart/form-data'
            }
          }
        )
        
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to upload file'
        console.error('Error uploading file:', error)
        return null
      } finally {
        this.loading = false
      }
    },
    
    clearCurrentSubpage() {
      this.currentSubpage = null
    },
    
    clearError() {
      this.error = null
    }
  }
})
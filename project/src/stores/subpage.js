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
      return state.subpages.filter(sp => sp.conference_id === conferenceId)
    },
    getSubpageById: (state) => (id) => {
      return state.subpages.find(sp => sp.id === id)
    }
  },
  
  actions: {
    async fetchSubpages(conferenceId) {
      if (!conferenceId) {
        this.error = 'Conference ID is required'
        return []
      }

      this.loading = true
      this.error = null
      
      const authStore = useAuthStore()
      
      try {
        const response = await axios.get(
          `${API_URL}/conferences/${conferenceId}/subpages`,
          { headers: authStore.authHeader }
        )
        this.subpages = response.data
        return response.data
      } catch (error) {
        console.error('Error fetching subpages:', error)
        this.error = error.response?.data?.message || 'Failed to fetch subpages'
        return []
      } finally {
        this.loading = false
      }
    },
    
    async fetchSubpage(conferenceId, subpageId) {
      if (!conferenceId) {
        this.error = 'Conference ID is required'
        return null
      }

      if (subpageId === 'new') {
        return null
      }

      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`${API_URL}/conferences/${conferenceId}/subpages/${subpageId}`)
        this.currentSubpage = response.data
        return response.data
      } catch (error) {
        try {
          const allSubpages = await this.fetchSubpages(conferenceId)
          const subpage = allSubpages.find(sp => sp.slug === subpageId)
          if (subpage) {
            this.currentSubpage = subpage
            return subpage
          }
          throw new Error('Subpage not found')
        } catch (innerError) {
          this.error = innerError.response?.data?.message || 'Failed to fetch subpage'
          throw innerError
        }
      } finally {
        this.loading = false
      }
    },
    
    async createSubpage(conferenceId, subpageData) {
      if (!conferenceId) {
        this.error = 'Conference ID is required'
        return null
      }

      this.loading = true
      this.error = null
      try {
        const data = {
          title: subpageData.title,
          content: subpageData.content,
          order: subpageData.order,
          is_published: subpageData.is_published,
          slug: subpageData.slug
        }

        console.log('Creating subpage with data:', data)
        
        const authStore = useAuthStore()
        
        const response = await axios.post(
          `${API_URL}/conferences/${conferenceId}/subpages`,
          data,
          { headers: authStore.authHeader }
        )
        this.subpages.push(response.data)
        return response.data
      } catch (error) {
        console.error('Error creating subpage:', error)
        this.error = error.response?.data?.message || 'Failed to create subpage'
        throw error
      } finally {
        this.loading = false
      }
    },
    
    async updateSubpage(conferenceId, subpageId, subpageData) {
      if (!conferenceId || !subpageId) {
        this.error = 'Conference ID and Subpage ID are required'
        return null
      }

      this.loading = true
      this.error = null
      
      try {
        const data = {
          title: subpageData.title,
          content: subpageData.content,
          order: subpageData.order,
          is_published: subpageData.is_published,
          slug: subpageData.slug
        }

        console.log('Updating subpage with data:', data)
        
        const response = await axios.put(
          `${API_URL}/conferences/${conferenceId}/subpages/${subpageId}`,
          data,
          {
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          }
        )
        
        this.currentSubpage = response.data
        
        const index = this.subpages.findIndex(sp => sp.id === subpageId)
        if (index !== -1) {
          this.subpages[index] = response.data
        }
        
        return response.data
      } catch (error) {
        console.error('Error updating subpage:', error)
        this.error = error.response?.data?.message || 'Failed to update subpage'
        throw error
      } finally {
        this.loading = false
      }
    },
    
    async deleteSubpage(conferenceId, subpageId) {
      if (!conferenceId || !subpageId) {
        this.error = 'Conference ID and Subpage ID are required'
        return
      }

      this.loading = true
      this.error = null
      try {
        await axios.delete(`${API_URL}/conferences/${conferenceId}/subpages/${subpageId}`)
        this.subpages = this.subpages.filter(sp => sp.id !== subpageId)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete subpage'
        throw error
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
<template>
  <div class="manage-subpages-page">
    <div class="page-header">
      <router-link to="/dashboard" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
      </router-link>
      <h1 class="page-title">Edit Subpages</h1>
    </div>

    <!-- Add Subpage Button -->
    <div class="add-section">
      <router-link :to="{ name: 'edit-subpage', params: { conferenceId: 'new', subpageId: 'new' }}" class="add-button">
        <i class="fas fa-plus"></i> Add Another Subpage
      </router-link>
    </div>

    <!-- Conferences with Subpages -->
    <div v-if="conferences.length === 0" class="empty-state">
      <div class="empty-icon">🎓</div>
      <h3 class="empty-title">No conferences found</h3>
      <p class="empty-description">Create a conference first to add subpages.</p>
      <router-link to="/manage-conference" class="empty-action">
        Add Conference
      </router-link>
    </div>

    <div v-else class="conferences-list">
      <div
        v-for="conference in conferences"
        :key="conference.id"
        class="conference-card"
      >
        <div class="conference-header">
          <h3 class="conference-title">
            {{ conference.title }} ({{ conference.year }})
          </h3>
          <p class="conference-meta">{{ conference.location }} • {{ formatDate(conference.date) }}</p>
        </div>
        
        <div class="conference-content">
          <div v-if="getSubpagesForConference(conference.id).length === 0" class="no-subpages">
            <div class="no-subpages-icon">📄</div>
            <p class="no-subpages-text">No subpages for this conference</p>
            <router-link 
              :to="{ name: 'edit-subpage', params: { conferenceId: conference.id, subpageId: 'new' }}" 
              class="add-subpage-button"
            >
              Add Subpage
            </router-link>
          </div>
          
          <div v-else class="subpages-grid">
            <div
              v-for="subpage in getSubpagesForConference(conference.id)"
              :key="subpage.id"
              class="subpage-card"
            >
              <div class="subpage-header">
                <h4 class="subpage-title">{{ subpage.title }}</h4>
                <span 
                  class="subpage-status" 
                  :class="{ 'active': subpage.is_active, 'inactive': !subpage.is_active }"
                >
                  {{ subpage.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              
              <p class="subpage-content">
                {{ truncateContent(subpage.content) }}
              </p>
              
              <div class="subpage-meta">
                <span class="subpage-order">Order: {{ subpage.order }}</span>
                <span class="subpage-date">{{ formatDate(subpage.updated_at) }}</span>
              </div>
              
              <div class="subpage-actions">
                <router-link 
                  :to="{ name: 'edit-subpage', params: { conferenceId: conference.id, subpageId: subpage.id }}" 
                  class="edit-button"
                >
                  <i class="fas fa-edit"></i> Edit
                </router-link>
                <button 
                  @click="deleteSubpage(subpage.id)" 
                  class="delete-button"
                >
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="message" class="message" :class="messageType">
      {{ message }}
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useConferenceStore } from '../../stores/conference'
import { useSubpageStore } from '../../stores/subpage'

export default defineComponent({
  name: 'ManageSubpagesPage',
  setup() {
    const conferenceStore = useConferenceStore()
    const subpageStore = useSubpageStore()
    
    const message = ref('')
    const messageType = ref('')

    const conferences = computed(() => conferenceStore.conferences || [])
    const subpages = computed(() => subpageStore.subpages || [])

    const getSubpagesForConference = (conferenceId) => {
      return subpages.value.filter(subpage => subpage.conference_id === conferenceId)
    }

    const truncateContent = (content) => {
      if (!content) return ''
      const text = content.replace(/<[^>]*>/g, '') // Remove HTML tags
      return text.length > 100 ? text.substring(0, 100) + '...' : text
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      })
    }

    const deleteSubpage = async (subpageId) => {
      if (!confirm('Are you sure you want to delete this subpage?')) return

      try {
        await subpageStore.deleteSubpage(subpageId)
        message.value = 'Subpage deleted successfully'
        messageType.value = 'success'
        
        setTimeout(() => {
          message.value = ''
        }, 3000)
      } catch (error) {
        message.value = 'Failed to delete subpage'
        messageType.value = 'error'
      }
    }

    onMounted(async () => {
      await conferenceStore.fetchConferences()
      await subpageStore.fetchSubpages()
    })

    return {
      conferences,
      subpages,
      message,
      messageType,
      getSubpagesForConference,
      truncateContent,
      formatDate,
      deleteSubpage
    }
  }
})
</script>

<style scoped>
.manage-subpages-page {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #f9fafb;
  min-height: 100vh;
}

.page-header {
  margin-bottom: 2rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  color: #3b82f6;
  text-decoration: none;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.back-link:hover {
  color: #1d4ed8;
}

.page-title {
  font-size: 3rem;
  font-weight: bold;
  color: #111827;
  margin: 0;
}

.add-section {
  margin-bottom: 2rem;
  text-align: center;
}

.add-button {
  background-color: #8b5cf6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.add-button:hover {
  background-color: #7c3aed;
}

.empty-state {
  text-align: center;
  padding: 3rem;
}

.empty-icon {
  font-size: 6rem;
  margin-bottom: 1rem;
  color: #9ca3af;
}

.empty-title {
  font-size: 1.125rem;
  font-weight: 500;
  color: #111827;
  margin-bottom: 0.5rem;
}

.empty-description {
  color: #6b7280;
  margin-bottom: 1rem;
}

.empty-action {
  background-color: #6366f1;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
}

.conferences-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.conference-card {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.conference-header {
  background-color: #f9fafb;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.conference-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.conference-meta {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.conference-content {
  padding: 1.5rem;
}

.no-subpages {
  text-align: center;
  padding: 2rem;
}

.no-subpages-icon {
  font-size: 3rem;
  margin-bottom: 0.5rem;
  color: #9ca3af;
}

.no-subpages-text {
  color: #6b7280;
  margin-bottom: 1rem;
}

.add-subpage-button {
  background-color: #8b5cf6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  text-decoration: none;
  font-size: 0.875rem;
}

.subpages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.subpage-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  padding: 1rem;
  transition: box-shadow 0.2s;
}

.subpage-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.subpage-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
}

.subpage-title {
  font-weight: 500;
  color: #111827;
  margin: 0;
  font-size: 1rem;
  flex: 1;
  margin-right: 0.5rem;
}

.subpage-status {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-weight: 500;
  text-transform: uppercase;
}

.subpage-status.active {
  background-color: #d1fae5;
  color: #065f46;
}

.subpage-status.inactive {
  background-color: #fee2e2;
  color: #991b1b;
}

.subpage-content {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0.75rem 0;
  line-height: 1.4;
}

.subpage-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem;
  color: #9ca3af;
  margin-bottom: 0.75rem;
}

.subpage-actions {
  display: flex;
  gap: 0.5rem;
}

.edit-button {
  flex: 1;
  background-color: #3b82f6;
  color: white;
  text-align: center;
  padding: 0.375rem 0.5rem;
  border-radius: 0.25rem;
  text-decoration: none;
  font-size: 0.75rem;
  transition: background-color 0.2s;
}

.edit-button:hover {
  background-color: #2563eb;
}

.delete-button {
  flex: 1;
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 0.375rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.delete-button:hover {
  background-color: #dc2626;
}

.message {
  position: fixed;
  top: 1rem;
  right: 1rem;
  padding: 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  z-index: 1000;
}

.message.success {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #10b981;
}

.message.error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #ef4444;
}

@media (max-width: 768px) {
  .manage-subpages-page {
    padding: 1rem;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .subpages-grid {
    grid-template-columns: 1fr;
  }
  
  .conference-header,
  .conference-content {
    padding: 1rem;
  }
}
</style>
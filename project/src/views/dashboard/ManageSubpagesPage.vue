<template>
  <div class="manage-subpages-page">
    <div class="page-header">
      <div class="header-left">
        <router-link
          :to="{ name: 'dashboard' }"
          class="btn btn-outline back-button"
        >
          <i class="fas fa-arrow-left"></i> Back
        </router-link>
        <h1>Manage Subpages</h1>
      </div>
    </div>

    <AlertMessage
      v-if="error"
      type="error"
      :message="error"
      @close="clearError"
    />

    <div class="conference-selector">
      <label for="conference-select" class="form-label">Select Conference</label>
      <select
        id="conference-select"
        v-model="selectedConferenceId"
        class="form-control"
        @change="handleConferenceChange"
      >
        <option value="">Select a conference...</option>
        <option
          v-for="conference in conferences"
          :key="conference.id"
          :value="conference.id"
        >
          {{ conference.title }}
        </option>
      </select>
    </div>

    <div v-if="!selectedConferenceId" class="empty-state">
      <p>Please select a conference to manage its subpages.</p>
    </div>

    <template v-else>
      <div class="page-actions">
        <router-link
          :to="{ name: 'create-subpage', params: { conferenceId: selectedConferenceId } }"
          class="btn btn-primary"
        >
          Create New Subpage
        </router-link>
      </div>

      <div v-if="loading" class="loading-state">
        Loading subpages...
      </div>

      <div v-else-if="subpages.length === 0" class="empty-state">
        <p>No subpages found for this conference.</p>
      </div>

      <div v-else class="subpages-list">
        <div v-for="subpage in subpages" :key="subpage.id" class="subpage-card">
          <div class="subpage-header">
            <h3>{{ subpage.title }}</h3>
            <div class="subpage-actions">
              <router-link
                :to="{ name: 'edit-subpage', params: { conferenceId: selectedConferenceId, subpageId: subpage.id } }"
                class="btn btn-outline"
              >
                Edit
              </router-link>
              <button
                class="btn btn-danger"
                @click="confirmDelete(subpage)"
                :disabled="loading"
              >
                Delete
              </button>
            </div>
          </div>
          <div class="subpage-meta">
            <span class="status" :class="{ 'published': subpage.is_published }">
              {{ subpage.is_published ? 'Published' : 'Draft' }}
            </span>
            <span class="order">Order: {{ subpage.order }}</span>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useSubpageStore } from '../../stores/subpage'
import { useConferenceStore } from '../../stores/conference'
import AlertMessage from '../../components/shared/AlertMessage.vue'

export default {
  name: 'ManageSubpagesPage',
  components: {
    AlertMessage
  },
  data() {
    return {
      loading: false,
      error: null,
      selectedConferenceId: ''
    }
  },
  computed: {
    ...mapState(useSubpageStore, ['subpages']),
    ...mapState(useConferenceStore, ['conferences']),
    
    conferenceId() {
      return this.$route.params.conferenceId
    }
  },
  methods: {
    ...mapActions(useSubpageStore, [
      'fetchSubpages',
      'deleteSubpage',
      'clearError'
    ]),
    ...mapActions(useConferenceStore, ['fetchConferences']),
    
    async handleConferenceChange() {
      if (this.selectedConferenceId) {
        await this.loadData()
      }
    },
    
    async loadData() {
      if (!this.selectedConferenceId) return
      
      this.loading = true
      this.error = null
      
      try {
        await this.fetchSubpages(this.selectedConferenceId)
      } catch (error) {
        this.error = error.message || 'Failed to load subpages'
      } finally {
        this.loading = false
      }
    },
    
    async confirmDelete(subpage) {
      if (!confirm(`Are you sure you want to delete "${subpage.title}"?`)) {
        return
      }
      
      this.loading = true
      this.error = null
      
      try {
        await this.deleteSubpage(this.selectedConferenceId, subpage.id)
        await this.fetchSubpages(this.selectedConferenceId)
      } catch (error) {
        this.error = error.message || 'Failed to delete subpage'
      } finally {
        this.loading = false
      }
    },
    
    clearError() {
      this.error = null
    }
  },
  async mounted() {
    try {
      await this.fetchConferences()
      if (this.conferenceId) {
        if (this.conferenceId === 'new') {
          
          if (this.conferences.length > 0) {
            this.selectedConferenceId = this.conferences[0].id
          }
        } else if (this.conferenceId === 'all') {
         
          this.selectedConferenceId = ''
        } else {
         
          this.selectedConferenceId = this.conferenceId
        }
        await this.loadData()
      }
    } catch (error) {
      this.error = error.message || 'Failed to load conferences'
    }
  }
}
</script>

<style scoped>
.manage-subpages-page {
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-lg);
}

.header-left {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
}

.back-button {
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
  padding: 0.5rem 1rem;
}

.back-button i {
  font-size: 0.9rem;
}

.subpages-list {
  display: grid;
  gap: var(--spacing-md);
  margin-top: var(--spacing-lg);
}

.subpage-card {
  background: white;
  border-radius: 8px;
  padding: var(--spacing-md);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.subpage-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-sm);
}

.subpage-header h3 {
  margin: 0;
  font-size: 1.2rem;
}

.subpage-actions {
  display: flex;
  gap: var(--spacing-sm);
}

.subpage-meta {
  display: flex;
  gap: var(--spacing-md);
  color: var(--color-text-secondary);
  font-size: 0.9rem;
}

.status {
  padding: 2px 8px;
  border-radius: 12px;
  background: var(--color-gray-light);
}

.status.published {
  background: var(--color-success-light);
  color: var(--color-success);
}

.loading-state,
.empty-state {
  text-align: center;
  padding: var(--spacing-xl);
  color: var(--color-text-secondary);
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background-color: var(--color-primary);
  color: white;
  border: none;
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--color-primary-dark);
}

.btn-outline {
  background-color: transparent;
  border: 1px solid var(--color-primary);
  color: var(--color-primary);
}

.btn-outline:hover {
  background-color: var(--color-primary);
  color: white;
}

.btn-danger {
  background-color: red;
  border: 1px solid red;
  color: black;
}


.btn-danger:hover:not(:disabled) {
  background-color: darkred;
}

.conference-selector {
  margin-bottom: var(--spacing-lg);
  max-width: 400px;
}

.form-label {
  display: block;
  margin-bottom: var(--spacing-xs);
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid var(--color-border);
  border-radius: 4px;
  font-size: 1rem;
}

.form-control:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 2px var(--color-primary-light);
}
</style>
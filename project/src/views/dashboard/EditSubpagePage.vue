<template>
  <div class="edit-subpage-page">
    <div class="page-header">
      <div class="header-left">
        <router-link
          :to="{ name: 'manage-subpages' }"
          class="btn btn-outline back-button"
        >
          <i class="fas fa-arrow-left"></i> Back
        </router-link>
        <h1>{{ isEditing ? 'Edit Subpage' : 'Create New Subpage' }}</h1>
      </div>
    </div>
    
    <AlertMessage
      v-if="error"
      type="error"
      :message="error"
      @close="clearError"
    />

    <div v-if="!selectedConferenceId" class="conference-selector">
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

    <div v-else class="card">
      <form @submit.prevent="handleSubmit" class="subpage-form">
        <div class="form-group">
          <label for="title" class="form-label">Title</label>
          <input
            type="text"
            id="title"
            v-model="form.title"
            class="form-control"
            required
            :disabled="loading"
          />
        </div>
        
        <div class="form-group">
          <label for="slug" class="form-label">Slug</label>
          <input
            type="text"
            id="slug"
            v-model="form.slug"
            class="form-control"
            required
            :disabled="loading"
            placeholder="e.g., about-us"
          />
          <span class="form-help">URL-friendly version of the title (e.g., about-us, contact)</span>
        </div>
        
        <div class="form-group">
          <label class="form-label">Content</label>
          <WysiwygEditor
            :value="form.content"
            @update:value="updateContent"
            :conference-id="String(selectedConferenceId)"
            :subpage-id="subpageId || 'temp'"
            :disabled="loading"
          />
        </div>
        
        <div class="form-group">
          <label class="form-label">Order</label>
          <input
            type="number"
            v-model.number="form.order"
            class="form-control"
            min="0"
            :disabled="loading"
          />
          <span class="form-help">Lower numbers appear first in navigation</span>
        </div>
        
        <div class="form-check">
          <input
            type="checkbox"
            id="isPublished"
            v-model="form.isPublished"
            :disabled="loading"
          />
          <label for="isPublished">Publish this subpage</label>
        </div>
        
        <div class="form-actions">
          <button 
            type="submit" 
            class="btn btn-primary" 
            :disabled="loading || !isFormValid"
            @click="handleSubmit"
          >
            {{ isEditing ? 'Update Subpage' : 'Create Subpage' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useSubpageStore } from '../../stores/subpage'
import { useConferenceStore } from '../../stores/conference'
import WysiwygEditor from '../../components/shared/WysiwygEditor.vue'
import AlertMessage from '../../components/shared/AlertMessage.vue'

export default {
  name: 'EditSubpagePage',
  components: {
    WysiwygEditor,
    AlertMessage
  },
  data() {
    return {
      form: {
        title: '',
        content: '',
        order: 0,
        isPublished: true,
        slug: ''
      },
      loading: false,
      error: null,
      selectedConferenceId: ''
    }
  },
  computed: {
    ...mapState(useSubpageStore, ['currentSubpage']),
    ...mapState(useConferenceStore, ['conferences']),
    
    conferenceId() {
      return this.$route.params.conferenceId
    },
    
    subpageId() {
      return this.$route.params.subpageId
    },
    
    isEditing() {
      return this.subpageId && this.subpageId !== 'new'
    },
    
    isFormValid() {
      const isValid = this.form.title && 
             this.form.content &&
             this.form.slug &&
             this.selectedConferenceId
      console.log('Form validation:', {
        title: this.form.title,
        content: this.form.content,
        slug: this.form.slug,
        conferenceId: this.selectedConferenceId,
        isValid
      })
      return isValid
    }
  },
  methods: {
    ...mapActions(useSubpageStore, [
      'fetchSubpage',
      'createSubpage',
      'updateSubpage',
      'clearError'
    ]),
    ...mapActions(useConferenceStore, ['fetchConferences']),
    
    updateContent(content) {
      this.form.content = content
    },
    
    async handleConferenceChange() {
      if (this.isEditing && this.selectedConferenceId) {
        await this.loadSubpageData()
      }
    },
    
    async loadSubpageData() {
      if (!this.selectedConferenceId || !this.isEditing) return
      
      this.loading = true
      try {
        const subpage = await this.fetchSubpage(this.selectedConferenceId, this.subpageId)
        if (subpage) {
          console.log('Loaded subpage data:', subpage)
          this.form = {
            title: subpage.title || '',
            content: subpage.content || '',
            order: subpage.order || 0,
            isPublished: subpage.is_published || false,
            slug: subpage.slug || ''
          }
        }
      } catch (error) {
        console.error('Error loading subpage:', error)
        this.error = 'Failed to load subpage data'
      } finally {
        this.loading = false
      }
    },
    
    async handleSubmit(event) {
      console.log('Form submitted', {
        event,
        form: this.form,
        isValid: this.isFormValid
      })
      
      if (!this.isFormValid) {
        console.log('Form is not valid')
        return
      }
      
      this.loading = true
      this.error = null
      
      try {
        const slug = this.form.slug.trim().toLowerCase().replace(/\s+/g, '-')
        
        const subpageData = {
          title: this.form.title.trim(),
          content: this.form.content,
          order: this.form.order,
          is_published: this.form.isPublished,
          slug: slug
        }
        
        console.log('Saving subpage with data:', subpageData)
        
        let savedSubpage
        if (this.isEditing) {
          console.log('Updating existing subpage:', this.subpageId)
          savedSubpage = await this.updateSubpage(this.selectedConferenceId, this.subpageId, subpageData)
        } else {
          console.log('Creating new subpage')
          savedSubpage = await this.createSubpage(this.selectedConferenceId, subpageData)
        }
        
        console.log('Subpage saved successfully:', savedSubpage)
        
        this.$router.push({
          name: 'manage-subpages',
          params: { conferenceId: this.selectedConferenceId }
        })
      } catch (error) {
        console.error('Error saving subpage:', error)
        this.error = error.message || 'Failed to save subpage'
      } finally {
        this.loading = false
      }
    },
    
    clearError() {
      this.error = null
    }
  },
  async mounted() {
    console.log('Component mounted', {
      conferenceId: this.conferenceId,
      subpageId: this.subpageId,
      isEditing: this.isEditing
    })
    
    try {
      await this.fetchConferences()
      if (this.conferenceId && this.conferenceId !== 'list') {
        this.selectedConferenceId = this.conferenceId
        if (this.isEditing && this.subpageId !== 'list') {
          await this.loadSubpageData()
        }
      }
    } catch (error) {
      this.error = error.message || 'Failed to load conferences'
    }
  }
}
</script>

<style scoped>
.edit-subpage-page {
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

.subpage-form {
  max-width: 800px;
}

.form-help {
  display: block;
  font-size: 0.85rem;
  color: var(--color-text-secondary);
  margin-top: var(--spacing-xs);
}

.form-check {
  display: flex;
  align-items: center;
  margin-bottom: var(--spacing-lg);
}

.form-check input {
  margin-right: var(--spacing-sm);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: var(--spacing-lg);
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
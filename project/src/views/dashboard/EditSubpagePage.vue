<template>
  <div class="edit-subpage-page">
    <div class="page-header">
      <h1>{{ isEditing ? 'Edit Subpage' : 'Create New Subpage' }}</h1>
      <div class="page-actions">
        <router-link
          :to="{ name: 'manage-subpages', params: { conferenceId } }"
          class="btn btn-outline"
        >
          Cancel
        </router-link>
      </div>
    </div>
    
    <AlertMessage
      v-if="error"
      type="error"
      :message="error"
      @close="clearError"
    />
    
    <div class="card">
      <form @submit.prevent="saveSubpage" class="subpage-form">
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
          <label for="slug" class="form-label">
            Slug 
            <span class="form-help">(URL-friendly identifier)</span>
          </label>
          <div class="slug-input">
            <input
              type="text"
              id="slug"
              v-model="form.slug"
              class="form-control"
              required
              :disabled="loading"
            />
            <button
              type="button"
              class="generate-slug"
              @click="generateSlug"
              :disabled="!form.title || loading"
            >
              Generate from title
            </button>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Content</label>
          <WysiwygEditor
            :value="form.content"
            @update:value="updateContent"
            :conference-id="conferenceId"
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
          <button type="submit" class="btn btn-primary" :disabled="loading || !isFormValid">
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
        slug: '',
        content: '',
        order: 0,
        isPublished: true
      },
      loading: false,
      error: null
    }
  },
  computed: {
    ...mapState(useSubpageStore, ['currentSubpage']),
    ...mapState(useConferenceStore, ['currentConference']),
    
    conferenceId() {
      return this.$route.params.conferenceId
    },
    
    subpageId() {
      return this.$route.params.subpageId
    },
    
    isEditing() {
      return !!this.subpageId
    },
    
    isFormValid() {
      return this.form.title && 
             this.form.slug && 
             this.form.content
    }
  },
  methods: {
    ...mapActions(useSubpageStore, [
      'fetchSubpage',
      'createSubpage',
      'updateSubpage',
      'clearError'
    ]),
    ...mapActions(useConferenceStore, ['fetchConferenceById']),
    
    updateContent(content) {
      this.form.content = content
    },
    
    generateSlug() {
      if (!this.form.title) return
      
      // Convert title to lowercase, replace spaces with hyphens, remove special chars
      const slug = this.form.title
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '')
      
      this.form.slug = slug
    },
    
    async saveSubpage() {
      if (!this.isFormValid) return
      
      this.loading = true
      this.error = null
      
      try {
        const subpageData = { ...this.form }
        
        if (this.isEditing) {
          await this.updateSubpage(this.conferenceId, this.subpageId, subpageData)
        } else {
          await this.createSubpage(this.conferenceId, subpageData)
        }
        
        // Navigate back to subpage management
        this.$router.push({
          name: 'manage-subpages',
          params: { conferenceId: this.conferenceId }
        })
      } catch (error) {
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
    // Load conference data
    if (!this.currentConference || this.currentConference.id !== this.conferenceId) {
      await this.fetchConferenceById(this.conferenceId)
    }
    
    // If editing, load subpage data
    if (this.isEditing) {
      this.loading = true
      try {
        const subpage = await this.fetchSubpage(this.conferenceId, this.subpageId)
        if (subpage) {
          this.form = {
            title: subpage.title,
            slug: subpage.slug,
            content: subpage.content,
            order: subpage.order,
            isPublished: subpage.isPublished
          }
        }
      } catch (error) {
        this.error = 'Failed to load subpage data'
      } finally {
        this.loading = false
      }
    }
  },
  beforeUnmount() {
    // Clear any existing error
    this.clearError()
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

.slug-input {
  display: flex;
  gap: var(--spacing-md);
}

.generate-slug {
  background-color: var(--color-secondary);
  color: white;
  border: none;
  border-radius: var(--border-radius-sm);
  padding: 0 var(--spacing-md);
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color var(--transition-fast);
}

.generate-slug:hover {
  background-color: var(--color-secondary-light);
}

.generate-slug:disabled {
  background-color: var(--color-text-secondary);
  cursor: not-allowed;
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
}
</style>
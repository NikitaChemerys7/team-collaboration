<template>
  <div class="dashboard-page">
    <h1 class="dashboard-title">Dashboard</h1>

    <div class="user-info-card">
      <p class="user-info-text">Current user: <span class="user-name">{{ user?.name || 'Admin User' }}</span></p>
      <p class="user-info-text">Role: <span class="user-role">{{ user?.role || 'admin' }}</span></p>
    </div>
    <h2>Pages</h2>
    <div class="dashboard-grid">
      <router-link to="/manage-conference" class="dashboard-card conference-card" v-if="isAdmin">
        <div class="card-content">
          <div class="card-icon graduation-icon">🎓</div>
          <h3 class="card-title">Manage Conferences</h3>
          <p class="card-description">Add, edit or delete conferences</p>
        </div>
      </router-link>

      <router-link to="/dashboard/conferences/new/subpages" class="dashboard-card subpage-card">
        <div class="card-content">
          <div class="card-icon document-icon">📄</div>
          <h3 class="card-title">Add Subpage</h3>
          <p class="card-description">Add a subpage to a conference</p>
        </div>
      </router-link>

      <router-link to="/dashboard/conferences/all/subpages" class="dashboard-card edit-card">
        <div class="card-content">
          <div class="card-icon edit-icon">✏️</div>
          <h3 class="card-title">Edit Subpages</h3>
          <p class="card-description">Edit or delete conference subpages</p>
        </div>
      </router-link>
    </div>
    
    <hr v-if="isAdmin">
    <h2 v-if="isAdmin">Users</h2>
    <div class="dashboard-grid" v-if="isAdmin">
      <router-link to="/dashboard/users" class="dashboard-card users-card">
        <div class="card-content">
          <div class="card-icon settings-icon">⚙️</div>
          <h3 class="card-title">Manage Users</h3>
          <p class="card-description">Manage user accounts and permissions</p>
        </div>
      </router-link>

      <!-- <router-link to="/manage-users?type=editor" class="dashboard-card editor-card">
        <div class="card-content">
          <div class="card-icon users-icon">👥</div>
          <h3 class="card-title">Add Editor</h3>
          <p class="card-description">Add a new content editor</p>
        </div>
      </router-link>

      <router-link to="/manage-users?type=admin" class="dashboard-card admin-card">
        <div class="card-content">
          <div class="card-icon admin-icon">🛡️</div>
          <h3 class="card-title">Add Admin</h3>
          <p class="card-description">Add a new administrator</p>
        </div>
      </router-link> -->
    </div>

    <hr>
    <h2>Other</h2>
    <div class="dashboard-grid"></div>
    <router-link to="/dashboard/mail" class="dashboard-card mail-card" v-if="isAdmin">
      <div class="card-content">
        <div class="card-icon mail-icon">✉️</div>
        <h3 class="card-title">Send Mail</h3>
        <p class="card-description">Send email messages to users and editors</p>
      </div>
    </router-link>

    <div class="dashboard-card stats-card">
      <div class="card-content">
        <div class="card-icon stats-icon">📊</div>
        <h3 class="card-title stats-title">Statistics</h3>
        <div class="stats-list">
          <div class="stat-item">
            <span class="stat-label">Conferences:</span>
            <span class="stat-value">{{ stats.conferences || 0 }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Subpages:</span>
            <span class="stat-value">{{ stats.subpages || 0 }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Total Users:</span>
            <span class="stat-value">{{ stats.users || 0 }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Editors:</span>
            <span class="stat-value">{{ stats.editors || 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Documents Section -->
    <div class="card mb-6">
      <h2 class="text-xl font-semibold mb-4">Documents</h2>
      <div class="mb-4">
        <div class="upload-form">
          <div class="file-input-container">
            <input
              type="file"
              ref="fileInput"
              @change="onFileSelected"
              accept=".pdf,.doc,.docx"
              class="file-input"
            />
            <span class="selected-file" v-if="selectedFile">{{ selectedFile.name }}</span>
          </div>
          <button
            type="button"
            class="btn btn-primary"
            :disabled="uploading || !selectedFile"
            @click="handleFileUpload"
          >
            {{ uploading ? 'Uploading...' : 'Upload Document' }}
          </button>
        </div>
      </div>
      
      <div class="documents-list">
        <div v-if="documents.length === 0" class="text-gray-500">
          No documents uploaded yet.
        </div>
        <div v-else class="space-y-2">
          <div v-for="doc in documents" :key="doc.id" class="document-item">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
              <div class="flex items-center">
                <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                <a :href="getDocumentUrl(doc.url)" target="_blank" class="text-blue-600 hover:underline">
                  {{ doc.name }}
                </a>
              </div>
              <div class="flex gap-2">
                <button
                  @click="copyDocumentUrl(doc.url)"
                  class="btn btn-secondary"
                  :title="'Copy relative URL'"
                >
                  Copy URL
                </button>
                <button
                  @click="deleteDocument(doc.id)"
                  class="btn btn-danger"
                  :disabled="deleting === doc.id"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useConferenceStore } from '../../stores/conference'
import { useSubpageStore } from '../../stores/subpage'
import { useUserStore } from '../../stores/user'
import axios from 'axios'
import { API_URL } from '../../config'

export default defineComponent({
  name: 'DashboardPage',
  setup() {
    const authStore = useAuthStore()
    const conferenceStore = useConferenceStore()
    const subpageStore = useSubpageStore()
    const userStore = useUserStore()

    const stats = ref({
      conferences: 0,
      subpages: 0,
      users: 0,
      editors: 0
    })

    const documents = ref([])
    const uploading = ref(false)
    const deleting = ref(null)
    const fileInput = ref(null)
    const selectedFile = ref(null)

    const loadStats = async () => {
      try {
        await conferenceStore.fetchConferences()
        stats.value.conferences = conferenceStore.conferences.length

        await subpageStore.fetchSubpages()
        stats.value.subpages = subpageStore.subpages.length

        if (authStore.isAdmin) {
          await userStore.fetchUsers()
          stats.value.users = userStore.users.length
          stats.value.editors = userStore.users.filter(user => user.role === 'editor').length
        }
      } catch (error) {
        console.error('Error loading stats:', error)
      }
    }

    async function fetchDocuments() {
      try {
        const response = await axios.get(`${API_URL}/documents`)
        documents.value = response.data
      } catch (error) {
        console.error('Error fetching documents:', error)
      }
    }

    async function handleFileUpload() {
      console.log('handleFileUpload called')
      if (!selectedFile.value) {
        console.log('No file selected')
        return
      }

      console.log('Starting upload for file:', selectedFile.value.name)
      uploading.value = true
      const formData = new FormData()
      formData.append('file', selectedFile.value)
      formData.append('name', selectedFile.value.name)

      try {
        console.log('Sending upload request to:', `${API_URL}/documents`)
        const response = await axios.post(`${API_URL}/documents`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        console.log('Upload successful:', response.data)
        documents.value.push(response.data)
        selectedFile.value = null
        if (fileInput.value) {
          fileInput.value.value = null
        }
      } catch (error) {
        console.error('Error uploading document:', error)
        alert('Failed to upload document')
      } finally {
        uploading.value = false
      }
    }

    function onFileSelected(event) {
      console.log('File selected:', event.target.files[0])
      selectedFile.value = event.target.files[0]
      console.log('selectedFile ref updated:', selectedFile.value)
    }

    async function deleteDocument(id) {
      if (!confirm('Are you sure you want to delete this document?')) return

      deleting.value = id
      try {
        await axios.delete(`${API_URL}/documents/${id}`)
        documents.value = documents.value.filter(doc => doc.id !== id)
      } catch (error) {
        console.error('Error deleting document:', error)
        alert('Failed to delete document')
      } finally {
        deleting.value = null
      }
    }

    function getDocumentUrl(url) {
      return `${API_URL.replace('/api', '')}${url}`
    }

    async function copyDocumentUrl(url) {
      try {
        await navigator.clipboard.writeText(url)
        alert('URL copied to clipboard!')
      } catch (err) {
        console.error('Failed to copy URL:', err)
        alert('Failed to copy URL')
      }
    }

    onMounted(async () => {
      if (!authStore.user) {
        await authStore.fetchCurrentUser()
      }
      await loadStats()
      await fetchDocuments()
    })

    return {
      user: authStore.user,
      isAdmin: authStore.isAdmin,
      isEditor: authStore.isEditor,
      stats,
      documents,
      uploading,
      deleting,
      fileInput,
      selectedFile,
      onFileSelected,
      handleFileUpload,
      deleteDocument,
      copyDocumentUrl,
      getDocumentUrl,
      API_URL
    }
  }
})
</script>

<style scoped>
hr {
  margin: 3rem 0;
}

.dashboard-page {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #f9fafb;
  min-height: 100vh;
}

.dashboard-title {
  font-size: 3rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 2rem;
}

.user-info-card {
  background-color: white;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
  padding: 1rem;
  margin-bottom: 2rem;
}

.user-info-text {
  margin: 0.25rem 0;
  color: #6b7280;
}

.user-name,
.user-role {
  font-weight: 600;
  color: #111827;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.dashboard-card {
  background-color: white;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
  text-decoration: none;
  color: inherit;
  transition: all 0.2s ease-in-out;
  cursor: pointer;
  display: block;
}

.dashboard-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.card-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.graduation-icon {
  color: #6366f1;
  /* Indigo */
}

.document-icon {
  color: #8b5cf6;
  /* Purple */
}

.users-icon {
  color: #10b981;
  /* Green */
}

.admin-icon {
  color: white;
}

.edit-icon {
  color: #f59e0b;
  /* Orange */
}

.stats-icon {
  color: #6b7280;
  /* Gray */
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.5rem 0;
}

.card-description {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

/* Specific card styles */
/* .admin-card {
  background-color: #3b82f6;
  color: white;
}

.admin-card .card-title {
  color: white;
}

.admin-card .card-description {
  color: #bfdbfe;
} */

.stats-card {
  background-color: #f3f4f6;
}

.stats-title {
  font-size: 1.125rem;
  color: #111827;
  margin-bottom: 0.75rem;
}

.stats-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  width: 100%;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
}

.stat-label {
  color: #6b7280;
}

.stat-value {
  font-weight: 600;
  color: #111827;
}

/* Responsive design */
@media (max-width: 768px) {
  .dashboard-page {
    padding: 1rem;
  }

  .dashboard-title {
    font-size: 2rem;
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .card-icon {
    font-size: 3rem;
  }
}

@media (min-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.settings-icon {
  color: #64748b; /* Slate */
}

.document-item {
  transition: all 0.2s ease;
}

.document-item:hover {
  transform: translateY(-1px);
}

.document-item button {
  opacity: 0.5;
  transition: opacity 0.2s ease;
}

.document-item:hover button {
  opacity: 1;
}

.upload-form {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.file-input-container {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.file-input {
  border: 1px solid #e5e7eb;
  padding: 0.5rem;
  border-radius: 0.375rem;
  width: 100%;
}

.selected-file {
  color: #6b7280;
  font-size: 0.875rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
  border: none;
}

.btn-primary:hover:not(:disabled) {
  background-color: #2563eb;
}

.btn-primary:disabled {
  background-color: #93c5fd;
  cursor: not-allowed;
}

.btn-danger {
  background-color: #dc2626;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-danger:hover:not(:disabled) {
  background-color: #b91c1c;
}

.btn-danger:disabled {
  background-color: #fca5a5;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #6b7280;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-secondary:hover:not(:disabled) {
  background-color: #4b5563;
}

.btn-secondary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.gap-2 {
  gap: 0.5rem;
}
</style>
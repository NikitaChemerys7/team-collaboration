<template>
  <div class="dashboard-page">
<<<<<<< HEAD
    <h1 class="dashboard-title">Dashboard</h1>

    <!-- User Info Card -->
    <div class="user-info-card">
      <p class="user-info-text">Current user: <span class="user-name">{{ user?.name || 'Admin User' }}</span></p>
      <p class="user-info-text">Role: <span class="user-role">{{ user?.role || 'admin' }}</span></p>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
      <!-- Add Conference -->
      <router-link to="/manage-conference" class="dashboard-card conference-card">
        <div class="card-content">
          <div class="card-icon graduation-icon">🎓</div>
          <h3 class="card-title">Add Conference</h3>
          <p class="card-description">Add a new conference year</p>
        </div>
      </router-link>

      <!-- Add Subpage -->
      <router-link to="/dashboard/conferences/new/subpages" class="dashboard-card subpage-card">
        <div class="card-content">
          <div class="card-icon document-icon">📄</div>
          <h3 class="card-title">Add Subpage</h3>
          <p class="card-description">Add a subpage to a conference</p>
        </div>
      </router-link>

      <!-- Add Editor -->
      <router-link to="/manage-users?type=editor" class="dashboard-card editor-card" v-if="isAdmin">
        <div class="card-content">
          <div class="card-icon users-icon">👥</div>
          <h3 class="card-title">Add Editor</h3>
          <p class="card-description">Add a new content editor</p>
        </div>
      </router-link>

      <!-- Add Admin -->
      <router-link to="/manage-users?type=admin" class="dashboard-card admin-card" v-if="isAdmin">
        <div class="card-content">
          <div class="card-icon admin-icon">🛡️</div>
          <h3 class="card-title">Add Admin</h3>
          <p class="card-description">Add a new administrator</p>
        </div>
      </router-link>

      <!-- Edit Subpages -->
      <router-link to="/dashboard/conferences/all/subpages" class="dashboard-card edit-card">
        <div class="card-content">
          <div class="card-icon edit-icon">✏️</div>
          <h3 class="card-title">Edit Subpages</h3>
          <p class="card-description">Edit or delete conference subpages</p>
        </div>
      </router-link>

      <!-- Statistics Card -->
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
=======
    <h1>Dashboard</h1>

   <div class="debug-info" v-if="user">
      <p>Current user: {{ user.name }}</p>
      <p>Role: {{ user.role }}</p>
    </div>

    <div class="dashboard-grid">
      
      <template v-if="isAdmin">
        <router-link to="/manage-users" class="dashboard-card">
          <div class="card-content">
            <div class="card-icon">👥</div>
            <h3>Manage Users</h3>
            <p>Add, edit, or remove system users</p>
          </div>
        </router-link>

        <router-link to="/manage-conference" class="dashboard-card">
          <div class="card-content">
            <div class="card-icon">🎓</div>
            <h3>Manage Conferences</h3>
            <p>Create and manage conference details</p>
          </div>
        </router-link>
      </template>

      <template v-if="isAdmin || isEditor">
        <router-link :to="{ name: 'manage-subpages', params: { conferenceId: 'new' }}" class="dashboard-card">
          <div class="card-content">
            <div class="card-icon">📄</div>
            <h3>Manage Subpages</h3>
            <p>Create and organize conference subpages</p>
          </div>
        </router-link>

        <router-link :to="{ name: 'edit-subpage', params: { conferenceId: 'new', subpageId: 'new' }}" class="dashboard-card">
          <div class="card-content">
            <div class="card-icon">✏️</div>
            <h3>Edit Subpages</h3>
            <p>Modify existing subpage content</p>
          </div>
        </router-link>
      </template>
>>>>>>> settings-update-3
    </div>
  </div>
</template>

<script>
<<<<<<< HEAD
import { defineComponent, ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useConferenceStore } from '../../stores/conference'
import { useSubpageStore } from '../../stores/subpage'
import { useUserStore } from '../../stores/user'
=======
import { defineComponent } from 'vue'
import { useAuthStore } from '../../stores/auth'
>>>>>>> settings-update-3

export default defineComponent({
  name: 'DashboardPage',
  setup() {
<<<<<<< HEAD
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

    const loadStats = async () => {
      try {
        // Load conferences
        await conferenceStore.fetchConferences()
        stats.value.conferences = conferenceStore.conferences.length

        // Load subpages
        await subpageStore.fetchSubpages()
        stats.value.subpages = subpageStore.subpages.length

        // Load users if admin
        if (authStore.isAdmin) {
          await userStore.fetchUsers()
          stats.value.users = userStore.users.length
          stats.value.editors = userStore.users.filter(user => user.role === 'editor').length
        }
      } catch (error) {
        console.error('Error loading stats:', error)
      }
    }

    onMounted(async () => {
      if (!authStore.user) {
        await authStore.fetchCurrentUser()
      }
      await loadStats()
    })

    return {
      user: authStore.user,
      isAdmin: authStore.isAdmin,
      isEditor: authStore.isEditor,
      stats
=======
    const store = useAuthStore()

    return {
      user: store.user,
      isAdmin: store.isAdmin,
      isEditor: store.isEditor
    }
  },
  async created() {
    const store = useAuthStore()
    if (!store.user) {
      await store.fetchCurrentUser()
>>>>>>> settings-update-3
    }
  }
})
</script>

<style scoped>
.dashboard-page {
<<<<<<< HEAD
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

.user-name, .user-role {
  font-weight: 600;
  color: #111827;
=======
  padding: var(--spacing-lg);
max-width: 1200px;
  margin: 0 auto;
}

.debug-info {
  background-color: var(--color-surface);
  padding: var(--spacing-md);
  border-radius: var(--border-radius-md);
  margin-bottom: var(--spacing-lg);
  border: 1px solid var(--color-border);
}

.debug-info p {
  margin: 0.5rem 0;
  color: var(--color-text-secondary);
>>>>>>> settings-update-3
}

.dashboard-grid {
  display: grid;
<<<<<<< HEAD
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
=======
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--spacing-lg);
  margin-top: var(--spacing-xl);
}

.dashboard-card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
  padding: var(--spacing-lg);
  text-decoration: none;
  color: var(--color-text);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: 1px solid var(--color-border);
>>>>>>> settings-update-3
}

.dashboard-card:hover {
  transform: translateY(-2px);
<<<<<<< HEAD
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
=======
  box-shadow: var(--shadow-lg);
  border-color: var(--color-primary);
>>>>>>> settings-update-3
}

.card-content {
  display: flex;
  flex-direction: column;
<<<<<<< HEAD
=======
  gap: var(--spacing-sm);
>>>>>>> settings-update-3
  align-items: center;
  text-align: center;
}

.card-icon {
<<<<<<< HEAD
  font-size: 4rem;
  margin-bottom: 1rem;
}

.graduation-icon {
  color: #6366f1; /* Indigo */
}

.document-icon {
  color: #8b5cf6; /* Purple */
}

.users-icon {
  color: #10b981; /* Green */
}

.admin-icon {
  color: white;
}

.edit-icon {
  color: #f59e0b; /* Orange */
}

.stats-icon {
  color: #6b7280; /* Gray */
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
.admin-card {
  background-color: #3b82f6;
  color: white;
}

.admin-card .card-title {
  color: white;
}

.admin-card .card-description {
  color: #bfdbfe;
}

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
=======
  font-size: 2rem;
  margin-bottom: var(--spacing-sm);
}

.card-content h3 {
  color: var(--color-primary);
  margin: 0;
  font-size: 1.25rem;
}

.card-content p {
  margin: 0;
  color: var(--color-text-secondary);
  font-size: 0.9rem;
>>>>>>> settings-update-3
}
</style>
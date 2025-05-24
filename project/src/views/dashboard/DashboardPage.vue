<template>
  <div class="dashboard-page">
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
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { useAuthStore } from '../../stores/auth'

export default defineComponent({
  name: 'DashboardPage',
  setup() {
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
    }
  }
})
</script>

<style scoped>
.dashboard-page {
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
}

.dashboard-grid {
  display: grid;
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
}

.dashboard-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--color-primary);
}

.card-content {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
  align-items: center;
  text-align: center;
}

.card-icon {
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
}
</style>
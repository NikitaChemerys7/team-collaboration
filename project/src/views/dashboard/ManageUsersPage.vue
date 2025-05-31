<template>
  <div class="manage-users">
    <div class="page-header">
      <h1 class="text-2xl font-bold">Manage Users</h1>
    </div>

    <div class="card">
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
      </div>
      
      <div v-else-if="error" class="error-message">
        {{ error }}
      </div>

      <div v-else>
        <div class="table-container">
          <table class="users-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <select 
                    v-model="user.role" 
                    class="role-select"
                    @change="updateUserRole(user)"
                    :disabled="isCurrentUser(user)"
                  >
                    <option value="user">User</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                  </select>
                </td>
                <td>
                  <div class="actions">
                    <button 
                      v-if="!isCurrentUser(user)"
                      class="btn btn-danger btn-sm"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const users = ref([])
const loading = ref(true)
const error = ref(null)

const isCurrentUser = (user) => {
  return user.id === authStore.user?.id
}

const fetchUsers = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await authStore.getUsers()
    users.value = response.data
  } catch (err) {
    error.value = 'Failed to load users. Please try again.'
    console.error('Error fetching users:', err)
  } finally {
    loading.value = false
  }
}

const updateUserRole = async (user) => {
  try {
    loading.value = true
    await authStore.updateUser(user.id, { role: user.role })
    await fetchUsers()
  } catch (err) {
    error.value = 'Failed to update user role. Please try again.'
    console.error('Error updating user role:', err)
  } finally {
    loading.value = false
  }
}

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete user ${user.name}?`)) {
    return
  }

  try {
    loading.value = true
    await authStore.deleteUser(user.id)
    await fetchUsers()
  } catch (err) {
    error.value = 'Failed to delete user. Please try again.'
    console.error('Error deleting user:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchUsers)
</script>

<style scoped>
.manage-users {
  padding: var(--spacing-lg);
}

.page-header {
  margin-bottom: var(--spacing-xl);
}

.card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--spacing-lg);
}

.table-container {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: var(--spacing-md);
}

.users-table th,
.users-table td {
  padding: var(--spacing-md);
  text-align: left;
  border-bottom: 1px solid var(--color-border);
}

.users-table th {
  font-weight: 600;
  color: var(--color-text-secondary);
  background-color: var(--color-background);
}

.users-table tr:hover {
  background-color: var(--color-background);
}

.role-select {
  padding: var(--spacing-sm) var(--spacing-md);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius-md);
  background-color: var(--color-surface);
  color: var(--color-text);
  font-size: 0.875rem;
}

.role-select:disabled {
  background-color: var(--color-background);
  cursor: not-allowed;
  opacity: 0.7;
}

.actions {
  display: flex;
  gap: var(--spacing-sm);
}

.btn {
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--border-radius-md);
  font-weight: 500;
  transition: all var(--transition-fast);
  cursor: pointer;
  border: none;
}

.btn-sm {
  padding: var(--spacing-xs) var(--spacing-sm);
  font-size: 0.875rem;
}

.btn-danger {
  background-color: var(--color-error);
  color: white;
  cursor: pointer;
}

.btn-danger:hover {
  background-color: darkred;
  transform: translateY(-1px);
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-message {
  color: var(--color-error);
  text-align: center;
  padding: var(--spacing-lg);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
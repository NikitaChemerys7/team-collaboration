<template>
  <div class="manage-users-page">
    <div class="page-header">
      <router-link to="/dashboard" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
      </router-link>
      <h1 class="page-title">{{ pageTitle }}</h1>
    </div>

    <!-- Add User Form -->
    <div class="form-container">
      <form @submit.prevent="handleSubmit" class="user-form">
        <div class="form-group">
          <label for="name" class="form-label">Name</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            class="form-input"
            :class="{ 'error': errors.name }"
            required
          />
          <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="form-input"
            :class="{ 'error': errors.email }"
            required
          />
          <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            class="form-input"
            :class="{ 'error': errors.password }"
            required
            minlength="6"
          />
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="form.password_confirmation"
            class="form-input"
            :class="{ 'error': errors.password_confirmation }"
            required
          />
          <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</span>
        </div>

        <div class="form-group" v-if="!userType">
          <label for="role" class="form-label">Role</label>
          <select
            id="role"
            v-model="form.role"
            class="form-input"
            :class="{ 'error': errors.role }"
            required
          >
            <option value="">Select role</option>
            <option value="admin">Administrator</option>
            <option value="editor">Editor</option>
          </select>
          <span v-if="errors.role" class="error-message">{{ errors.role }}</span>
        </div>

        <button
          type="submit"
          class="submit-button"
          :class="{ 'admin-button': form.role === 'admin' || userType === 'admin' }"
          :disabled="loading"
        >
          <i class="fas fa-user-plus"></i>
          {{ loading ? 'Adding...' : `Add ${form.role || userType || 'User'}` }}
        </button>
      </form>
    </div>

    <!-- Users List -->
    <div class="users-list" v-if="users.length > 0">
      <h2 class="list-title">{{ userType ? `${userType}s` : 'All Users' }}</h2>
      <div class="users-grid">
        <div
          v-for="user in filteredUsers"
          :key="user.id"
          class="user-card"
          :class="{ 'admin-user': user.role === 'admin' }"
        >
          <div class="user-info">
            <div class="user-details">
              <h3 class="user-name">{{ user.name }}</h3>
              <p class="user-email">{{ user.email }}</p>
              <span class="user-role" :class="`role-${user.role}`">
                {{ user.role }}
              </span>
            </div>
          </div>
          <div class="user-actions">
            <button
              @click="deleteUser(user.id)"
              class="delete-button"
              :disabled="user.id === currentUser?.id"
            >
              <i class="fas fa-trash"></i>
            </button>
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
import { useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useUserStore } from '../../stores/user'

export default defineComponent({
  name: 'ManageUsersPage',
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()
    const userStore = useUserStore()

    const userType = computed(() => route.query.type)
    const pageTitle = computed(() => {
      if (userType.value === 'admin') return 'Add Administrator'
      if (userType.value === 'editor') return 'Add Editor'
      return 'Manage Users'
    })

    const form = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      role: userType.value || ''
    })

    const errors = ref({})
    const loading = ref(false)
    const message = ref('')
    const messageType = ref('')

    const users = computed(() => userStore.users || [])
    const currentUser = computed(() => authStore.user)

    const filteredUsers = computed(() => {
      if (!userType.value) return users.value
      return users.value.filter(user => user.role === userType.value)
    })

    const validateForm = () => {
      errors.value = {}
      
      if (!form.value.name.trim()) {
        errors.value.name = 'Name is required'
      }
      
      if (!form.value.email.trim()) {
        errors.value.email = 'Email is required'
      } else if (!/\S+@\S+\.\S+/.test(form.value.email)) {
        errors.value.email = 'Email is invalid'
      }
      
      if (!form.value.password) {
        errors.value.password = 'Password is required'
      } else if (form.value.password.length < 6) {
        errors.value.password = 'Password must be at least 6 characters'
      }
      
      if (form.value.password !== form.value.password_confirmation) {
        errors.value.password_confirmation = 'Passwords do not match'
      }
      
      if (!form.value.role) {
        errors.value.role = 'Role is required'
      }

      return Object.keys(errors.value).length === 0
    }

    const handleSubmit = async () => {
      if (!validateForm()) return

      loading.value = true
      try {
        await userStore.createUser({
          name: form.value.name,
          email: form.value.email,
          password: form.value.password,
          password_confirmation: form.value.password_confirmation,
          role: form.value.role
        })

        // Reset form
        form.value = {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
          role: userType.value || ''
        }

        message.value = `${form.value.role} added successfully!`
        messageType.value = 'success'

        // Refresh users list
        await userStore.fetchUsers()

        setTimeout(() => {
          message.value = ''
        }, 3000)

      } catch (error) {
        message.value = error.response?.data?.message || 'Failed to add user'
        messageType.value = 'error'
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
      } finally {
        loading.value = false
      }
    }

    const deleteUser = async (userId) => {
      if (!confirm('Are you sure you want to delete this user?')) return

      try {
        await userStore.deleteUser(userId)
        message.value = 'User deleted successfully'
        messageType.value = 'success'
        
        setTimeout(() => {
          message.value = ''
        }, 3000)
      } catch (error) {
        message.value = 'Failed to delete user'
        messageType.value = 'error'
      }
    }

    onMounted(async () => {
      await userStore.fetchUsers()
    })

    return {
      userType,
      pageTitle,
      form,
      errors,
      loading,
      message,
      messageType,
      users,
      currentUser,
      filteredUsers,
      handleSubmit,
      deleteUser
    }
  }
})
</script>

<style scoped>
.manage-users-page {
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

.form-container {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-bottom: 2rem;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.user-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input.error {
  border-color: #ef4444;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.submit-button {
  background-color: #10b981;
  color: white;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.submit-button:hover {
  background-color: #059669;
}

.submit-button.admin-button {
  background-color: #3b82f6;
}

.submit-button.admin-button:hover {
  background-color: #2563eb;
}

.submit-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.users-list {
  margin-top: 2rem;
}

.list-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.users-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.user-card {
  background-color: white;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.user-card.admin-user {
  border-color: #3b82f6;
  background-color: #eff6ff;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.user-name {
  font-weight: 600;
  color: #111827;
  margin: 0;
  font-size: 1rem;
}

.user-email {
  color: #6b7280;
  margin: 0;
  font-size: 0.875rem;
}

.user-role {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-weight: 500;
  text-transform: uppercase;
}

.role-admin {
  background-color: #dbeafe;
  color: #1e40af;
}

.role-editor {
  background-color: #d1fae5;
  color: #065f46;
}

.role-user {
  background-color: #f3f4f6;
  color: #374151;
}

.user-actions {
  display: flex;
  gap: 0.5rem;
}

.delete-button {
  background-color: #ef4444;
  color: white;
  border: none;
  border-radius: 0.25rem;
  padding: 0.5rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.delete-button:hover:not(:disabled) {
  background-color: #dc2626;
}

.delete-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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
  .manage-users-page {
    padding: 1rem;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .form-container {
    padding: 1rem;
  }
  
  .users-grid {
    grid-template-columns: 1fr;
  }
}
</style>
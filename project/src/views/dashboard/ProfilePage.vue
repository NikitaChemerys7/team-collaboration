<template>
  <div class="profile-page">
    <div class="container">
      <div class="header">
        <h1>Profile Settings</h1>
        <div class="role-badge" :class="userRole">
          {{ userRole }}
        </div>
      </div>
      
      <div v-if="message" class="message" :class="messageType">
        {{ message }}
      </div>

      <form @submit.prevent="handleSubmit" class="profile-form">
        <div class="form-group">
          <label for="name" class="form-label">Full Name</label>
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
          <label for="degree" class="form-label">Academic Degree</label>
          <input
            type="text"
            id="degree"
            v-model="form.degree"
            class="form-input"
            :class="{ 'error': errors.degree }"
            placeholder="e.g., Bc., Ing., Mgr., Ph.D."
          />
          <span v-if="errors.degree" class="error-message">{{ errors.degree }}</span>
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
          <label for="current_password" class="form-label">Current Password</label>
          <input
            type="password"
            id="current_password"
            v-model="form.current_password"
            class="form-input"
            :class="{ 'error': errors.current_password }"
            autocomplete="current-password"
          />
          <span v-if="errors.current_password" class="error-message">{{ errors.current_password }}</span>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">New Password</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            class="form-input"
            :class="{ 'error': errors.password }"
            autocomplete="new-password"
          />
          <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm New Password</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="form.password_confirmation"
            class="form-input"
            :class="{ 'error': errors.password_confirmation }"
            autocomplete="new-password"
          />
          <span v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation }}</span>
        </div>

        <button type="submit" class="submit-button" :disabled="loading">
          {{ loading ? 'Saving...' : 'Save Changes' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from 'axios'
import { API_URL } from '../../config'

export default {
  name: 'ProfilePage',
  setup() {
    const authStore = useAuthStore()
    const loading = ref(false)
    const message = ref('')
    const messageType = ref('')
    const errors = ref({})

    const userRole = computed(() => {
      if (!authStore.user) return ''
      return authStore.user.role.charAt(0).toUpperCase() + authStore.user.role.slice(1)
    })

    const form = ref({
      name: '',
      degree: '',
      email: '',
      current_password: '',
      password: '',
      password_confirmation: ''
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
      
      if (form.value.password) {
        if (form.value.password.length < 6) {
          errors.value.password = 'Password must be at least 6 characters'
        }
        
        if (form.value.password !== form.value.password_confirmation) {
          errors.value.password_confirmation = 'Passwords do not match'
        }

        if (!form.value.current_password) {
          errors.value.current_password = 'Current password is required to set a new password'
        }
      }

      return Object.keys(errors.value).length === 0
    }

    const handleSubmit = async () => {
      if (!validateForm()) return

      loading.value = true
      try {
        const dataToSend = {}
        if (form.value.name !== authStore.user.name) dataToSend.name = form.value.name
        if (form.value.degree !== authStore.user.degree) dataToSend.degree = form.value.degree
        if (form.value.email !== authStore.user.email) dataToSend.email = form.value.email
        if (form.value.password) {
          dataToSend.current_password = form.value.current_password
          dataToSend.password = form.value.password
          dataToSend.password_confirmation = form.value.password_confirmation
        }

        const response = await axios.put(`${API_URL}/profile`, dataToSend, {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })

        message.value = 'Profile updated successfully'
        messageType.value = 'success'
        
        authStore.setUser(response.data.user)

        form.value.current_password = ''
        form.value.password = ''
        form.value.password_confirmation = ''

        setTimeout(() => {
          message.value = ''
        }, 3000)

      } catch (error) {
        console.error('Axios error:', error);
        message.value = error.response?.data?.message || 'Failed to update profile'
        messageType.value = 'error'
        
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      if (authStore.user) {
        form.value.name = authStore.user.name
        form.value.degree = authStore.user.degree || ''
        form.value.email = authStore.user.email
      }
    })

    return {
      form,
      errors,
      loading,
      message,
      messageType,
      userRole,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.profile-page {
  padding: var(--spacing-xl) 0;
}

.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 0 var(--spacing-lg);
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--spacing-xl);
}

h1 {
  margin: 0;
  color: var(--color-text);
  font-size: 2rem;
}

.role-badge {
  padding: var(--spacing-xs) var(--spacing-md);
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  text-transform: capitalize;
}

.role-badge.admin {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.role-badge.editor {
  background: var(--color-success-light);
  color: var(--color-success);
}

.role-badge.user {
  background: var(--color-gray-light);
  color: var(--color-gray);
}

.profile-form {
  background: white;
  padding: var(--spacing-xl);
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: var(--spacing-lg);
}

.form-label {
  display: block;
  margin-bottom: var(--spacing-sm);
  color: var(--color-text);
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: var(--spacing-md);
  border: 1px solid var(--color-border);
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
}

.form-input.error {
  border-color: var(--color-error);
}

.error-message {
  color: var(--color-error);
  font-size: 0.875rem;
  margin-top: var(--spacing-xs);
}

.submit-button {
  width: 100%;
  padding: var(--spacing-md);
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-button:hover:not(:disabled) {
  background: var(--color-primary-dark);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.message {
  padding: var(--spacing-md);
  border-radius: 4px;
  margin-bottom: var(--spacing-lg);
  text-align: center;
}

.message.success {
  background: var(--color-success-light);
  color: var(--color-success);
}

.message.error {
  background: var(--color-error-light);
  color: var(--color-error);
}
</style>
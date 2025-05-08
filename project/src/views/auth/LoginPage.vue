<template>
  <div class="login-page">
    <div class="login-container">
      <h1 class="login-title">Welcome Back</h1>
      
      <div v-if="authStore.error" class="error-message">
        {{ authStore.error }}
        <button class="error-close" @click="authStore.clearError">×</button>
      </div>
      
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="form-control"
            placeholder="Enter your email"
            required
            :disabled="authStore.loading"
          />
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input">
            <input
              :type="showPassword ? 'text' : 'password'"
              id="password"
              v-model="password"
              class="form-control"
              placeholder="Enter your password"
              required
              :disabled="authStore.loading"
            />
            <button
              type="button"
              class="toggle-password"
              @click="showPassword = !showPassword"
              :disabled="authStore.loading"
            >
              {{ showPassword ? '👁' : '👁️‍🗨️' }}
            </button>
          </div>
        </div>
        
        <div class="form-actions">
          <button
            type="submit"
            class="btn btn-primary btn-block"
            :disabled="authStore.loading"
          >
            <span v-if="authStore.loading">Logging in...</span>
            <span v-else>Log In</span>
          </button>
        </div>
      </form>
      
      <div class="login-footer">
        <p>
          This login is for administrators and editors only.
          <br />
          If you need access, please contact the system administrator.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'LoginPage',
  data() {
    return {
      email: '',
      password: '',
      showPassword: false
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    }
  },
  methods: {
    async handleLogin() {
      if (!this.email || !this.password) return
      
      await this.authStore.login(this.email, this.password)
    }
  }
}
</script>

<style scoped>
.login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: calc(100vh - 200px);
  background-color: var(--color-background);
}

.login-container {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--spacing-xl);
  width: 100%;
  max-width: 450px;
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.login-title {
  text-align: center;
  color: var(--color-primary);
  margin-bottom: var(--spacing-xl);
}

.error-message {
  background-color: rgba(244, 67, 54, 0.1);
  color: var(--color-error);
  padding: var(--spacing-md);
  border-radius: var(--border-radius-md);
  margin-bottom: var(--spacing-lg);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.error-close {
  background: none;
  border: none;
  color: var(--color-error);
  font-size: 1.2rem;
  cursor: pointer;
}

.login-form {
  margin-bottom: var(--spacing-xl);
}

.form-group {
  margin-bottom: var(--spacing-lg);
}

.password-input {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: var(--spacing-md);
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: var(--spacing-xs);
  color: var(--color-text-secondary);
}

.btn-block {
  width: 100%;
  padding: var(--spacing-md);
}

.login-footer {
  text-align: center;
  color: var(--color-text-secondary);
  font-size: 0.9rem;
}
</style>
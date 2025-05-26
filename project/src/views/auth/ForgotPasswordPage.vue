<template>
  <div class="login-page">
    <div class="login-container">
      <h1 class="login-title">Forgot your password?</h1>
      <p class="login-subtitle">
        Enter your email and we’ll send you a link to reset and change your password.
      </p>

      <div v-if="successMessage" class="success-message">
        {{ successMessage }}
      </div>

      <div v-if="error" class="error-message">
        {{ error }}
        <button class="error-close" @click="error = ''">×</button>
      </div>

      <form @submit.prevent="handleSubmit" class="login-form" v-if="!successMessage">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="form-control"
            placeholder="Enter your email"
            required
            :disabled="loading"
            autocomplete="email"
          />
        </div>

        <div class="form-actions">
          <button
            type="submit"
            class="btn btn-primary btn-block"
            :disabled="loading"
          >
            <span v-if="loading">Sending...</span>
            <span v-else>Send Reset Link</span>
          </button>
        </div>
      </form>

      <div class="login-footer">
        <router-link to="/login" class="forgot-password-link">
          Back to login
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ForgotPasswordPage',
  data() {
    return {
      email: '',
      loading: false,
      error: '',
      successMessage: ''
    }
  },
  methods: {
    async handleSubmit() {
      this.loading = true
      this.error = ''
      try {
        await axios.post('http://127.0.0.1:8000/api/password/email', {
          email: this.email
        })
        this.successMessage = 'Reset link has been sent to your email.'
        setTimeout(() => {
          this.$router.push('/login')
        }, 2500)
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to send reset link.'
      } finally {
        this.loading = false
      }
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

.login-subtitle {
  text-align: center;
  margin-bottom: var(--spacing-lg);
  color: var(--color-text-secondary);
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

.form-group {
  margin-bottom: var(--spacing-lg);
}

.form-label {
  display: block;
  margin-bottom: var(--spacing-xs);
  font-weight: bold;
}

.form-control {
  width: 100%;
  padding: var(--spacing-md);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius-md);
}

.form-actions {
  margin-top: var(--spacing-lg);
}

.btn-block {
  width: 100%;
  padding: var(--spacing-md);
}

.login-footer {
  text-align: center;
  color: var(--color-text-secondary);
  font-size: 0.9rem;
  margin-top: var(--spacing-lg);
}

.forgot-password-link {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
}
.forgot-password-link:hover {
  text-decoration: underline;
}
</style>
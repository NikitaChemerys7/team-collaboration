<template>
  <div class="login-page">
    <div class="login-container">
      <h1 class="login-title">Reset your password</h1>

      <div v-if="successMessage" class="success-message">
        {{ successMessage }}
      </div>

      <div v-if="error" class="error-message">
        {{ error }}
        <button class="error-close" @click="error = ''">×</button>
      </div>

      <form @submit.prevent="handleSubmit" class="login-form" v-if="!successMessage">
        <div class="form-group">
          <label class="form-label">New Password</label>
          <div style="position: relative;">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              class="form-control"
              required
              autocomplete="new-password"
            />
            <span
              class="password-toggle"
              @click="showPassword = !showPassword"
              title="Toggle password visibility"
            >
              {{ showPassword ? '👁' : '👁️‍🗨️' }}
            </span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <div style="position: relative;">
            <input
              :type="showConfirmPassword ? 'text' : 'password'"
              v-model="confirmPassword"
              class="form-control"
              required
              autocomplete="new-password"
            />
            <span
              class="password-toggle"
              @click="showConfirmPassword = !showConfirmPassword"
              title="Toggle password visibility"
            >
              {{ showConfirmPassword ? '👁' : '👁️‍🗨️' }}
            </span>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
            <span v-if="loading">Updating...</span>
            <span v-else>Reset Password</span>
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
  name: 'ResetPasswordPage',
  data() {
    return {
        password: '',
        confirmPassword: '',
        token: '',
        email: '',
        successMessage: '',
        error: '',
        loading: false,
        showPassword: false,
        showConfirmPassword: false,
    }
  },
  methods: {
    async handleSubmit() {
    if (this.password !== this.confirmPassword) {
        this.error = 'Passwords do not match.'
        return
    }

    this.loading = true
    this.error = ''
        try {
            const token = this.$route.params.token
            const email = this.$route.query.email
            await axios.post('http://127.0.0.1:8000/api/password/reset', {
            token,
            email,
            password: this.password,
            password_confirmation: this.confirmPassword
            })
            this.successMessage = 'Your password has been successfully updated.'
            setTimeout(() => {
            this.$router.push('/login')
            }, 2500)
        } catch (err) {
            this.error = err.response?.data?.message || 'Failed to reset password.'
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

.success-message {
  background-color: rgba(76, 175, 80, 0.1);
  color: var(--color-success, #2e7d32);
  padding: var(--spacing-md);
  border-radius: var(--border-radius-md);
  margin-bottom: var(--spacing-lg);
  text-align: center;
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
.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 1.2rem;
  user-select: none;
  color: var(--color-text-secondary);
}
</style>
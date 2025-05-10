<template>
  <div class="login-page">
    <div class="login-container">
      <h1 class="login-title">Create an Account</h1>

      <form @submit.prevent="handleRegister" class="login-form">
        <div v-if="error" class="error-message">
          {{ error }}
          <button class="error-close" @click="clearError">×</button>
        </div>

        <div class="form-group">
          <label for="name" class="form-label">Full Name</label>
          <input
            type="text"
            id="name"
            v-model="name"
            class="form-control"
            placeholder="Your name"
            required
          />
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="form-control"
            placeholder="you@example.com"
            required
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input">
            <input
              type="password"
              id="password"
              v-model="password"
              class="form-control"
              placeholder="Choose a password"
              required
            />
          </div>
        </div>

        <div class="form-group">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <div class="password-input">
            <input
              type="password"
              id="confirmPassword"
              v-model="confirmPassword"
              class="form-control"
              placeholder="Repeat your password"
              required
            />
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary btn-block">
            Register
          </button>
        </div>
      </form>

      <div class="login-footer">
        <p>
          Already have an account?
          <router-link to="/login">Log In</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RegistrationPage',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      confirmPassword: '',
      error: ''
    }
  },
  methods: {
    clearError() {
      this.error = ''
    },
    handleRegister() {
      if (this.password !== this.confirmPassword) {
        this.error = 'Passwords do not match.'
        return
      }
      // simulate frontend handling
      console.log('Registering:', {
        name: this.name,
        email: this.email,
        password: this.password
      })
      alert('Registration successful. You can now log in.')
      this.$router.push('/login')
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

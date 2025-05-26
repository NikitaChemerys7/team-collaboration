<template>
  <div class="verification-page">
    <div class="verification-content">
      <h2>Please confirm your email</h2>
      <p>We have sent an email to {{ email }}. Please follow the link to complete your registration</p>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'EmailVerificationWaitingPage',
  data() {
    return {
      email: this.$route.query.email,
      error: '',
      intervalId: null,
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    }
  },
  mounted() {
    this.checkVerification()  
    this.startPolling()
  },
  methods: {
    startPolling() {
      this.intervalId = setInterval(this.checkVerification, 3000)
    },
    async checkVerification() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/check-email-verified', {
          params: { email: this.email }
        })

        if (response.data.verified) {
            clearInterval(this.intervalId)

            this.authStore.token = response.data.token
            this.authStore.user = response.data.user
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
            
            this.$router.push('/dashboard')
        }

      } catch (err) {
        this.error = 'Email validation error'
        console.error(err)
      }
    }
  },
  beforeUnmount() {
    if (this.intervalId) {
      clearInterval(this.intervalId)
    }
  }
}
</script>


<style scoped>
.verification-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh; /* Полная высота экрана */
  text-align: center;
  padding: 2rem;
}

.verification-content {
  max-width: 600px;
}

.error {
  color: red;
  margin-top: 1rem;
}
</style>

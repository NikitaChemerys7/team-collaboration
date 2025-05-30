<template>
  <div class="mail-page">
    <h1>Send Email</h1>

    <form @submit.prevent="sendMail" class="mail-form">
      <label for="email">Email</label>
      <input 
        id="email" 
        type="email" 
        v-model="email" 
        placeholder="Enter recipient's email" 
        required
      />

      <label for="message">Message</label>
      <textarea 
        id="message" 
        v-model="message" 
        rows="6" 
        placeholder="Write your message here" 
        required
      ></textarea>

      <button type="submit" :disabled="loading">{{ loading ? 'Sending...' : 'Send' }}</button>
    </form>

    <p v-if="successMessage" class="success-message">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
  </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  name: 'MailPage',
  setup() {
    const email = ref('')
    const message = ref('')
    const loading = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')

    const sendMail = async () => {
      successMessage.value = ''
      errorMessage.value = ''
      loading.value = true

      try {
        await axios.post('http://127.0.0.1:8000/api/send-email', { email: email.value, message: message.value })
        successMessage.value = 'Email successfully sent!'
        email.value = ''
        message.value = ''
      } catch (err) {
        errorMessage.value = 'Failed to send email. Please try again.'
        console.error(err)
      } finally {
        loading.value = false
      }
    }

    return {
      email, message, loading, successMessage, errorMessage, sendMail
    }
  }
}
</script>

<style scoped>
.mail-page {
  max-width: 600px;
  margin: 2rem auto;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.mail-page h1 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #111827;
}

.mail-form label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.mail-form input[type="email"],
.mail-form textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  margin-bottom: 1.25rem;
  resize: vertical;
  font-family: inherit;
  color: #111827;
  transition: border-color 0.2s ease;
}

.mail-form input[type="email"]:focus,
.mail-form textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.3);
}

.mail-form button {
  background-color: #3b82f6;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 1.125rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.mail-form button:disabled {
  background-color: #93c5fd;
  cursor: not-allowed;
}

.mail-form button:not(:disabled):hover {
  background-color: #2563eb;
}

.success-message {
  color: #16a34a;
  font-weight: 600;
  margin-top: 1rem;
}

.error-message {
  color: #dc2626;
  font-weight: 600;
  margin-top: 1rem;
}
</style>
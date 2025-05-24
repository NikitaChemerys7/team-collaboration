<template>
  <div class="contact-page">
    <h1>Contact Us</h1>
    <p>If you have questions about our conferences, feel free to reach out using the form below.</p>
    
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label for="name">Name:</label>
        <input v-model="form.name" type="text" id="name" required />
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input v-model="form.email" type="email" id="email" required />
      </div>

      <div class="form-group">
        <label for="message">Message:</label>
        <textarea v-model="form.message" id="message" rows="5" required></textarea>
      </div>

      <button type="submit">Send Message</button>
    </form>

    <p v-if="submitted">Thanks for your message! We'll get back to you shortly.</p>
  </div>
</template>

<script>
export default {
  name: 'ContactPage',
  data() {
    return {
      form: {
        name: '',
        email: '',
        message: ''
      },
      submitted: false
    }
  },
  methods: {
    submitForm() {
      // tu by si mohol integrovať API alebo len simulovať odoslanie
      this.submitted = true
      this.form = { name: '', email: '', message: '' }
    }
  }
}
</script>

<style scoped>
.contact-page {
  padding: var(--spacing-lg);
}
.form-group {
  margin-bottom: var(--spacing-md);
}
input, textarea {
  width: 100%;
  padding: 0.5em;
  border-radius: 4px;
  border: 1px solid #ccc;
}
button {
  padding: 0.5em 1em;
  border: none;
  background-color: #007acc;
  color: white;
  cursor: pointer;
  border-radius: 4px;
}
</style>

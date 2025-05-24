<template>
  <div class="conferences-page">
    <div class="container">
      <h1>Conferences</h1>
      
      <div v-if="store.loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading conferences...</p>
      </div>

      <div v-else-if="store.error" class="error-state">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ store.error }}</p>
        <button @click="store.fetchConferences" class="retry-button">
          Try Again
        </button>
      </div>

      <div v-else-if="!sortedConferences.length" class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p>No conferences found</p>
      </div>

      <div v-else class="conferences-grid">
        <ConferenceCard
          v-for="conference in sortedConferences"
          :key="conference.id"
          :conference="conference"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useConferenceStore } from '../stores/conference'
import ConferenceCard from '../components/ConferenceCard.vue'

const store = useConferenceStore()

const sortedConferences = computed(() => {
  return [...store.conferences].sort((a, b) => {
    return new Date(b.date) - new Date(a.date)
  })
})

onMounted(() => {
  store.fetchConferences()
})
</script>

<style scoped>
.conferences-page {
  padding: var(--spacing-xl) 0;
}

.container {
  max-width: var(--container-width);
  margin: 0 auto;
  padding: 0 var(--spacing-lg);
}

h1 {
  margin-bottom: var(--spacing-xl);
  color: var(--color-text);
  font-size: 2rem;
}

.conferences-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--spacing-lg);
}

.loading-state,
.error-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-xl);
  text-align: center;
  color: var(--color-text-secondary);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--color-primary-light);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: var(--spacing-md);
}

.error-state i,
.empty-state i {
  font-size: 3rem;
  margin-bottom: var(--spacing-md);
  color: var(--color-error);
}

.retry-button {
  margin-top: var(--spacing-md);
  padding: var(--spacing-sm) var(--spacing-lg);
  background-color: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  transition: background-color var(--transition-fast);
}

.retry-button:hover {
  background-color: var(--color-primary-dark);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>

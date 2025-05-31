<template>
  <div class="conferences-page">
    <div class="container">
      <div class="page-layout">
        <!-- Sidebar with year filters -->
        <aside class="sidebar">
          <h2>Years</h2>
          <div class="year-filters">
            <button 
              v-for="year in availableYears" 
              :key="year"
              :class="['year-button', { active: selectedYear === year }]"
              @click="selectedYear = year"
            >
              {{ year }}
            </button>
          </div>
        </aside>

        <!-- Main content -->
        <main class="main-content">
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

          <div v-else-if="!filteredConferences.length" class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>No conferences found for {{ selectedYear }}</p>
          </div>

          <div v-else class="conferences-grid">
            <ConferenceCard
              v-for="conference in filteredConferences"
              :key="conference.id"
              :conference="conference"
            />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue'
import { useConferenceStore } from '../stores/conference'
import ConferenceCard from '../components/ConferenceCard.vue'

const store = useConferenceStore()
const selectedYear = ref(new Date().getFullYear())

const availableYears = computed(() => {
  const years = store.conferences.map(conf => conf.year)
  return [...new Set(years)].sort((a, b) => b - a)
})

const filteredConferences = computed(() => {
  return store.conferences
    .filter(conf => conf.year === selectedYear.value)
    .sort((a, b) => new Date(b.date) - new Date(a.date))
})

onMounted(async () => {
  try {
    await store.fetchConferences()
    if (availableYears.value.length > 0) {
      selectedYear.value = availableYears.value[0]
    }
  } catch (error) {
    console.error('Error loading conferences:', error)
  }
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

.page-layout {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: var(--spacing-xl);
}

.sidebar {
  background: white;
  padding: var(--spacing-lg);
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  height: fit-content;
}

.sidebar h2 {
  margin-bottom: var(--spacing-md);
  color: var(--color-text);
  font-size: 1.25rem;
}

.year-filters {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
}

.year-button {
  padding: var(--spacing-sm) var(--spacing-md);
  border: 1px solid var(--color-border);
  border-radius: 4px;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.year-button:hover {
  background: var(--color-background);
}

.year-button.active {
  background: var(--color-primary);
  color: white;
  border-color: var(--color-primary);
}

.main-content h1 {
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
  border: 3px solid var(--color-border);
  border-top: 3px solid var(--color-primary);
  border-radius: 50%;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
  margin-bottom: var(--spacing-md);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.retry-button {
  margin-top: var(--spacing-md);
  padding: var(--spacing-sm) var(--spacing-lg);
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.retry-button:hover {
  background: var(--color-primary-dark);
}

@media (max-width: 768px) {
  .page-layout {
    grid-template-columns: 1fr;
  }
  
  .sidebar {
    position: sticky;
    top: var(--spacing-md);
    z-index: 10;
  }
  
  .year-filters {
    flex-direction: row;
    flex-wrap: wrap;
  }
  
  .year-button {
    flex: 1;
    min-width: 80px;
    text-align: center;
  }
}
</style>

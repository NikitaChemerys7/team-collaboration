<template>
  <div class="conferences-page">
    <h1>Conferences</h1>
    <p>Browse our upcoming and past conferences.</p>

    <div class="conference-list">
      <ConferenceCard
        v-for="conference in conferences"
        :key="conference.id"
        :conference="conference"
      />
    </div>
  </div>
</template>

<script>
// импорт компонента карточки конференции
import ConferenceCard from '../components/ConferenceCard.vue'

export default {
  name: 'ConferencesPage',
  components: {
    ConferenceCard
  },
  data() {
    return {
      conferences: [
        {
          id: 1,
          title: 'International AI Symposium 2025',
          date: 'April 22–24, 2025',
          location: 'Bratislava, Slovakia'
        },
        {
          id: 2,
          title: 'Global Climate Research Forum',
          date: 'May 15–17, 2025',
          location: 'Vienna, Austria'
        },
        {
          id: 3,
          title: 'Education Technology Summit',
          date: 'June 5–6, 2025',
          location: 'Online'
        }
      ]
    }
  }
}
</script>

<style scoped>
.conferences-page {
  padding: var(--spacing-lg);
}
.conference-list {
  margin-top: var(--spacing-md);
  display: flex;
  flex-direction: column;
  gap: var(--spacing-md);
}
</style>

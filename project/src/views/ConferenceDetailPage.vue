<template>
  <div class="conference-detail-page" v-if="conference">
    <h1>{{ conference.title }}</h1>
    <p><strong>Date:</strong> {{ conference.date }}</p>
    <p><strong>Location:</strong> {{ conference.location }}</p>
    <p>{{ conference.description }}</p>

    <h2>Speakers</h2>
    <ul>
      <li v-for="speaker in conference.speakers" :key="speaker">{{ speaker }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'ConferenceDetailPage',
  props: ['id'],
  data() {
    return {
      conference: null
    }
  },
  created() {
    const id = Number(this.$route.params.id)

    // napodobíme fetch z databázy alebo API
    const allConferences = [
      {
        id: 1,
        title: 'International AI Symposium 2025',
        date: 'April 22–24, 2025',
        location: 'Bratislava, Slovakia',
        description: 'Explore the future of AI with global experts.',
        speakers: ['Anna Nowak', 'Samuel Green', 'Dr. Emília Čechová']
      },
      {
        id: 2,
        title: 'Global Climate Research Forum',
        date: 'May 15–17, 2025',
        location: 'Vienna, Austria',
        description: 'A forum discussing urgent climate research topics.',
        speakers: ['Prof. Müller', 'Dr. Wang Li', 'Ing. Maria Fiala']
      },
      {
        id: 3,
        title: 'Education Technology Summit',
        date: 'June 5–6, 2025',
        location: 'Online',
        description: 'Two days of panels on EdTech trends.',
        speakers: ['John Smith', 'Lisa Novak', 'Yuki Tanaka']
      }
    ]

    this.conference = allConferences.find(conf => conf.id === id)
  }
}
</script>

<style scoped>
.conference-detail-page {
  padding: var(--spacing-lg);
}
</style>

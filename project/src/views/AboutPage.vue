<template>
  <div class="about-page">
    <h1>About Us</h1>
    <p>We are a consortium of universities working together to organize academic and professional conferences across various fields.</p>
    <section class="mission">
      <h2>Our Mission</h2>
      <p>To connect scholars and professionals through conferences that inspire innovation, research, and collaboration.</p>
    </section>
    <section class="team">
      <h2>Our Team</h2>
      <ul>
        <li v-for="member in team" :key="member.name">
          <strong>{{ member.name }}</strong> – {{ member.role }}
        </li>
      </ul>
    </section>
  </div>
</template>

<script>
export default {
  name: 'AboutPage',
  data() {
    return {
      team: [
        { name: 'Dr. Anna Smith', role: 'Chairperson' },
        { name: 'Prof. Mark Liu', role: 'Conference Coordinator' },
        { name: 'Lisa Novak', role: 'Logistics Manager' }
      ]
    }
  }
}
</script>

<style scoped>
.about-page {
  padding: var(--spacing-lg);
}
.mission, .team {
  margin-top: var(--spacing-md);
}
ul {
  list-style-type: disc;
  padding-left: 1.5em;
}
</style>

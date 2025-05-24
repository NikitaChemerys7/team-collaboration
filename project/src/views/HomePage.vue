<template>
  <div class="home-page">
    <header class="hero-section">
      <h1>Welcome to University Consortium CMS</h1>
      <p class="lead">A unified platform for accessing and viewing university consortium conferences.</p>
    </header>

    <section class="features-section">
      <h2>Platform Features</h2>
      <div class="features-grid">
        <div class="feature-card">
          <i class="fas fa-calendar-alt"></i>
          <h3>Conference Information</h3>
          <p>Access detailed information about academic conferences across consortium universities.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-users"></i>
          <h3>Participant Portal</h3>
          <p>View conference participants and their affiliated institutions.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-file-alt"></i>
          <h3>Conference Materials</h3>
          <p>Access conference schedules, presentations, and related documents.</p>
        </div>
        <div class="feature-card">
          <i class="fas fa-info-circle"></i>
          <h3>Event Details</h3>
          <p>Find comprehensive information about venues, dates, and conference programs.</p>
        </div>
      </div>
    </section>

    <section class="conferences-section">
      <h2>Conference Schedule</h2>
      <p class="conferences-info">View all upcoming and past conferences in our consortium network.</p>
      <router-link to="/conferences" class="primary-btn">View Conferences</router-link>
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomePage'
}
</script>

<style scoped>
.home-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.hero-section {
  text-align: center;
  padding: 4rem 0;
  background: linear-gradient(to right, #f8f9fa, #e9ecef);
  border-radius: 1rem;
  margin-bottom: 3rem;
}

.hero-section h1 {
  font-size: 2.5rem;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.lead {
  font-size: 1.25rem;
  color: #6c757d;
  margin-bottom: 2rem;
}

.features-section {
  margin-bottom: 3rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.feature-card {
  padding: 1.5rem;
  background: #ffffff;
  border-radius: 0.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.feature-card i {
  font-size: 2rem;
  color: #3498db;
  margin-bottom: 1rem;
}

.feature-card h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.conferences-section {
  text-align: center;
  padding: 3rem;
  background: linear-gradient(to right, #f8f9fa, #e9ecef);
  border-radius: 1rem;
  margin-bottom: 2rem;
}

.conferences-info {
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.primary-btn {
  display: inline-block;
  background: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.25rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s ease;
  text-decoration: none;
}

.primary-btn:hover {
  background: #2980b9;
}

h2 {
  color: #2c3e50;
  margin-bottom: 1rem;
  text-align: center;
}
</style>
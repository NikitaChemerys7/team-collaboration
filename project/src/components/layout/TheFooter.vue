<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo">
          <h3>University Consortium CMS</h3>
          <p>A unified platform for university conference management</p>
        </div>
        
        <div class="footer-links">
          <div class="footer-column">
            <h4>Universities</h4>
            <ul>
              <li v-for="(university, index) in universities" :key="index">
                <a :href="university.url" target="_blank">{{ university.name }}</a>
              </li>
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Quick Links</h4>
            <ul>
              <li><router-link to="/">Home</router-link></li>
              <li><router-link to="/about">About</router-link></li>
              <li><router-link to="/conferences">Conferences</router-link></li>
              <li><router-link to="/contact">Contact</router-link></li>
            </ul>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; {{ currentYear }} University Consortium CMS. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'TheFooter',
  data() {
    return {
      universities: [
        { name: 'University of Ljubljana', url: 'https://www.uni-lj.si/eng/' },
        { name: 'University of Zagreb', url: 'http://www.unizg.hr/homepage/' },
        { name: 'University of Osijek', url: 'https://www.unios.hr/en/' },
        { name: 'BOKU University', url: 'https://boku.ac.at/en/' },
        { name: 'University of Padua', url: 'https://www.unipd.it/en/' },
        { name: 'Czech University of Life Sciences', url: 'https://www.czu.cz/en/' },
        { name: 'Hungarian University of Agriculture', url: 'https://uni-mate.hu/en' },
        { name: 'Slovak University of Agriculture', url: 'https://www.uniag.sk/en/' }
      ]
    }
  },
  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  }
}
</script>

<style scoped>
.footer {
  background-color: var(--color-primary-dark);
  color: white;
  padding: var(--spacing-xl) 0;
  margin-top: var(--spacing-xl);
}

.footer-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  margin-bottom: var(--spacing-xl);
}

.footer-logo {
  flex: 1;
  min-width: 300px;
  margin-right: var(--spacing-xl);
  margin-bottom: var(--spacing-lg);
}

.footer-logo h3 {
  color: white;
  margin-bottom: var(--spacing-sm);
}

.footer-logo p {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
}

.footer-links {
  display: flex;
  flex-wrap: wrap;
  flex: 2;
}

.footer-column {
  flex: 1;
  min-width: 200px;
  margin-bottom: var(--spacing-lg);
}

.footer-column h4 {
  color: white;
  margin-bottom: var(--spacing-md);
  font-size: 1.1rem;
}

.footer-column ul {
  list-style: none;
  padding: 0;
}

.footer-column li {
  margin-bottom: var(--spacing-sm);
}

.footer-column a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: color var(--transition-fast);
  font-size: 0.9rem;
}

.footer-column a:hover {
  color: white;
}

.footer-bottom {
  padding-top: var(--spacing-lg);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.5);
}

@media (max-width: 768px) {
  .footer-content {
    flex-direction: column;
  }
  
  .footer-logo {
    margin-right: 0;
    text-align: center;
  }
  
  .footer-links {
    justify-content: space-around;
  }
}
</style>
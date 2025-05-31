<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <router-link to="/">
            <!-- <h1>University Consortium CMS</h1> -->
             <img src="/logo.png" width="80" height="78" class="logo" alt="">
          </router-link>
        </div>
        
        <div class="header-right">
          <nav class="desktop-nav">
            <router-link to="/">Home</router-link>
            <!-- <router-link to="/about">About</router-link> -->
            <router-link to="/conferences">Conferences</router-link>
            <!-- <router-link to="/contact">Contact</router-link> -->
            <template v-if="isLoggedIn">
              <router-link v-if="isAdmin || isEditor" to="/dashboard">Dashboard</router-link>
              <div class="user-menu">
                <button @click="toggleUserMenu" class="user-button">
                  {{ user.name }} <span class="arrow">▼</span>
                </button>
                <div v-if="showUserMenu" class="dropdown-menu">
                  <div class="dropdown-item user-info">
                    <strong>{{ user.name }}</strong>
                    <span class="role-badge" :class="userRoleClass">{{ userRole }}</span>
                  </div>
                  <router-link to="/profile" class="dropdown-item">Profile</router-link>
                  <button @click="logout" class="dropdown-item">Logout</button>
                </div>
              </div>
            </template>
            <template v-else>
              <router-link to="/login" class="login-link">Login</router-link>
              <router-link to="/registration" class="login-link">Registration</router-link>
            </template>
          </nav>

          <button @click="toggleTheme" class="theme-toggle" :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
            <span v-if="isDarkMode">🌞</span>
            <span v-else>🌙</span>
          </button>
          
          <button @click="toggleMobileMenu" class="mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
      </div>
    </div>
    
    <transition name="slide-down">
      <nav v-if="showMobileMenu" class="mobile-nav">
        <router-link to="/" @click="closeMobileMenu">Home</router-link>
        <router-link to="/about" @click="closeMobileMenu">About</router-link>
        <router-link to="/conferences" @click="closeMobileMenu">Conferences</router-link>
        <router-link to="/contact" @click="closeMobileMenu">Contact</router-link>
        <template v-if="isLoggedIn">
          <router-link v-if="isAdmin || isEditor" to="/dashboard" @click="closeMobileMenu">Dashboard</router-link>
          <router-link to="/profile" @click="closeMobileMenu">Profile</router-link>
          <button @click="logout" class="mobile-logout">Logout</button>
        </template>
        <template v-else>
          <router-link to="/login" @click="closeMobileMenu">Login</router-link>
        </template>
      </nav>
    </transition>
  </header>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '../../stores/auth'
import { useThemeStore } from '../../stores/theme'

export default {
  name: 'TheHeader',
  data() {
    return {
      showUserMenu: false,
      showMobileMenu: false
    }
  },
  computed: {
    ...mapState(useAuthStore, ['isLoggedIn', 'user']),
    ...mapState(useThemeStore, ['isDarkMode']),
    userRole() {
      if (!this.user || !this.user.role) return ''
      return this.user.role.charAt(0).toUpperCase() + this.user.role.slice(1)
    },
    userRoleClass() {
      if (!this.user || !this.user.role) return ''
      return `role-${this.user.role.toLowerCase()}`
    },
    isAdmin() {
      return this.user && this.user.role === 'admin'
    },
    isEditor() {
      return this.user && this.user.role === 'editor'
    }
  },
  methods: {
    ...mapActions(useAuthStore, ['logout']),
    ...mapActions(useThemeStore, ['toggleTheme']),
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu
    },
    toggleMobileMenu() {
      this.showMobileMenu = !this.showMobileMenu
    },
    closeMobileMenu() {
      this.showMobileMenu = false
    }
  },
  watch: {
    '$route'() {
      this.showUserMenu = false
      this.showMobileMenu = false
    }
  },
  mounted() {
    document.addEventListener('click', (event) => {
      const userMenu = this.$el.querySelector('.user-menu')
      if (userMenu && !userMenu.contains(event.target)) {
        this.showUserMenu = false
      }
    })
  }
}
</script>

<style scoped>
.header {
  background-color: var(--color-surface);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
  transition: background-color var(--transition-normal);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-md) 0;
}

.logo h1 {
  font-size: 1.5rem;
  margin: 0;
  color: var(--color-primary);
  transition: color var(--transition-fast);
}

.logo a {
  text-decoration: none;
}

.header-right {
  display: flex;
  align-items: center;
}

.desktop-nav {
  display: flex;
  align-items: center;
}

.desktop-nav a {
  margin-left: var(--spacing-lg);
  color: var(--color-text);
  font-weight: 500;
  transition: color var(--transition-fast);
}

.desktop-nav a:hover, 
.desktop-nav a.router-link-active {
  color: var(--color-primary);
}

.theme-toggle {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.25rem;
  margin-left: var(--spacing-md);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  transition: background-color var(--transition-fast);
}

.theme-toggle:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.user-menu {
  position: relative;
  margin-left: var(--spacing-lg);
}

.user-button {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--color-text);
  font-weight: 500;
  display: flex;
  align-items: center;
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--border-radius-md);
  transition: background-color var(--transition-fast);
}

.user-button:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.arrow {
  font-size: 0.7rem;
  margin-left: var(--spacing-sm);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background-color: var(--color-surface);
  border-radius: var(--border-radius-md);
  box-shadow: var(--shadow-md);
  min-width: 200px;
  z-index: 10;
  overflow: hidden;
  animation: dropdown 0.2s ease;
}

@keyframes dropdown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: block;
  padding: var(--spacing-md);
  color: var(--color-text);
  text-decoration: none;
  transition: background-color var(--transition-fast);
  text-align: left;
  width: 100%;
  border: none;
  background: none;
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.user-info {
  border-bottom: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
}

.role-badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 600;
  margin-top: 4px;
}

.role-admin {
  background-color: var(--color-primary-light);
  color: white;
}

.role-editor {
  background-color: var(--color-secondary-light);
  color: white;
}

.mobile-menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  width: 30px;
  height: 24px;
  position: relative;
  margin-left: var(--spacing-md);
}

.mobile-menu-toggle span {
  display: block;
  position: absolute;
  height: 3px;
  width: 100%;
  background: var(--color-text);
  opacity: 1;
  left: 0;
  transform: rotate(0deg);
  transition: .25s ease-in-out;
}

.mobile-menu-toggle span:nth-child(1) {
  top: 0px;
}

.mobile-menu-toggle span:nth-child(2) {
  top: 10px;
}

.mobile-menu-toggle span:nth-child(3) {
  top: 20px;
}

.mobile-nav {
  display: none;
  padding: var(--spacing-md);
  background-color: var(--color-surface);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.mobile-nav a,
.mobile-logout {
  display: block;
  padding: var(--spacing-md);
  color: var(--color-text);
  text-decoration: none;
  font-weight: 500;
  border-bottom: 1px solid var(--color-border);
}

.mobile-logout {
  width: 100%;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  color: var(--color-error);
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}

@media (max-width: 768px) {
  .logo h1 {
    font-size: 1.2rem;
  }
  
  .desktop-nav {
    display: none;
  }
  
  .mobile-menu-toggle {
    display: block;
  }
  
  .mobile-nav {
    display: block;
  }
}
</style>
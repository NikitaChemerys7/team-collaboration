<template>
  <div class="app-container" :class="{ 'dark-mode': isDarkMode }">
    <TheHeader />
    <main class="main-content">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
    <TheFooter />
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useThemeStore } from './stores/theme'
import { useAuthStore } from './stores/auth'
import TheHeader from './components/layout/TheHeader.vue'
import TheFooter from './components/layout/TheFooter.vue'

export default {
  name: 'App',
  components: {
    TheHeader,
    TheFooter
  },
  computed: {
    ...mapState(useThemeStore, ['isDarkMode'])
  },
  async created() {
    const authStore = useAuthStore()
    if (localStorage.getItem('token')) {
      await authStore.fetchCurrentUser()
    }
  }
}
</script>

<style>
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
  color: var(--color-text);
  transition: background-color 0.3s ease, color 0.3s ease;
}

.main-content {
  flex: 1;
  padding: 2rem;
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
}

.dark-mode {
  --color-background: #121212;
  --color-surface: #1e1e1e;
  --color-text: rgba(255, 255, 255, 0.87);
  --color-border: rgba(255, 255, 255, 0.12);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }
}
</style>
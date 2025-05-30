<template>
  <div class="conference-detail">
    <div v-if="store.loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading conference details...</p>
    </div>

    <div v-else-if="store.error" class="error-state">
      <i class="fas fa-exclamation-circle"></i>
      <p>{{ store.error }}</p>
      <button @click="fetchConference" class="retry-button">
        Try Again
      </button>
    </div>

    <div v-else-if="!conference" class="not-found">
      <i class="fas fa-search"></i>
      <p>Conference not found</p>
      <router-link to="/conferences" class="back-link">
        Back to Conferences
      </router-link>
    </div>

    <div v-else class="container">
      <div class="hero-section" :style="heroStyle">
        <div class="hero-content">
          <h1>{{ conference.title }}</h1>
          <div class="conference-meta">
            <p class="date">
              <i class="fas fa-calendar"></i>
              {{ formatDate(conference.date) }}
            </p>
            <p class="location">
              <i class="fas fa-map-marker-alt"></i>
              {{ conference.location }}
            </p>
          </div>
        </div>
      </div>

      <div class="main-content">
        <div class="description-section">
          <h2>About the Conference</h2>
          <div v-html="conference.description" class="description-content"></div>
        </div>

        <div v-if="conference.speakers?.length" class="speakers-section">
          <h2>Speakers</h2>
          <div class="speakers-grid">
            <div v-for="speaker in conference.speakers" :key="speaker.name" class="speaker-card">
              <img v-if="speaker.photo" :src="speaker.photo" :alt="speaker.name" class="speaker-photo">
              <div class="speaker-info">
                <h3>{{ speaker.name }}</h3>
                <p class="role">{{ speaker.role }}</p>
                <p class="workplace">{{ speaker.workplace }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="conference.files?.length" class="documents-section">
          <h2>Documents</h2>
          <div class="documents-list">
            <a 
              v-for="file in conference.files" 
              :key="file.name"
              :href="getDocumentUrl(file.url)"
              target="_blank"
              class="document-link"
            >
              <i class="fas fa-file-alt"></i>
              {{ file.name }}
            </a>
          </div>
        </div>

        <div v-if="subpages.length" class="subpages-section">
          <h2>Additional Information</h2>
          <div class="subpages-grid">
            <router-link 
              v-for="subpage in subpages" 
              :key="subpage.id"
              :to="{ name: 'subpage', params: { conferenceId: conference.id, subpageId: subpage.slug }}"
              class="subpage-card"
            >
              <h3>{{ subpage.title }}</h3>
              <span class="read-more">Read More <i class="fas fa-arrow-right"></i></span>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useConferenceStore } from '../stores/conference'
import { useSubpageStore } from '../stores/subpage'
import { API_URL } from '../config'

const route = useRoute()
const router = useRouter()
const store = useConferenceStore()
const subpageStore = useSubpageStore()

const showGallery = ref(false)
const currentImageIndex = ref(0)

const conference = computed(() => store.currentConference)
const subpages = computed(() => subpageStore.subpages)

const heroStyle = computed(() => ({
  backgroundImage: conference.value?.hero_image 
    ? `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(${getHeroImageUrl(conference.value.hero_image)})`
    : 'linear-gradient(var(--color-primary), var(--color-primary-dark))'
}))

function getHeroImageUrl(heroImage) {
    if (!heroImage) return ''
    if (heroImage.startsWith('http')) return heroImage
    if (heroImage.startsWith('/')) {
        return `${API_URL.replace('/api', '')}${heroImage}`
    }
    return `${API_URL.replace('/api', '')}/${heroImage}`
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function openGallery(index) {
  currentImageIndex.value = index
  showGallery.value = true
  document.body.style.overflow = 'hidden'
}

function closeGallery() {
  showGallery.value = false
  document.body.style.overflow = ''
}

function prevImage() {
  currentImageIndex.value = (currentImageIndex.value - 1 + conference.value.gallery.length) % conference.value.gallery.length
}

function nextImage() {
  currentImageIndex.value = (currentImageIndex.value + 1) % conference.value.gallery.length
}

async function fetchConference() {
  const id = Number(route.params.id)
  if (!id) {
    router.push('/conferences')
    return
  }
  await store.fetchConferenceById(id)
  await subpageStore.fetchSubpages(id)
}

function truncateContent(content) {
  if (!content) return ''
  const strippedContent = content.replace(/<[^>]*>/g, '')
  return strippedContent.length > 150 ? strippedContent.substring(0, 150) + '...' : strippedContent
}

function getDocumentUrl(url) {
  return `${API_URL.replace('/api', '')}${url}`
}

onMounted(fetchConference)
</script>

<style scoped>
.conference-detail {
  min-height: 100vh;
  background-color: var(--color-background);
}

.container {
  max-width: var(--container-width);
  margin: 0 auto;
  padding: 0 var(--spacing-lg);
}

.hero-section {
  height: 400px;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
  margin-bottom: var(--spacing-xl);
}

.hero-content {
  max-width: 800px;
  padding: var(--spacing-xl);
}

.hero-content h1 {
  font-size: 3rem;
  margin-bottom: var(--spacing-md);
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  color: white;
}

.conference-meta {
  display: flex;
  gap: var(--spacing-lg);
  justify-content: center;
  font-size: 1.2rem;
}

.conference-meta p {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}

.main-content {
  padding: var(--spacing-xl) 0;
}

.description-section,
.speakers-section,
.gallery-section,
.documents-section,
.subpages-section {
  margin-bottom: var(--spacing-xl);
}

h2 {
  color: var(--color-text);
  font-size: 2rem;
  margin-bottom: var(--spacing-lg);
  padding-bottom: var(--spacing-sm);
  border-bottom: 2px solid var(--color-primary);
}

.speakers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: var(--spacing-lg);
}

.speaker-card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-md);
  transition: transform var(--transition-fast);
}

.speaker-card:hover {
  transform: translateY(-4px);
}

.speaker-photo {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.speaker-info {
  padding: var(--spacing-md);
}

.speaker-info h3 {
  margin: 0 0 var(--spacing-xs);
  color: var(--color-text);
}

.speaker-info .role {
  color: var(--color-primary);
  font-weight: 500;
  margin-bottom: var(--spacing-xs);
}

.speaker-info .workplace {
  color: var(--color-text-secondary);
  font-size: 0.9rem;
}

/* Gallery Grid */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: var(--spacing-md);
}

.gallery-image {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  transition: transform var(--transition-fast);
}

.gallery-image:hover {
  transform: scale(1.05);
}

.documents-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: var(--spacing-md);
}

.document-link {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
  padding: var(--spacing-md);
  background-color: var(--color-surface);
  border-radius: var(--border-radius-md);
  color: var(--color-text);
  text-decoration: none;
  transition: background-color var(--transition-fast);
}

.document-link:hover {
  background-color: var(--color-primary-light);
}


.gallery-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-image {
  max-width: 90%;
  max-height: 90vh;
  object-fit: contain;
}

.close-button {
  position: absolute;
  top: var(--spacing-lg);
  right: var(--spacing-lg);
  background: none;
  border: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
  padding: var(--spacing-sm);
}

.nav-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
  padding: var(--spacing-lg);
}

.nav-button.prev {
  left: var(--spacing-lg);
}

.nav-button.next {
  right: var(--spacing-lg);
}

.loading-state,
.error-state,
.not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
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
.not-found i {
  font-size: 3rem;
  margin-bottom: var(--spacing-md);
  color: var(--color-error);
}

.retry-button,
.back-link {
  margin-top: var(--spacing-md);
  padding: var(--spacing-sm) var(--spacing-lg);
  background-color: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  text-decoration: none;
  transition: background-color var(--transition-fast);
}

.retry-button:hover,
.back-link:hover {
  background-color: var(--color-primary-dark);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.description-content {
  line-height: 1.6;
  color: var(--color-text);
}

.description-content :deep(p) {
  margin-bottom: 1rem;
}

.description-content :deep(strong) {
  font-weight: 600;
}

.description-content :deep(em) {
  font-style: italic;
}

.subpages-section {
  margin-top: var(--spacing-xl);
}

.subpages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--spacing-lg);
}

.subpage-card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-lg);
  text-decoration: none;
  color: var(--color-text);
  transition: transform var(--transition-fast), box-shadow var(--transition-fast);
  box-shadow: var(--shadow-md);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 120px;
}

.subpage-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.subpage-card h3 {
  color: var(--color-primary);
  margin: 0;
  font-size: 1.25rem;
}

.read-more {
  color: var(--color-primary);
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: var(--spacing-xs);
  margin-top: var(--spacing-md);
}

.read-more i {
  transition: transform var(--transition-fast);
}

.subpage-card:hover .read-more i {
  transform: translateX(4px);
}
</style>

<template>
  <div class="subpage">
    <div v-if="store.loading" class="loading-state">
      <div class="spinner"></div>
      <p>Loading subpage details...</p>
    </div>

    <div v-else-if="store.error" class="error-state">
      <i class="fas fa-exclamation-circle"></i>
      <p>{{ store.error }}</p>
      <button @click="fetchSubpage" class="retry-button">
        Try Again
      </button>
    </div>

    <div v-else-if="!subpage" class="not-found">
      <i class="fas fa-search"></i>
      <p>Subpage not found</p>
      <router-link :to="{ name: 'conference-detail', params: { id: conferenceId }}" class="back-link">
        Back to Conference
      </router-link>
    </div>

    <div v-else class="container">
      <div class="hero-section" :style="heroStyle">
        <div class="hero-content">
          <h1>{{ subpage.title }}</h1>
          <div class="subpage-meta">
            <p class="date" v-if="subpage.date">
              <i class="fas fa-calendar"></i>
              {{ formatDate(subpage.date) }}
            </p>
            <p class="author" v-if="subpage.author">
              <i class="fas fa-user"></i>
              {{ subpage.author }}
            </p>
          </div>
        </div>
      </div>

      <div class="main-content">
        <div class="description-section">
          <div v-html="subpage.content" class="content"></div>
        </div>

        <div v-if="subpage.gallery?.length" class="gallery-section">
          <h2>Gallery</h2>
          <div class="gallery-grid">
            <img 
              v-for="(image, index) in subpage.gallery" 
              :key="index" 
              :src="image" 
              :alt="`Subpage image ${index + 1}`"
              @click="openGallery(index)"
              class="gallery-image"
            >
          </div>
        </div>

        <div v-if="subpage.files?.length" class="documents-section">
          <h2>Documents</h2>
          <div class="documents-list">
            <a 
              v-for="file in subpage.files" 
              :key="file.name"
              :href="file.url"
              target="_blank"
              class="document-link"
            >
              <i class="fas fa-file-alt"></i>
              {{ file.name }}
            </a>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showGallery" class="gallery-modal" @click="closeGallery">
      <button class="close-button" @click="closeGallery">
        <i class="fas fa-times"></i>
      </button>
      <button class="nav-button prev" @click.stop="prevImage">
        <i class="fas fa-chevron-left"></i>
      </button>
      <button class="nav-button next" @click.stop="nextImage">
        <i class="fas fa-chevron-right"></i>
      </button>
      <img 
        :src="subpage.gallery[currentImageIndex]" 
        :alt="`Subpage image ${currentImageIndex + 1}`"
        class="modal-image"
      >
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSubpageStore } from '../stores/subpage'

const route = useRoute()
const router = useRouter()
const store = useSubpageStore()

const showGallery = ref(false)
const currentImageIndex = ref(0)

const conferenceId = computed(() => Number(route.params.conferenceId))
const subpageId = computed(() => route.params.subpageId)
const subpage = computed(() => store.currentSubpage)

const heroStyle = computed(() => ({
  backgroundImage: subpage.value?.hero_image 
    ? `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(${subpage.value.hero_image})`
    : 'linear-gradient(var(--color-primary), var(--color-primary-dark))'
}))

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
  currentImageIndex.value = (currentImageIndex.value - 1 + subpage.value.gallery.length) % subpage.value.gallery.length
}

function nextImage() {
  currentImageIndex.value = (currentImageIndex.value + 1) % subpage.value.gallery.length
}

async function fetchSubpage() {
  if (!conferenceId.value || !subpageId.value) {
    router.push('/conferences')
    return
  }
  await store.fetchSubpage(conferenceId.value, subpageId.value)
}

onMounted(fetchSubpage)
</script>

<style scoped>
.subpage {
  min-height: 100vh;
  background-color: var(--color-background);
}

.container {
  max-width: var(--container-width);
  margin: 0 auto;
  padding: 0 var(--spacing-lg);
}

.hero-section {
  height: 300px;
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
  font-size: 2.5rem;
  margin-bottom: var(--spacing-md);
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  color: white;
}

.subpage-meta {
  display: flex;
  gap: var(--spacing-lg);
  justify-content: center;
  font-size: 1.1rem;
}

.subpage-meta p {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}


.main-content {
  padding: var(--spacing-xl) 0;
}

.description-section,
.gallery-section,
.documents-section {
  margin-bottom: var(--spacing-xl);
}

h2 {
  color: var(--color-text);
  font-size: 1.8rem;
  margin-bottom: var(--spacing-lg);
  padding-bottom: var(--spacing-sm);
  border-bottom: 2px solid var(--color-primary);
}

.content {
  line-height: 1.8;
  color: var(--color-text);
}

.content :deep(p) {
  margin-bottom: 1.5rem;
}

.content :deep(strong) {
  font-weight: 600;
}

.content :deep(em) {
  font-style: italic;
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
</style>
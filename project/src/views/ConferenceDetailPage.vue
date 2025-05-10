<template>
  <div class="conference-detail-page" v-if="conference">
    <div class="hero-image" :style="{ backgroundImage: `url(${conference.heroImage})` }"></div>

    <div class="conf-top">
      <h1>{{ conference.title }}</h1>
      <p><strong>Date:</strong> {{ conference.date }}</p>
      <p><strong>Location:</strong> {{ conference.location }}</p>
      <p>{{ conference.description }}</p>
    </div>

    <div class="conf-gallery">
      <h2>Gallery</h2>
      <GalleryLightbox :images="galleryImages" />
    </div>

    <div class="conf-speakers">
      <h2>Speakers</h2>
      <div class="speakers">
        <div class="speaker" v-for="speaker in conference.speakers" :key="speaker.name">
          <img :src="speaker.photo" alt="Speaker photo" />
          <div class="info">
            <h3>{{ speaker.name }}</h3>
            <p class="speaker-role"><strong>{{ speaker.role }}</strong></p>
            <p>{{ speaker.workplace }}</p>
          </div>
        </div>
      </div>
    </div>

    <h2>Documents</h2>
    <ul>
      <li v-for="file in conference.files" :key="file.url">
        <a :href="file.url" target="_blank" rel="noopener">
          📄 {{ file.name }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
import { conferences } from '../data/conferences'
import GalleryLightbox from '../components/GalleryLightbox.vue'

export default {
  name: 'ConferenceDetailPage',
  components: { GalleryLightbox },
  data() {
    return {
      conference: null
    }
  },
  computed: {
    galleryImages() {
      return this.conference
        ? this.conference.gallery.map(src => ({
          src,
          width: 1200,
          height: 800
        }))
        : []
    }
  },
  created() {
    const id = Number(this.$route.params.id)
    this.conference = conferences.find(conf => conf.id === id)
  }
}
</script>

<style scoped>
.conference-detail-page {
  padding: var(--spacing-lg);
}

.hero-image {
  width: 100%;
  height: 300px;
  background-size: cover;
  background-position: center;
  object-fit: cover;
  border-radius: var(--radius-md);
  margin-bottom: var(--spacing-md);
}

.gallery {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: var(--spacing-lg);
}

.gallery-img {
  width: 200px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}

.gallery-img:hover {
  transform: scale(1.03);
}

.conf-top {
  margin: 30px 0 60px;
}

.conf-gallery {
  margin: 60px 0;
}

.speakers {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-md);
}

.speaker {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 180px;
  text-align: center;
}

.speaker img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 0.5rem;
}

.speaker .info h3 {
  margin: 0.5rem 0 0.2rem;
}

.speaker-role {
  margin: 5px 0;
}
</style>

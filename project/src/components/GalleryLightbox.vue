<template>
  <div class="gallery">
    <img
      v-for="(img, index) in images"
      :key="index"
      :src="img.src"
      class="gallery-img"
      @click="openGallery(index)"
      alt="Gallery image"
    />
  </div>
</template>

<script>
import PhotoSwipeLightbox from 'photoswipe/lightbox'
import 'photoswipe/style.css'

export default {
  name: 'GalleryLightbox',
  props: {
    images: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      lightbox: null
    }
  },
  methods: {
    openGallery(index) {
      const PhotoSwipe = () => import('photoswipe')

      import('photoswipe').then((module) => {
        const pswp = new module.default({
          dataSource: this.images,
          index
        })
        pswp.init()
      })
    }
  }
}
</script>

<style scoped>
.gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
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
</style>

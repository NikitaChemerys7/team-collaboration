<template>
    <div class="conference-card">
        <div class="conference-image" v-if="conference.hero_image">
            <img :src="getHeroImageUrl(conference.hero_image)" :alt="conference.title">
        </div>
        <div class="conference-content">
            <h3>{{ conference.title }}</h3>
            <div class="conference-details">
                <p class="date">
                    <i class="fas fa-calendar"></i>
                    {{ formatDate(conference.date) }}
                </p>
                <p class="location">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ conference.location }}
                </p>
            </div>
            <p class="description" v-html="truncateDescription(conference.description)"></p>
            <router-link :to="`/conference/${conference.id}`" class="view-details">
                View Details
                <i class="fas fa-arrow-right"></i>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { API_URL } from '../config'

const props = defineProps({
    conference: {
        type: Object,
        required: true
    }
})

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

function truncateDescription(description) {
    if (!description) return ''
    const strippedContent = description.replace(/<[^>]*>/g, '')
        .replace(/&nbsp;/g, ' ')
        .replace(/&amp;/g, '&')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
    
    const lines = strippedContent.split('\n').filter(line => line.trim())
    const truncatedLines = lines.slice(0, 3).map(line => {
        return line.length > 100 ? line.substring(0, 100) + '...' : line
    })
    
    return truncatedLines.join('\n') + (lines.length > 3 ? '...' : '')
}
</script>

<style scoped>
.conference-card {
    background-color: var(--color-surface);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
    display: flex;
    flex-direction: column;
}

.conference-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.conference-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.conference-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-fast);
}

.conference-card:hover .conference-image img {
    transform: scale(1.05);
}

.conference-content {
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.conference-content h3 {
    margin: 0 0 var(--spacing-md);
    color: var(--color-text);
    font-size: 1.25rem;
}

.conference-details {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    color: var(--color-text-secondary);
    font-size: 0.9rem;
}

.conference-details p {
    display: flex;
    align-items: center;
    margin: 0;
}

.conference-details i {
    color: var(--color-primary);
}

.description {
    color: var(--color-text-secondary);
    margin-bottom: var(--spacing-lg);
    line-height: 1.5;
}

.view-details {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
    margin-top: auto;
}

.view-details:hover {
    color: var(--color-primary-dark);
}

.view-details i {
    transition: transform var(--transition-fast);
}

.view-details:hover i {
    transform: translateX(4px);
}
</style>
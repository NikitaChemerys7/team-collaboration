<template>
    <div class="conference-card">
        <div class="conference-image" v-if="conference.hero_image">
            <img :src="conference.hero_image" :alt="conference.title">
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

const props = defineProps({
    conference: {
        type: Object,
        required: true
    }
})

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
    return description.length > 150 ? description.substring(0, 150) + '...' : description
}
</script>

<style scoped>
.conference-card {
    background-color: var(--color-surface);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
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
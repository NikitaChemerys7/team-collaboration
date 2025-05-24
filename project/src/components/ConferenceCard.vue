<template>
    <div class="conference-card">
        <h3>{{ conference.title }}</h3>
        <p>{{ conference.date }} – {{ conference.location }}</p>
        <router-link :to="`/conference/${conference.id}`">View Details</router-link>
    </div>
</template>

<script>
export default {
    name: 'ConferenceCard',
    props: {
        conference: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>
.conference-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
}
</style>
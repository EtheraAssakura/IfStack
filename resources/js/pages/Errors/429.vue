<script setup lang="ts">
import ErrorPage from '@/components/ErrorPage.vue';
</script>

<template>
    <ErrorPage
        code="429"
        title="Trop de requêtes"
        message="Vous avez envoyé trop de requêtes. Veuillez patienter quelques instants avant de réessayer."
    />
</template> 
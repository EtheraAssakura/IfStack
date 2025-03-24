<script setup lang="ts">
import ErrorPage from '@/Components/ErrorPage.vue';
</script>

<template>
    <ErrorPage
        code="500"
        title="Erreur serveur"
        message="Une erreur est survenue sur notre serveur. Veuillez réessayer plus tard."
    />
</template> 
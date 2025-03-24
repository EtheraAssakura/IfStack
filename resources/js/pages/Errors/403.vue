<script setup lang="ts">
import ErrorPage from '@/Components/ErrorPage.vue';
</script>

<template>
    <ErrorPage
        code="403"
        title="Accès interdit"
        message="Vous n'avez pas les permissions nécessaires pour accéder à cette page."
    />
</template> 
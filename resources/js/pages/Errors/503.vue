<script setup lang="ts">
import ErrorPage from '@/components/ErrorPage.vue';
</script>

<template>
    <ErrorPage
        code="503"
        title="Service indisponible"
        message="Le service est temporairement indisponible. Veuillez réessayer plus tard."
    />
</template> 
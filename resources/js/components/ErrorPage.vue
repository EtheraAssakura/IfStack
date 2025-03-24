<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    code: string;
    title: string;
    message: string;
}

defineProps<Props>();
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-lg">
            <div class="text-center">
                <h1 class="text-9xl font-bold text-gray-900">{{ code }}</h1>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    {{ title }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ message }}
                </p>
            </div>
            <div class="mt-8 text-center">
                <Link
                    :href="route('home')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Retour à l'accueil
                </Link>
            </div>
        </div>
    </div>
</template> 
<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    title?: string;
    breadcrumbs?: BreadcrumbItemType[];
}>();

defineOptions({
    name: 'UserLayout'
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('welcome')">
                                <img class="h-8 w-auto" src="/Logo.png" alt="IfStack" />
                            </Link>
                        </div>
                    </div>

                    <!-- Menu utilisateur -->
                    <div class="flex items-center">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-gray-600 hover:text-gray-900 text-sm font-medium"
                        >
                            Se déconnecter
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- En-tête de page -->
        <header v-if="$slots.header || breadcrumbs" class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
                <!-- Breadcrumbs -->
                <nav v-if="breadcrumbs" class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li v-for="(item, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                            <Link
                                v-if="index < breadcrumbs.length - 1"
                                :href="item.href"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600"
                            >
                                {{ item.title }}
                                <svg class="w-3 h-3 mx-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            </Link>
                            <span v-else class="text-sm font-medium text-gray-500">{{ item.title }}</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </header>

        <!-- Contenu principal -->
        <main>
            <slot />
        </main>
    </div>
</template> 
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    sites: Array<{
        name: string;
        route: string;
        params: {
            site: string;
        };
    }>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Stocks',
        href: '/stocks/sites',
    },
];

// Fonction pour vérifier les paramètres avant la navigation
const handleSiteClick = (site: Props['sites'][0]) => {
    console.log('Navigation vers:', {
        route: site.route,
        params: site.params,
        fullUrl: route(site.route, site.params)
    });
};
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <Head>
            <title>Sélection du site - ISFAC</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-center min-h-[60vh]">
                    <h1 class="text-3xl font-bold tracking-tight mb-8">Sélectionnez un site pour voir son stock</h1>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Button
                            v-for="site in sites"
                            :key="site.name"
                            class="w-64 h-32 text-lg"
                            as-child
                            @click="handleSiteClick(site)"
                        >
                            <Link :href="route(site.route, site.params)">
                                {{ site.name }}
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template> 
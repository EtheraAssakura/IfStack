<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useInitials } from '@/composables/useInitials';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    title?: string;
    breadcrumbs?: BreadcrumbItemType[];
}>();

defineOptions({
    name: 'UserLayout'
});

const page = usePage();
const auth = page.props.auth;
const { getInitials } = useInitials();
</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- Navigation -->
        <nav class="bg-card shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('welcome')">
                                <img class="h-8 w-auto" src="/Logo.png" alt="IfStack" />
                            </Link>
                        </div>
                        <!-- Bouton Accueil -->
                        <div class="ml-4 flex items-center">
                            <Link :href="route('welcome')">
                                <Button variant="ghost">Accueil</Button>
                            </Link>
                        </div>
                    </div>

                    <!-- Menu utilisateur -->
                    <div class="flex items-center">
                        <DropdownMenu>
                            <DropdownMenuTrigger :as-child="true">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                                >
                                    <Avatar class="size-8 overflow-hidden rounded-full">
                                        <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                        <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                            {{ getInitials(auth.user?.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <UserMenuContent :user="auth.user" />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>
        </nav>

        <!-- En-tête de page -->
        <header v-if="$slots.header || breadcrumbs" class="bg-card shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
                <!-- Breadcrumbs -->
                <nav v-if="breadcrumbs" class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li v-for="(item, index) in breadcrumbs" :key="index" class="inline-flex items-center">
                            <Link
                                v-if="index < breadcrumbs.length - 1"
                                :href="item.href"
                                class="inline-flex items-center text-sm font-medium text-muted-foreground hover:text-primary"
                            >
                                {{ item.title }}
                                <svg class="w-3 h-3 mx-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            </Link>
                            <span v-else class="text-sm font-medium text-muted-foreground">{{ item.title }}</span>
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
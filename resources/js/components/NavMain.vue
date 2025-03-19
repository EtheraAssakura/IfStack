<script setup lang="ts">
import { SidebarGroup, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarMenuSub } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <SidebarMenuItem v-if="!item.children">
                    <SidebarMenuButton as-child :is-active="item.href === page.url">
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem v-else>
                    <SidebarMenuButton as-child :is-active="item.href === page.url">
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                    <SidebarMenuSub>
                        <SidebarMenuItem v-for="child in item.children" :key="child.title">
                            <SidebarMenuButton as-child :is-active="child.href === page.url">
                                <Link :href="child.href">
                                    <span>{{ child.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenuSub>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>

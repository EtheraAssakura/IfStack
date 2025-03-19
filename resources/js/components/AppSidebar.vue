<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    Bell,
    BookOpen,
    Boxes,
    Building2,
    ClipboardList,
    FileBarChart,
    LayoutGrid,
    Package,
    Scale,
    Shield,
    Truck,
    Users
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Tableau de bord',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Fournitures',
        href: route('fournitures.index'),
        icon: Package,
    },
    {
        title: 'Etablissements',
        href: route('etablissements.index'),
        icon: Building2,
    },
    {
        title: 'Stocks',
        href: route('stocks.index'),
        icon: Boxes,
    },
    {
        title: 'Commandes',
        href: route('orders.index'),
        icon: ClipboardList,
    },
    {
        title: 'Livraisons',
        href: route('livraisons.index'),
        icon: Truck,
    },
    {
        title: 'Alertes',
        href: route('notifications.index'),
        icon: Bell,
    },
    {
        title: 'Rapports',
        href: route('rapports.consommation'),
        icon: FileBarChart,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Utilisateurs',
        href: route('utilisateurs.index'),
        icon: Users,
    },
    {
        title: 'Rôles',
        href: route('roles.index'),
        icon: Shield
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Mentions légales',
        href: '/legal',
        icon: Scale,
    },
    {
        title: 'Politique de confidentialité',
        href: '/privacy',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo class="h-6 w-auto" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Navigation principale -->
             <div class="mt-4">
                <h4 class="mb-1 px-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Gestion
                </h4>
                <NavMain :items="mainNavItems" />
            </div>
            <!-- Navigation admin -->
            <div class="mt-4">
                <h4 class="mb-1 px-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Administration
                </h4>
                <NavMain :items="adminNavItems" />
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

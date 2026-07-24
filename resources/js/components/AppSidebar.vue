<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    Blocks,
    BookOpen,
    BookMarked,
    Bot,
    FolderGit2,
    LayoutGrid,
    Layers,
    Library,
    Rocket,
    Shield,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboardUrl.value,
        icon: LayoutGrid,
    },
    {
        title: 'Workspaces',
        href: '/workspaces',
        icon: Layers,
    },
    {
        title: 'AI Agents',
        href: '/agents',
        icon: Bot,
    },
]);

const engineeringNavItems: NavItem[] = [
    {
        title: 'Architecture',
        href: '/architecture',
        icon: BookMarked,
    },
    {
        title: 'Source Control',
        href: '/source-control',
        icon: FolderGit2,
    },
    {
        title: 'Deployments',
        href: '/deployments',
        icon: Rocket,
    },
];

const intelligenceNavItems: NavItem[] = [
    {
        title: 'Knowledge Hub',
        href: '/knowledge',
        icon: Library,
    },
    {
        title: 'Monitoring',
        href: '/monitoring',
        icon: Activity,
    },
    {
        title: 'Marketplace',
        href: '/marketplace',
        icon: Blocks,
    },
];

const governanceNavItems: NavItem[] = [
    {
        title: 'Governance',
        href: '/governance/api-keys',
        icon: Shield,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
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
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <SidebarMenu>
                <SidebarMenuItem>
                    <TeamSwitcher />
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" label="Platform" />
            <NavMain :items="engineeringNavItems" label="Engineering" />
            <NavMain :items="intelligenceNavItems" label="Intelligence" />
            <NavMain :items="governanceNavItems" label="Administration" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

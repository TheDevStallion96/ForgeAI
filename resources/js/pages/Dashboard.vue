<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
    Activity,
    ArrowRight,
    Bot,
    Cpu,
    Layers,
    Shield,
    TrendingUp,
    Wallet,
} from '@lucide/vue'
import PendingInvitationsModal from '@/components/PendingInvitationsModal.vue'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { dashboard } from '@/routes'
import type { DashboardInvitation, Team } from '@/types'

defineProps<{
    pendingInvitations?: DashboardInvitation[]
}>()

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam ? dashboard(props.currentTeam.slug) : '/',
            },
        ],
    }),
})

const stats = [
    { label: 'Active Agents', value: '4', icon: Bot, change: '+2 this month', color: 'text-indigo-500 bg-indigo-100 dark:bg-indigo-950/50' },
    { label: 'Token Usage', value: '847K', icon: Wallet, change: '62% of budget', color: 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50' },
    { label: 'Executions', value: '1,283', icon: Cpu, change: '+18% vs last month', color: 'text-amber-500 bg-amber-100 dark:bg-amber-950/50' },
    { label: 'Active Workspaces', value: '3', icon: Layers, change: '2 with recent activity', color: 'text-sky-500 bg-sky-100 dark:bg-sky-950/50' },
]

const recentActivity = [
    { event: 'Agent "Code Reviewer" completed session', time: '2 min ago', type: 'agent' },
    { event: 'New workspace "API Gateway" created', time: '15 min ago', type: 'workspace' },
    { event: 'Budget threshold reached (80%)', time: '1 hour ago', type: 'governance' },
    { event: 'Deployment to staging succeeded', time: '2 hours ago', type: 'deploy' },
    { event: 'API key for OpenAI rotated', time: '3 hours ago', type: 'governance' },
]

const quickLinks = [
    { title: 'AI Agent Studio', href: '/agents', icon: Bot, desc: 'Create and manage agents' },
    { title: 'Workspaces', href: '/workspaces', icon: Layers, desc: 'Project boards and tasks' },
    { title: 'Token Budget', href: '/governance/budget', icon: Wallet, desc: 'Monitor monthly usage' },
    { title: 'Audit Logs', href: '/governance/audit-logs', icon: Shield, desc: 'Security event trail' },
]
</script>

<template>
    <Head title="Dashboard" />

    <PendingInvitationsModal
        v-if="pendingInvitations && pendingInvitations.length > 0"
        :invitations="pendingInvitations"
    />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Overview of your organization's activity and health.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="stat in stats" :key="stat.label">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        {{ stat.label }}
                    </CardTitle>
                    <div class="rounded-lg p-2" :class="stat.color">
                        <component :is="stat.icon" class="h-4 w-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stat.value }}</div>
                    <p class="mt-1 text-xs text-muted-foreground flex items-center gap-1">
                        <TrendingUp class="h-3 w-3 text-emerald-500" />
                        {{ stat.change }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <Activity class="h-4 w-4" />
                        Recent Activity
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div
                            v-for="(item, i) in recentActivity"
                            :key="i"
                            class="flex items-center gap-3 border-b pb-3 last:border-0 last:pb-0"
                        >
                            <div
                                class="flex size-8 shrink-0 items-center justify-center rounded-full"
                                :class="item.type === 'agent' ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50' :
                                    item.type === 'workspace' ? 'bg-sky-100 text-sky-600 dark:bg-sky-950/50' :
                                    'bg-amber-100 text-amber-600 dark:bg-amber-950/50'"
                            >
                                <component
                                    :is="item.type === 'agent' ? Bot :
                                        item.type === 'workspace' ? Layers : Shield"
                                    class="h-4 w-4"
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm">{{ item.event }}</p>
                                <p class="text-xs text-muted-foreground">{{ item.time }}</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <Layers class="h-4 w-4" />
                        Quick Navigation
                    </CardTitle>
                    <CardDescription>Jump to key areas</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-3">
                        <Link
                            v-for="link in quickLinks"
                            :key="link.title"
                            :href="link.href"
                            class="group flex items-center gap-3 rounded-lg border p-3 transition-all hover:shadow-md hover:border-primary/50"
                        >
                            <div class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <component :is="link.icon" class="h-4 w-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium">{{ link.title }}</p>
                                <p class="text-xs text-muted-foreground">{{ link.desc }}</p>
                            </div>
                            <ArrowRight class="h-4 w-4 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity" />
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

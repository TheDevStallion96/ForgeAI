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

type Stat = {
    label: string
    value: string
    change: string
    color: string
}

type ActivityItem = {
    event: string
    time: string
    type: string
}

const props = defineProps<{
    pendingInvitations?: DashboardInvitation[]
    stats: Stat[]
    recentActivity: ActivityItem[]
}>()

const iconMap: Record<string, typeof Bot> = {
    'Active Agents': Bot,
    'Token Usage': Wallet,
    'Executions': Cpu,
    'Active Workspaces': Layers,
}

defineOptions({
    layout: (pr: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: pr.currentTeam ? dashboard(pr.currentTeam.slug) : '/',
            },
        ],
    }),
})

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
                        <component :is="iconMap[stat.label] || Bot" class="h-4 w-4" />
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

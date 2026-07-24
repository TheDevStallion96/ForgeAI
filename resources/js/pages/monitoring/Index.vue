<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    Activity,
    AlertTriangle,
    Cpu,
    Server,
    Timer,
} from '@lucide/vue'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import monitoring from '@/routes/monitoring'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Monitoring', href: monitoring.index().url },
        ],
    },
})

const metrics = [
    { label: 'API Response Time', value: '245ms', icon: Timer, change: '-12%', color: 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50' },
    { label: 'Active Queues', value: '3', icon: Server, change: '1,245 jobs', color: 'text-sky-500 bg-sky-100 dark:bg-sky-950/50' },
    { label: 'Error Rate', value: '0.02%', icon: AlertTriangle, change: 'Below threshold', color: 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50' },
    { label: 'Avg Token Latency', value: '1.2s', icon: Cpu, change: '+5% this hour', color: 'text-amber-500 bg-amber-100 dark:bg-amber-950/50' },
]

const recentAlerts = [
    { severity: 'warning', message: 'Token budget at 85% for Forge AI', time: '10 min ago' },
    { severity: 'info', message: 'Agent "Code Reviewer" completed successfully', time: '25 min ago' },
    { severity: 'critical', message: 'Deployment to production failed: forge-ai/agents', time: '1h ago' },
    { severity: 'warning', message: 'API key "OpenAI Production" expires in 7 days', time: '2h ago' },
]
</script>

<template>
    <Head title="Monitoring" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Monitoring & Telemetry</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Real-time metrics, alerting, queue health, and LLM evaluation benchmarks.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="metric in metrics" :key="metric.label">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">{{ metric.label }}</CardTitle>
                    <div class="rounded-lg p-2" :class="metric.color">
                        <component :is="metric.icon" class="h-4 w-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ metric.value }}</div>
                    <p class="text-xs text-muted-foreground flex items-center gap-1 mt-1">
                        <Activity class="h-3 w-3" :class="metric.change.startsWith('+') ? 'text-red-500' : 'text-emerald-500'" />
                        {{ metric.change }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <Server class="h-4 w-4" />
                        Queue Workers
                    </CardTitle>
                    <CardDescription>Horizon queue worker metrics</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2">
                                <div class="size-2 rounded-full bg-emerald-500" />
                                Default Queue
                            </span>
                            <span class="text-muted-foreground">2 workers · 0 jobs</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2">
                                <div class="size-2 rounded-full bg-emerald-500" />
                                AI Completions
                            </span>
                            <span class="text-muted-foreground">4 workers · 3 jobs</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2">
                                <div class="size-2 rounded-full bg-amber-500" />
                                Knowledge Indexing
                            </span>
                            <span class="text-muted-foreground">1 worker · 12 jobs</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2">
                                <div class="size-2 rounded-full bg-red-500" />
                                Failed Jobs
                            </span>
                            <span class="text-muted-foreground">0 workers · 2 failed</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <AlertTriangle class="h-4 w-4" />
                        Recent Alerts
                    </CardTitle>
                    <CardDescription>System notifications</CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-for="(alert, i) in recentAlerts" :key="i" class="flex items-start gap-3 pb-3 border-b last:border-0 last:pb-0">
                        <div
                            class="flex size-7 shrink-0 items-center justify-center rounded-full mt-0.5"
                            :class="alert.severity === 'critical' ? 'bg-red-100 text-red-600 dark:bg-red-950/50' :
                                alert.severity === 'warning' ? 'bg-amber-100 text-amber-600 dark:bg-amber-950/50' :
                                'bg-sky-100 text-sky-600 dark:bg-sky-950/50'"
                        >
                            <component :is="alert.severity === 'critical' ? AlertTriangle : Activity" class="h-3.5 w-3.5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm">{{ alert.message }}</p>
                            <p class="text-xs text-muted-foreground">{{ alert.time }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

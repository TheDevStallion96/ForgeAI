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

type Metric = {
    label: string
    value: string
    icon: string
    change: string
    color: string
}

type AlertItem = {
    severity: string
    message: string
    time: string
}

type AuditItem = {
    message: string
    time: string
}

defineProps<{
    metrics: Metric[]
    alerts: AlertItem[]
    recentAudit: AuditItem[]
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Monitoring', href: monitoring.index().url },
        ],
    },
})

const iconMap: Record<string, typeof Timer> = {
    Timer,
    Server,
    AlertTriangle,
    Cpu,
}

const severityColors: Record<string, string> = {
    critical: 'bg-red-100 text-red-600 dark:bg-red-950/50',
    warning: 'bg-amber-100 text-amber-600 dark:bg-amber-950/50',
    info: 'bg-sky-100 text-sky-600 dark:bg-sky-950/50',
}
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
                        <component :is="iconMap[metric.icon] || Activity" class="h-4 w-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ metric.value }}</div>
                    <p class="text-xs text-muted-foreground flex items-center gap-1 mt-1">
                        <Activity class="h-3 w-3 text-emerald-500" />
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
                        Recent Audit Events
                    </CardTitle>
                    <CardDescription>System activity</CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-for="(event, i) in recentAudit" :key="i" class="flex items-start gap-3 border-b pb-3 last:border-0 last:pb-0">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-950/50 mt-0.5">
                            <Activity class="h-3.5 w-3.5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm">{{ event.message }}</p>
                            <p class="text-xs text-muted-foreground">{{ event.time }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <AlertTriangle class="h-4 w-4" />
                        Active Alerts
                    </CardTitle>
                    <CardDescription>Unacknowledged notifications</CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-if="alerts.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                        No active alerts.
                    </div>
                    <div v-for="(alert, i) in alerts" :key="i" class="flex items-start gap-3 border-b pb-3 last:border-0 last:pb-0">
                        <div class="flex size-7 shrink-0 items-center justify-center rounded-full mt-0.5" :class="severityColors[alert.severity] || severityColors.info">
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

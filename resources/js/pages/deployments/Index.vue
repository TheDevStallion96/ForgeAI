<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    CheckCircle2,
    Clock,
    Rocket,
    Server,
    XCircle,
} from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import deployments from '@/routes/deployments'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Deployments', href: deployments.index().url },
        ],
    },
})

const pipelines = [
    { name: 'forge-ai/core', env: 'production', status: 'running', version: 'v1.2.3', started: '5 min ago' },
    { name: 'forge-ai/frontend', env: 'staging', status: 'success', version: 'v2.1.0', started: '1h ago' },
    { name: 'forge-ai/agents', env: 'production', status: 'failed', version: 'v0.5.0', started: '3h ago' },
    { name: 'forge-ai/docs', env: 'staging', status: 'pending', version: 'v1.0.0', started: '--' },
]

const envColors: Record<string, string> = {
    production: 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-300 border-red-200 dark:border-red-800',
    staging: 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
    development: 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border-sky-200 dark:border-sky-800',
}

const statusIcons: Record<string, typeof CheckCircle2> = {
    success: CheckCircle2,
    running: Clock,
    failed: XCircle,
    pending: Clock,
}

const statusColors: Record<string, string> = {
    success: 'text-emerald-500',
    running: 'text-sky-500',
    failed: 'text-red-500',
    pending: 'text-amber-500',
}
</script>

<template>
    <Head title="Deployments" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Deployments</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Release pipelines, environment health, and deployment history.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Total Pipelines</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold">{{ pipelines.length }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Production</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold text-emerald-500">2 Healthy</CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Staging</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold text-amber-500">2 Active</CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Failed</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold text-red-500">1</CardContent>
            </Card>
        </div>

        <div class="space-y-3">
            <div v-for="pipeline in pipelines" :key="pipeline.name" class="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <Rocket class="h-5 w-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium font-mono">{{ pipeline.name }}</span>
                        <Badge variant="outline" :class="envColors[pipeline.env] ?? ''" class="capitalize">
                            <Server class="mr-1 h-3 w-3" />
                            {{ pipeline.env }}
                        </Badge>
                        <Badge variant="outline">{{ pipeline.version }}</Badge>
                    </div>
                    <p class="mt-0.5 text-xs text-muted-foreground">Started {{ pipeline.started }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <component :is="statusIcons[pipeline.status] || Clock" class="h-5 w-5" :class="statusColors[pipeline.status] || 'text-muted-foreground'" />
                    <span class="text-sm capitalize text-muted-foreground">{{ pipeline.status }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

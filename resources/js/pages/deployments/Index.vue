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

type Pipeline = {
    id: number
    name: string
    environment: string
    status: string
    version: string | null
    started_at: string
    completed_at: string | null
}

defineProps<{
    pipelines: Pipeline[]
    stats: {
        total: number
        production_healthy: number
        staging_active: number
        failed: number
    }
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Deployments', href: deployments.index().url },
        ],
    },
})

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
                <CardContent class="text-2xl font-bold">{{ stats.total }}</CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Production</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold text-emerald-500">{{ stats.production_healthy }} Healthy</CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Staging</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold text-amber-500">{{ stats.staging_active }} Active</CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Failed</CardTitle>
                </CardHeader>
                <CardContent class="text-2xl font-bold text-red-500">{{ stats.failed }}</CardContent>
            </Card>
        </div>

        <div v-if="pipelines.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <Rocket class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No pipelines yet</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Create a deployment pipeline to get started.
            </p>
        </div>

        <div v-else class="space-y-3">
            <div v-for="pipeline in pipelines" :key="pipeline.id" class="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <Rocket class="h-5 w-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium font-mono">{{ pipeline.name }}</span>
                        <Badge variant="outline" :class="envColors[pipeline.environment] ?? ''" class="capitalize">
                            <Server class="mr-1 h-3 w-3" />
                            {{ pipeline.environment }}
                        </Badge>
                        <Badge v-if="pipeline.version" variant="outline">{{ pipeline.version }}</Badge>
                    </div>
                    <p class="mt-0.5 text-xs text-muted-foreground">Started {{ pipeline.started_at }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <component :is="statusIcons[pipeline.status] || Clock" class="h-5 w-5" :class="statusColors[pipeline.status] || 'text-muted-foreground'" />
                    <span class="text-sm capitalize text-muted-foreground">{{ pipeline.status }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    BookMarked,
    FileText,
    GitMerge,
    GitBranch,
    Plus,
    Share2,
} from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import architecture from '@/routes/architecture'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Architecture Studio', href: architecture.index().url },
        ],
    },
})

const adrs = [
    { id: 1, title: 'ADR-0001: Tech Stack Decision', status: 'accepted', date: '2026-06-15', context: 'Laravel 13 + Vue 3 + Inertia' },
    { id: 2, title: 'ADR-0002: AI Engine Architecture', status: 'accepted', date: '2026-06-20', context: 'Multi-provider AI orchestration' },
    { id: 3, title: 'ADR-0003: Modular Monolith', status: 'accepted', date: '2026-06-25', context: 'Domain-driven module boundaries' },
    { id: 4, title: 'ADR-0004: Vector Storage Strategy', status: 'proposed', date: '2026-07-01', context: 'pgvector for embeddings' },
    { id: 5, title: 'ADR-0005: Real-Time SSE Protocol', status: 'draft', date: '2026-07-10', context: 'Server-Sent Events for streaming' },
]

const statusStyles: Record<string, string> = {
    accepted: 'border-emerald-400 text-emerald-600 dark:text-emerald-400',
    proposed: 'border-amber-400 text-amber-600 dark:text-amber-400',
    draft: 'border-sky-400 text-sky-600 dark:text-sky-400',
    deprecated: 'border-red-400 text-red-600 dark:text-red-400',
}

const statusIcons: Record<string, typeof BookMarked> = {
    accepted: BookMarked,
    proposed: FileText,
    draft: FileText,
    deprecated: FileText,
}
</script>

<template>
    <Head title="Architecture Studio" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Architecture Studio</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Architectural Decision Records, domain context maps, and C4 diagrams.
                </p>
            </div>
            <Button size="sm">
                <Plus class="mr-1.5 h-4 w-4" />
                New ADR
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50">
                        <BookMarked class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3 text-base">ADR Log</CardTitle>
                    <CardDescription>{{ adrs.length }} records</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <div v-for="adr in adrs" :key="adr.id" class="flex items-center justify-between rounded-lg border p-2.5 text-sm hover:bg-muted/50 cursor-pointer transition-colors">
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ adr.title }}</p>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ adr.context }}</p>
                            </div>
                            <Badge variant="outline" :class="statusStyles[adr.status]" class="shrink-0 ml-2 capitalize">
                                <component :is="statusIcons[adr.status]" class="mr-1 h-3 w-3" />
                                {{ adr.status }}
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-950/50">
                        <Share2 class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3 text-base">Domain Context Map</CardTitle>
                    <CardDescription>Module boundaries and relationships</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed bg-muted/30 py-12">
                        <Share2 class="h-10 w-10 text-muted-foreground/50 mb-2" />
                        <p class="text-sm text-muted-foreground">C4 diagram viewer</p>
                        <p class="text-xs text-muted-foreground/60 mt-1">Interactive topology map</p>
                        <Button variant="secondary" size="sm" class="mt-4">
                            <GitMerge class="mr-1.5 h-4 w-4" />
                            Generate
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-950/50">
                        <GitMerge class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3 text-base">Boundary Audit</CardTitle>
                    <CardDescription>Module compliance checking</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span>Core Platform</span>
                            <Badge variant="outline" class="border-emerald-400 text-emerald-600">Compliant</Badge>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span>AI Engine</span>
                            <Badge variant="outline" class="border-emerald-400 text-emerald-600">Compliant</Badge>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span>Agent Framework</span>
                            <Badge variant="outline" class="border-amber-400 text-amber-600">1 Violation</Badge>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span>Workspace Module</span>
                            <Badge variant="outline" class="border-emerald-400 text-emerald-600">Compliant</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

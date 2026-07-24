<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    BookOpen,
    FileText,
    Search,
    Upload,
} from '@lucide/vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import knowledge from '@/routes/knowledge'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Knowledge Hub', href: knowledge.index().url },
        ],
    },
})

const documents = [
    { id: 1, name: 'Laravel Documentation.pdf', type: 'pdf', pages: 48, status: 'indexed', chunks: 120 },
    { id: 2, name: 'Architecture Blueprint.md', type: 'markdown', pages: 12, status: 'indexed', chunks: 35 },
    { id: 3, name: 'API Specification.yaml', type: 'yaml', pages: 24, status: 'indexing', chunks: null },
    { id: 4, name: 'Engineering Handbook.pdf', type: 'pdf', pages: 156, status: 'pending', chunks: null },
    { id: 5, name: 'Deployment Runbook.md', type: 'markdown', pages: 8, status: 'indexed', chunks: 22 },
]
</script>

<template>
    <Head title="Knowledge Hub" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Knowledge Hub</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage document ingestion, vector embeddings, and semantic search.
                </p>
            </div>
            <Button size="sm">
                <Upload class="mr-1.5 h-4 w-4" />
                Upload Document
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Total Documents</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ documents.length }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Vector Chunks</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">177</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Indexing Status</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-2 text-2xl font-bold text-emerald-500">
                        <span class="size-2 rounded-full bg-emerald-500" />
                        80% Complete
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Search documents..." class="pl-8" />
            </div>
        </div>

        <div class="space-y-2">
            <div v-for="doc in documents" :key="doc.id" class="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <component :is="doc.type === 'pdf' ? BookOpen : FileText" class="h-5 w-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">{{ doc.name }}</p>
                    <p class="text-xs text-muted-foreground">{{ doc.pages }} pages{{ doc.chunks ? ` · ${doc.chunks} vector chunks` : '' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="doc.status === 'indexed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' :
                            doc.status === 'indexing' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/30 dark:text-sky-400' :
                            'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400'"
                    >
                        <span class="mr-1 size-1.5 rounded-full" :class="doc.status === 'indexed' ? 'bg-emerald-500' : doc.status === 'indexing' ? 'bg-sky-500' : 'bg-amber-500'" />
                        {{ doc.status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

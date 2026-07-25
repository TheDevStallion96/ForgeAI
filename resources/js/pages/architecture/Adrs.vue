<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    BookMarked,
    FileText,
} from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import architecture from '@/routes/architecture'

type AdrRecord = {
    id: number
    title: string
    adr_number: number
    status: string
    context: string | null
    decision: string | null
    consequences: string | null
    created_at: string
}

defineProps<{
    adrs: {
        data: AdrRecord[]
    }
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Architecture Studio', href: architecture.index().url },
            { title: 'ADR Log', href: architecture.adrs().url },
        ],
    },
})

const statusStyles: Record<string, string> = {
    accepted: 'border-emerald-400 text-emerald-600 dark:text-emerald-400',
    proposed: 'border-amber-400 text-amber-600 dark:text-amber-400',
    draft: 'border-sky-400 text-sky-600 dark:text-sky-400',
    deprecated: 'border-red-400 text-red-600 dark:text-red-400',
}
</script>

<template>
    <Head title="ADR Log" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">ADR Log</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    All Architectural Decision Records.
                </p>
            </div>
        </div>

        <div class="space-y-2">
            <div
                v-for="adr in adrs.data"
                :key="adr.id"
                class="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50"
            >
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <FileText class="h-5 w-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">ADR-{{ adr.adr_number }}: {{ adr.title }}</p>
                    <p class="text-xs text-muted-foreground mt-0.5">{{ adr.context?.slice(0, 120) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge variant="outline" :class="statusStyles[adr.status]" class="capitalize">
                        <component :is="adr.status === 'accepted' ? BookMarked : FileText" class="mr-1 h-3 w-3" />
                        {{ adr.status }}
                    </Badge>
                </div>
            </div>
            <div v-if="adrs.data.length === 0" class="py-12 text-center text-sm text-muted-foreground">
                No ADRs recorded yet.
            </div>
        </div>
    </div>
</template>

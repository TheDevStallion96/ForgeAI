<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { BookMarked } from '@lucide/vue'
import architecture from '@/routes/architecture'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Architecture Studio', href: architecture.index().url },
            { title: 'ADR Log', href: architecture.adrs().url },
        ],
    },
})
</script>

<template>
    <Head title="ADR Log" />

    <div class="flex flex-1 flex-col items-center justify-center gap-4 py-20">
        <BookMarked class="h-12 w-12 text-muted-foreground/50" />
        <h2 class="text-lg font-medium">ADR Log</h2>
        <p class="text-sm text-muted-foreground">Architectural Decision Records detail view coming soon.</p>
    </div>
</template>

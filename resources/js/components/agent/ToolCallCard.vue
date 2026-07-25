<script setup lang="ts">
import { ChevronDown, ChevronRight, Wrench } from '@lucide/vue'
import { ref } from 'vue'
import { Badge } from '@/components/ui/badge'

export type ToolCall = {
    id: string
    name: string
    args: Record<string, unknown>
    result?: string | null
    status: 'pending' | 'running' | 'completed' | 'failed'
}

defineProps<{
    tool: ToolCall
}>()

const expanded = ref(false)

const statusStyles: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
    running: 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
    completed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
    failed: 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-300',
}
</script>

<template>
    <div class="rounded-lg border bg-card text-sm">
        <button
            class="flex w-full items-center gap-2 px-3 py-2 text-left"
            @click="expanded = !expanded"
        >
            <Wrench class="h-3.5 w-3.5 text-muted-foreground" />
            <span class="font-medium">{{ tool.name }}</span>
            <Badge variant="outline" :class="statusStyles[tool.status] ?? ''">
                {{ tool.status }}
            </Badge>
            <component :is="expanded ? ChevronDown : ChevronRight" class="ml-auto h-3.5 w-3.5 text-muted-foreground" />
        </button>

        <div v-if="expanded" class="border-t px-3 py-2 space-y-2">
            <div>
                <p class="text-xs font-medium text-muted-foreground mb-1">Arguments</p>
                <pre class="overflow-x-auto rounded bg-muted p-2 text-xs">{{ JSON.stringify(tool.args, null, 2) }}</pre>
            </div>
            <div v-if="tool.result">
                <p class="text-xs font-medium text-muted-foreground mb-1">Result</p>
                <pre class="overflow-x-auto rounded bg-muted p-2 text-xs">{{ tool.result }}</pre>
            </div>
        </div>
    </div>
</template>

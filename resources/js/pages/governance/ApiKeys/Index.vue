<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { CalendarDays, Eye, EyeOff, Key, KeyRound, Plus, Trash2 } from '@lucide/vue'
import { ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
} from '@/components/ui/card'
import governance from '@/routes/governance'

defineProps<{
    keys: Array<{
        id: number
        provider: string
        name: string | null
        key_preview: string
        is_active: boolean
        last_used_at: string | null
    }>
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Governance', href: governance.apiKeys.index().url },
            { title: 'API Keys', href: governance.apiKeys.index().url },
        ],
    },
})

const visibleKeys = ref<Set<number>>(new Set())

function toggleVisibility(id: number) {
    if (visibleKeys.value.has(id)) {
        visibleKeys.value.delete(id)
    } else {
        visibleKeys.value.add(id)
    }
}

const providerColors: Record<string, string> = {
    openai: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
    anthropic: 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300',
    google: 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
    azure: 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
}
</script>

<template>
    <Head title="API Keys" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">API Keys</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage API keys for AI provider authentication.
                </p>
            </div>
            <Button size="sm">
                <Plus class="mr-1.5 h-4 w-4" />
                Add Key
            </Button>
        </div>

        <div v-if="keys.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <KeyRound class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No API keys</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Add provider API keys to enable AI agents.
            </p>
        </div>

        <div v-else class="space-y-3">
            <Card v-for="key in keys" :key="key.id">
                <CardContent class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Key class="h-5 w-5" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">{{ key.name ?? key.provider }}</span>
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                    :class="providerColors[key.provider] ?? 'bg-muted text-muted-foreground'"
                                >
                                    {{ key.provider }}
                                </span>
                                <Badge
                                    variant="outline"
                                    :class="key.is_active
                                        ? 'border-emerald-400 text-emerald-600'
                                        : 'border-red-400 text-red-600'"
                                >
                                    {{ key.is_active ? 'Active' : 'Revoked' }}
                                </Badge>
                            </div>
                            <div class="mt-1 flex items-center gap-3 text-xs text-muted-foreground">
                                <span class="font-mono">
                                    {{ visibleKeys.has(key.id) ? key.key_preview : '••••' + key.key_preview.slice(-4) }}
                                </span>
                                <button
                                    @click="toggleVisibility(key.id)"
                                    class="hover:text-foreground transition-colors"
                                >
                                    <component :is="visibleKeys.has(key.id) ? EyeOff : Eye" class="h-3.5 w-3.5" />
                                </button>
                                <span v-if="key.last_used_at" class="flex items-center gap-1">
                                    <CalendarDays class="h-3 w-3" />
                                    Last used {{ new Date(key.last_used_at).toLocaleDateString() }}
                                </span>
                                <span v-else class="italic">Never used</span>
                            </div>
                        </div>
                    </div>
                    <Button variant="ghost" size="icon" class="size-8 text-muted-foreground hover:text-red-600">
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

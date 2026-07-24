<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { AlertTriangle, ClipboardList, Clock, Search, ShieldAlert } from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import {
    Card,
    CardContent,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import governance from '@/routes/governance'

type Log = {
    id: number
    event_type: string
    payload: Record<string, unknown>
    metadata: Record<string, unknown>
    is_tampered: boolean
    created_at: string
}

defineProps<{
    logs: { data: Log[] }
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Governance', href: governance.apiKeys.index().url },
            { title: 'Audit Logs', href: governance.auditLogs.index().url },
        ],
    },
})

const eventStyles: Record<string, string> = {
    agent_created: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
    agent_updated: 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
    agent_deleted: 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-300',
    session_started: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300',
    session_completed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
    api_key_added: 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
    api_key_revoked: 'bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300',
    budget_updated: 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300',
    user_invited: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-300',
    user_joined: 'bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300',
}

function formatPayload(payload: Record<string, unknown>): string {
    try {
        return JSON.stringify(payload, null, 2)
    } catch {
        return String(payload)
    }
}
</script>

<template>
    <Head title="Audit Logs" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Audit Logs</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Immutable audit trail of all security and governance events.
                </p>
            </div>
            <div class="relative w-64">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Search events..." class="pl-8" />
            </div>
        </div>

        <div v-if="logs.data.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <ClipboardList class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No audit logs</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Audit events will appear here as actions are performed.
            </p>
        </div>

        <div v-else class="space-y-2">
            <Card v-for="log in logs.data" :key="log.id" :class="log.is_tampered ? 'border-red-400' : ''">
                <CardContent class="flex items-start gap-4 p-4">
                    <div
                        class="flex size-8 shrink-0 items-center justify-center rounded-full"
                        :class="log.is_tampered
                            ? 'bg-red-100 text-red-600 dark:bg-red-950/50 dark:text-red-400'
                            : 'bg-muted text-muted-foreground'"
                    >
                        <component :is="log.is_tampered ? ShieldAlert : ClipboardList" class="h-4 w-4" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <Badge
                                variant="outline"
                                :class="eventStyles[log.event_type] ?? 'bg-muted text-muted-foreground'"
                            >
                                {{ log.event_type.replace(/_/g, ' ') }}
                            </Badge>
                            <Badge v-if="log.is_tampered" variant="destructive" class="gap-1">
                                <AlertTriangle class="h-3 w-3" />
                                Tampered
                            </Badge>
                            <span class="ml-auto flex items-center gap-1 text-xs text-muted-foreground">
                                <Clock class="h-3 w-3" />
                                {{ new Date(log.created_at).toLocaleString() }}
                            </span>
                        </div>
                        <details class="mt-1">
                            <summary class="cursor-pointer text-xs text-muted-foreground hover:text-foreground">
                                View payload
                            </summary>
                            <pre class="mt-2 overflow-x-auto rounded-lg bg-muted p-3 text-xs">{{ formatPayload(log.payload) }}</pre>
                        </details>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

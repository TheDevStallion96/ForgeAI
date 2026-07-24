<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
    AlertCircle,
    Bot,
    CalendarDays,
    CheckCircle2,
    Clock,
    Cog,
    MessagesSquare,
} from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import {
    Card,
    CardContent,
} from '@/components/ui/card'
import agents from '@/routes/agents'

type Session = {
    id: number
    agent_name: string
    status: string
    message_count: number
    context_tokens: number
    created_at: string
    updated_at: string
}

defineProps<{
    sessions: { data: Session[] }
    agent: { id: number; name: string } | null
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'AI Agent Studio', href: agents.index().url },
            { title: 'Sessions', href: agents.sessions.index().url },
        ],
    },
})

const statusConfig: Record<string, { icon: typeof Clock; label: string; class: string }> = {
    running: { icon: Cog, label: 'Running', class: 'bg-sky-50 text-sky-700 dark:bg-sky-950/30 dark:text-sky-400 border-sky-200 dark:border-sky-800' },
    completed: { icon: CheckCircle2, label: 'Completed', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' },
    failed: { icon: AlertCircle, label: 'Failed', class: 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border-red-200 dark:border-red-800' },
    pending: { icon: Clock, label: 'Pending', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border-amber-200 dark:border-amber-800' },
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleString(undefined, {
        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit',
    })
}
</script>

<template>
    <Head title="Execution Sessions" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Execution Sessions
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    <template v-if="agent">Sessions for <span class="font-medium text-foreground">{{ agent.name }}</span></template>
                    <template v-else>All agent execution sessions across your organization</template>
                </p>
            </div>
        </div>

        <div v-if="sessions.data.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <MessagesSquare class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No sessions yet</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Sessions appear when agents are executed.
            </p>
        </div>

        <div v-else class="space-y-3">
            <Link
                v-for="session in sessions.data"
                :key="session.id"
                :href="agents.sessions.show(session).url"
                class="block"
            >
                <Card class="transition-all duration-200 hover:shadow-md hover:border-primary/50 cursor-pointer">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Bot class="h-5 w-5" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">{{ session.agent_name }}</span>
                                <Badge
                                    variant="outline"
                                    :class="statusConfig[session.status]?.class ?? ''"
                                    class="gap-1"
                                >
                                    <component :is="statusConfig[session.status]?.icon ?? Clock" class="h-3 w-3" />
                                    {{ statusConfig[session.status]?.label ?? session.status }}
                                </Badge>
                            </div>
                            <div class="mt-1 flex items-center gap-4 text-xs text-muted-foreground">
                                <span class="flex items-center gap-1">
                                    <MessagesSquare class="h-3.5 w-3.5" />
                                    {{ session.message_count }} messages
                                </span>
                                <span>{{ session.context_tokens.toLocaleString() }} tokens</span>
                                <span class="flex items-center gap-1">
                                    <CalendarDays class="h-3.5 w-3.5" />
                                    {{ formatDate(session.created_at) }}
                                </span>
                            </div>
                        </div>

                        <div class="shrink-0 text-muted-foreground">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>

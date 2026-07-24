<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    AlertCircle,
    Bot,
    CheckCircle2,
    Clock,
    Cog,
    MessagesSquare,
    User,
    Wrench,
} from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import agents from '@/routes/agents'

type Message = {
    id: number
    role: string
    content: string | null
    tool_calls: unknown[] | null
    created_at: string | null
}

defineProps<{
    session: {
        id: number
        agent: { id: number; name: string }
        status: string
        context_tokens: number
        created_at: string
    }
    messages: Message[]
}>()

defineOptions({
    layout: (props: { session: { agent: { name: string } } }) => ({
        breadcrumbs: [
            { title: 'AI Agent Studio', href: agents.index().url },
            { title: 'Sessions', href: agents.sessions.index().url },
            { title: props.session.agent.name, href: `/agents/sessions/${props.session.id}` },
        ],
    }),
})

const statusConfig: Record<string, { icon: typeof Clock; label: string; class: string }> = {
    running: { icon: Cog, label: 'Running', class: 'bg-sky-50 text-sky-700 dark:bg-sky-950/30 dark:text-sky-400' },
    completed: { icon: CheckCircle2, label: 'Completed', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' },
    failed: { icon: AlertCircle, label: 'Failed', class: 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400' },
    pending: { icon: Clock, label: 'Pending', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' },
}
</script>

<template>
    <Head :title="`Session: ${session.agent.name}`" />

    <div class="flex h-full flex-1 flex-col">
        <div class="flex items-center justify-between border-b px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <Bot class="h-5 w-5" />
                </div>
                <div>
                    <h1 class="text-lg font-semibold">{{ session.agent.name }}</h1>
                    <div class="flex items-center gap-3 text-xs text-muted-foreground">
                        <Badge variant="outline" :class="statusConfig[session.status]?.class ?? ''">
                            <component :is="statusConfig[session.status]?.icon ?? Clock" class="mr-1 h-3 w-3" />
                            {{ statusConfig[session.status]?.label ?? session.status }}
                        </Badge>
                        <span>{{ session.context_tokens.toLocaleString() }} tokens used</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            <div v-if="messages.length === 0" class="flex flex-col items-center justify-center py-20 text-muted-foreground">
                <MessagesSquare class="mb-3 h-8 w-8" />
                <p class="text-sm">No messages in this session yet.</p>
            </div>

            <div v-else class="mx-auto max-w-3xl space-y-4">
                <div
                    v-for="msg in messages"
                    :key="msg.id"
                    class="flex gap-3"
                    :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
                >
                    <div
                        class="flex size-8 shrink-0 items-center justify-center rounded-full"
                        :class="msg.role === 'user'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-muted text-muted-foreground'"
                    >
                        <component :is="msg.role === 'user' ? User : (msg.role === 'tool' ? Wrench : Bot)" class="h-4 w-4" />
                    </div>

                    <div
                        class="max-w-[75%] rounded-xl px-4 py-3 text-sm"
                        :class="msg.role === 'user'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-muted'"
                    >
                        <div v-if="msg.content" class="whitespace-pre-wrap">{{ msg.content }}</div>
                        <div v-if="msg.tool_calls" class="mt-2 space-y-1">
                            <div v-for="(tc, i) in msg.tool_calls" :key="i" class="flex items-center gap-1 text-xs opacity-70">
                                <Wrench class="h-3 w-3" />
                                Tool call #{{ i + 1 }}
                            </div>
                        </div>
                        <div v-if="msg.created_at" class="mt-1 text-right text-[10px] opacity-50">
                            {{ new Date(msg.created_at).toLocaleTimeString() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

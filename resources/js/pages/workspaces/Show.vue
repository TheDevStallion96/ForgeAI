<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import {
    Bot,
    BotMessageSquare,
    Clock,
    History,
    Info,
    Layers,
    MessagesSquare,
    Network,
    Plus,
    Sparkles,
} from '@lucide/vue'
import { computed, ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import ChatWindow from '@/components/agent/ChatWindow.vue'
import type { Message } from '@/components/agent/ChatWindow.vue'
import agentsRoutes from '@/routes/agents'

type Agent = {
    id: number
    name: string
    primary_model: string
}

type Session = {
    id: number
    agent_name: string
    status: string
    message_count: number
    created_at: string
}

type Workspace = {
    id: number
    name: string
    slug: string
    description: string | null
    status: string
}

const props = defineProps<{
    workspace: Workspace
    agents: Agent[]
    sessions: Session[]
}>()

defineOptions({
    layout: (pr: { workspace: Workspace }) => ({
        breadcrumbs: [
            { title: 'Workspaces', href: '/workspaces' },
            { title: pr.workspace.name, href: `/workspaces/${pr.workspace.id}` },
        ],
    }),
})

const selectedAgentId = ref<number | null>(props.agents[0]?.id ?? null)
const activeSessionId = ref<number | null>(null)
const messages = ref<Message[]>([])
const streaming = ref(false)
const streamingContent = ref('')
const rightTab = ref<'sessions' | 'agents' | 'info'>('sessions')

const selectedAgent = computed(() =>
    props.agents.find(a => a.id === selectedAgentId.value),
)

const statusConfig: Record<string, { label: string; class: string }> = {
    active: { label: 'Active', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' },
    completed: { label: 'Completed', class: 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400' },
    failed: { label: 'Failed', class: 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400' },
    pending: { label: 'Pending', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' },
}

function startNewSession() {
    activeSessionId.value = null
    messages.value = []
}

function openSession(session: Session) {
    activeSessionId.value = session.id
    router.get(agentsRoutes.sessions.show(session.id).url, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            const data = page.props as Record<string, unknown>
            const msgs = data.messages as Message[] | undefined
            if (msgs) messages.value = msgs
        },
    })
}

async function sendMessage(prompt: string) {
    if (!selectedAgentId.value) return

    if (!activeSessionId.value) {
        messages.value.push({
            id: `temp-${Date.now()}`,
            role: 'user',
            content: prompt,
        })
        messages.value.push({
            id: `temp-stream-${Date.now()}`,
            role: 'assistant',
            content: null,
        })
        streaming.value = true

        try {
            const res = await fetch('/ai/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? '' },
                body: JSON.stringify({ prompt, agent_id: selectedAgentId.value }),
            })

            if (!res.ok) throw new Error('Chat request failed')
            if (!res.body) throw new Error('No response body')

            const reader = res.body.getReader()
            const decoder = new TextDecoder()
            let buffer = ''

            while (true) {
                const { done, value } = await reader.read()
                if (done) break

                buffer += decoder.decode(value, { stream: true })
                const lines = buffer.split('\n')
                buffer = lines.pop() ?? ''

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const data = line.slice(6)
                        if (data === '[DONE]') continue
                        try {
                            const parsed = JSON.parse(data)
                            if (parsed.type === 'text-delta' && parsed.delta) {
                                streamingContent.value += parsed.delta
                            }
                        } catch {
                            streamingContent.value += data
                        }
                    }
                }
            }

            messages.value = messages.value.filter(m => !m.id.toString().startsWith('temp-'))
        } catch (err) {
            console.error('Stream error:', err)
        } finally {
            streaming.value = false
            streamingContent.value = ''
        }
        return
    }

    messages.value.push({
        id: `msg-${Date.now()}`,
        role: 'user',
        content: prompt,
    })

    streaming.value = true
    streamingContent.value = ''

    try {
        const res = await fetch(agentsRoutes.sessions.run(activeSessionId.value).url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? '' },
            body: JSON.stringify({ prompt }),
        })

        if (!res.ok) throw new Error('Session run failed')
        if (!res.body) throw new Error('No response body')

        const reader = res.body.getReader()
        const decoder = new TextDecoder()
        let buffer = ''

        while (true) {
            const { done, value } = await reader.read()
            if (done) break

            buffer += decoder.decode(value, { stream: true })
            const lines = buffer.split('\n')
            buffer = lines.pop() ?? ''

            for (const line of lines) {
                if (line.startsWith('data: ')) {
                    const data = line.slice(6)
                    if (data === '[DONE]') continue
                    try {
                        const parsed = JSON.parse(data)
                        if (parsed.type === 'text-delta' && parsed.delta) {
                            streamingContent.value += parsed.delta
                        }
                    } catch {
                        streamingContent.value += data
                    }
                }
            }
        }
    } catch (err) {
        console.error('Stream error:', err)
    } finally {
        streaming.value = false
        streamingContent.value = ''
    }
}

function stopStreaming() {
    streaming.value = false
}
</script>

<template>
    <Head :title="workspace.name" />

    <div class="flex h-full flex-1">
        <div class="flex flex-1 flex-col border-r">
            <div class="flex items-center justify-between border-b px-4 py-2.5">
                <div class="flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <Layers class="h-4 w-4" />
                    </div>
                    <div>
                        <h1 class="text-sm font-semibold">{{ workspace.name }}</h1>
                        <p class="text-xs text-muted-foreground">{{ workspace.description ?? 'Agent workspace' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Select v-model="selectedAgentId">
                        <SelectTrigger class="w-44 h-8 text-xs">
                            <SelectValue placeholder="Select agent..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="agent in agents" :key="agent.id" :value="agent.id">
                                <div class="flex items-center gap-2">
                                    <Bot class="h-3.5 w-3.5" />
                                    {{ agent.name }}
                                </div>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Button size="sm" variant="outline" @click="startNewSession" :disabled="!selectedAgentId">
                        <Plus class="h-3.5 w-3.5 mr-1" />
                        New Session
                    </Button>
                </div>
            </div>

            <ChatWindow
                :messages="messages"
                :streaming="streaming"
                :streaming-content="streamingContent"
                :disabled="!selectedAgentId"
                placeholder="Ask your agent..."
                @send="sendMessage"
                @stop="stopStreaming"
            />
        </div>

        <div class="w-72 flex-shrink-0 bg-muted/30">
            <div class="flex border-b">
                <button
                    v-for="tab in [{ id: 'sessions', icon: History, label: 'Sessions' }, { id: 'agents', icon: BotMessageSquare, label: 'Agents' }, { id: 'info', icon: Info, label: 'Info' }]"
                    :key="tab.id"
                    class="flex flex-1 items-center justify-center gap-1.5 border-b-2 px-3 py-2 text-xs font-medium transition-colors"
                    :class="rightTab === tab.id
                        ? 'border-primary text-foreground'
                        : 'border-transparent text-muted-foreground hover:text-foreground'"
                    @click="rightTab = tab.id as typeof rightTab"
                >
                    <component :is="tab.icon" class="h-3.5 w-3.5" />
                    {{ tab.label }}
                </button>
            </div>

            <div class="h-[calc(100vh-10rem)] overflow-y-auto">
                <div v-if="rightTab === 'sessions'" class="p-3 space-y-2">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-medium text-muted-foreground">Recent Sessions</h3>
                        <Button variant="ghost" size="icon-sm" @click="startNewSession" :disabled="!selectedAgentId">
                            <Plus class="h-3.5 w-3.5" />
                        </Button>
                    </div>

                    <div v-if="sessions.length === 0" class="flex flex-col items-center justify-center py-8 text-muted-foreground">
                        <MessagesSquare class="mb-2 h-6 w-6" />
                        <p class="text-xs">No sessions yet</p>
                    </div>

                    <button
                        v-for="session in sessions"
                        :key="session.id"
                        class="w-full rounded-lg border bg-card p-2.5 text-left text-xs transition-colors hover:bg-accent"
                        :class="activeSessionId === session.id ? 'border-primary' : ''"
                        @click="openSession(session)"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-medium">{{ session.agent_name }}</span>
                            <Badge variant="outline" :class="statusConfig[session.status]?.class ?? ''">
                                {{ statusConfig[session.status]?.label ?? session.status }}
                            </Badge>
                        </div>
                        <div class="mt-1 flex items-center gap-3 text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <MessagesSquare class="h-3 w-3" />
                                {{ session.message_count }}
                            </span>
                            <span class="flex items-center gap-1">
                                <Clock class="h-3 w-3" />
                                {{ new Date(session.created_at).toLocaleDateString() }}
                            </span>
                        </div>
                    </button>
                </div>

                <div v-if="rightTab === 'agents'" class="p-3 space-y-2">
                    <h3 class="text-xs font-medium text-muted-foreground mb-2">Available Agents</h3>
                    <div
                        v-for="agent in agents"
                        :key="agent.id"
                        class="rounded-lg border bg-card p-2.5 text-xs cursor-pointer transition-colors hover:bg-accent"
                        :class="selectedAgentId === agent.id ? 'border-primary' : ''"
                        @click="selectedAgentId = agent.id"
                    >
                        <div class="flex items-center gap-2 font-medium">
                            <Bot class="h-3.5 w-3.5" />
                            {{ agent.name }}
                        </div>
                        <p class="mt-1 text-muted-foreground truncate">{{ agent.primary_model }}</p>
                    </div>
                </div>

                <div v-if="rightTab === 'info'" class="p-3 space-y-3">
                    <h3 class="text-xs font-medium text-muted-foreground">Workspace Info</h3>
                    <div class="rounded-lg border bg-card p-3 text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Status</span>
                            <Badge variant="outline" :class="statusConfig[workspace.status]?.class ?? ''">
                                {{ workspace.status }}
                            </Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Slug</span>
                            <span class="font-mono">{{ workspace.slug }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center text-muted-foreground">
                        <Network class="mb-2 h-8 w-8" />
                        <p class="text-xs font-medium">Graph View</p>
                        <p class="text-xs mt-1">Visualize entity relationships</p>
                        <Button variant="secondary" size="sm" class="mt-3">
                            <Sparkles class="h-3.5 w-3.5 mr-1" />
                            Explore
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

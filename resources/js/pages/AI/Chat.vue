<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import {
    Bot,
    Brain,
    ChevronDown,
    Clock,
    Cpu,
    History,
    Lightbulb,
    MessagesSquare,
    Plus,
    Search,
    Sparkles,
    Trash2,
} from '@lucide/vue'
import { computed, ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
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
    fallback_model: string | null
    system_instruction: string | null
    temperature: number | null
}

type Session = {
    id: number
    agent_name: string
    status: string
    message_count: number
    context_tokens: number
    created_at: string
}

const props = defineProps<{
    agents: Agent[]
    sessions: Session[]
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'AI Chat', href: '/ai/chat' },
        ],
    },
})

const selectedAgentId = ref<number | null>(props.agents[0]?.id ?? null)
const activeSessionId = ref<number | null>(null)
const messages = ref<Message[]>([])
const streaming = ref(false)
const streamingContent = ref('')
const sessionSearch = ref('')

const selectedAgent = computed(() =>
    props.agents.find(a => a.id === selectedAgentId.value),
)

const filteredSessions = computed(() => {
    if (!sessionSearch.value.trim()) return props.sessions
    const q = sessionSearch.value.toLowerCase()
    return props.sessions.filter(s =>
        s.agent_name.toLowerCase().includes(q) ||
        s.status.toLowerCase().includes(q),
    )
})

function modelLabel(model: string): string {
    const parts = model.split(':')
    return parts.length > 1 ? parts[1] : model
}

function providerLabel(model: string): string {
    const parts = model.split(':')
    return parts.length > 1 ? parts[0] : 'unknown'
}

const statusConfig: Record<string, { label: string; class: string }> = {
    active: { label: 'Active', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' },
    completed: { label: 'Completed', class: 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400' },
    failed: { label: 'Failed', class: 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400' },
    pending: { label: 'Pending', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' },
}

const abortController = ref<AbortController | null>(null)

const suggestions = [
    'Explain the architecture of this project',
    'Write a Pest test for a controller',
    'Help me design a new module',
    'Review my code for security issues',
]

function startNewChat() {
    activeSessionId.value = null
    messages.value = []
    streamingContent.value = ''
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

function deleteSession(session: Session, e: MouseEvent) {
    e.stopPropagation()
    router.delete(`/agents/sessions/${session.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            if (activeSessionId.value === session.id) {
                startNewChat()
            }
        },
    })
}

function stopGeneration() {
    abortController.value?.abort()
    streaming.value = false
    streamingContent.value = ''
    messages.value = messages.value.filter(m => !m.id.toString().startsWith('temp-'))
}

async function sendMessage(prompt: string, files?: File[]) {
    if (!selectedAgentId.value) return

    if (!activeSessionId.value) {
        messages.value = []
        messages.value.push({
            id: `temp-${Date.now()}`,
            role: 'user',
            content: prompt,
        })
        streaming.value = true
        streamingContent.value = ''

        abortController.value = new AbortController()

        try {
            const body = new FormData()
            body.append('prompt', prompt)
            body.append('agent_id', String(selectedAgentId.value))
            if (files) {
                for (const file of files) {
                    body.append('files[]', file)
                }
            }

            const res = await fetch('/ai/chat', {
                method: 'POST',
                signal: abortController.value.signal,
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? '',
                },
                body,
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

            const finalContent = streamingContent.value
            messages.value = messages.value.filter(m => !m.id.toString().startsWith('temp-'))
            if (finalContent) {
                messages.value.push({
                    id: `assistant-${Date.now()}`,
                    role: 'assistant',
                    content: finalContent,
                })
            }
        } catch (err) {
            if ((err as Error).name === 'AbortError') return
            console.error('Stream error:', err)
        } finally {
            streaming.value = false
            streamingContent.value = ''
            abortController.value = null
        }
        return
    }

    abortController.value = new AbortController()

    messages.value.push({
        id: `msg-${Date.now()}`,
        role: 'user',
        content: prompt,
    })

    streaming.value = true
    streamingContent.value = ''

    try {
        const body = new FormData()
        body.append('prompt', prompt)
        if (files) {
            for (const file of files) {
                body.append('files[]', file)
            }
        }

        const res = await fetch(agentsRoutes.sessions.run(activeSessionId.value).url, {
            method: 'POST',
            signal: abortController.value?.signal,
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') as HTMLMetaElement)?.content ?? '',
            },
            body,
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

        const finalContent = streamingContent.value
        messages.value.push({
            id: `assistant-${Date.now()}`,
            role: 'assistant',
            content: finalContent,
        })
    } catch (err) {
        if ((err as Error).name === 'AbortError') return
        console.error('Stream error:', err)
    } finally {
        streaming.value = false
        streamingContent.value = ''
        abortController.value = null
    }
}

function selectSuggestion(suggestion: string) {
    sendMessage(suggestion)
}
</script>

<template>
    <Head title="AI Chat" />

    <div class="flex h-full flex-1">
        <div class="flex w-64 flex-shrink-0 flex-col border-r bg-muted/20">
            <div class="flex items-center justify-between border-b px-3 py-2.5">
                <h2 class="text-xs font-semibold text-muted-foreground flex items-center gap-1.5">
                    <History class="h-3.5 w-3.5" />
                    Sessions
                </h2>
                <Button variant="ghost" size="icon-sm" @click="startNewChat" title="New chat">
                    <Plus class="h-4 w-4" />
                </Button>
            </div>

            <div class="p-2">
                <div class="relative">
                    <Search class="absolute left-2.5 top-2 h-3.5 w-3.5 text-muted-foreground" />
                    <Input
                        v-model="sessionSearch"
                        placeholder="Search sessions..."
                        class="h-8 pl-7 text-xs"
                    />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                <div v-if="filteredSessions.length === 0" class="flex flex-col items-center justify-center py-8 text-muted-foreground">
                    <MessagesSquare class="mb-2 h-5 w-5" />
                    <p class="text-xs">{{ sessionSearch ? 'No matches' : 'No sessions yet' }}</p>
                </div>

                <button
                    v-for="session in filteredSessions"
                    :key="session.id"
                    class="group relative w-full rounded-lg border bg-card p-2.5 text-left text-xs transition-all hover:bg-accent"
                    :class="activeSessionId === session.id ? 'border-primary ring-1 ring-primary/20' : 'border-transparent'"
                    @click="openSession(session)"
                >
                    <div class="flex items-center justify-between">
                        <span class="font-medium truncate flex items-center gap-1">
                            <Bot class="h-3 w-3 shrink-0 text-muted-foreground" />
                            {{ session.agent_name }}
                        </span>
                        <button
                            class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity p-0.5 rounded hover:bg-destructive/10 text-muted-foreground hover:text-destructive"
                            @click="deleteSession(session, $event)"
                        >
                            <Trash2 class="h-3 w-3" />
                        </button>
                    </div>
                    <div class="mt-1 flex items-center gap-2 text-muted-foreground">
                        <Badge variant="outline" class="px-1 py-0 text-[10px] leading-none" :class="statusConfig[session.status]?.class ?? ''">
                            {{ statusConfig[session.status]?.label ?? session.status }}
                        </Badge>
                        <span class="flex items-center gap-0.5">
                            <MessagesSquare class="h-2.5 w-2.5" />
                            {{ session.message_count }}
                        </span>
                        <span class="flex items-center gap-0.5 ml-auto">
                            <Clock class="h-2.5 w-2.5" />
                            {{ new Date(session.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}
                        </span>
                    </div>
                </button>
            </div>
        </div>

        <div class="flex flex-1 flex-col">
            <div class="flex items-center justify-between border-b px-4 py-2.5">
                <div class="flex items-center gap-3">
                    <Select v-model="selectedAgentId">
                        <SelectTrigger class="w-52 h-8 text-xs">
                            <SelectValue placeholder="Select an agent...">
                                <template #default>
                                    <div v-if="selectedAgent" class="flex items-center gap-2">
                                        <Bot class="h-3.5 w-3.5" />
                                        <span>{{ selectedAgent.name }}</span>
                                    </div>
                                    <span v-else>Select an agent...</span>
                                </template>
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="agent in agents" :key="agent.id" :value="agent.id">
                                <div class="flex items-center gap-2">
                                    <Bot class="h-4 w-4" />
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium">{{ agent.name }}</span>
                                        <span class="text-xs text-muted-foreground">{{ modelLabel(agent.primary_model) }}</span>
                                    </div>
                                </div>
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <template v-if="selectedAgent">
                        <Badge variant="secondary" class="gap-1 text-xs">
                            <Cpu class="h-3 w-3" />
                            {{ modelLabel(selectedAgent.primary_model) }}
                        </Badge>
                        <Badge variant="outline" class="text-xs capitalize">
                            {{ providerLabel(selectedAgent.primary_model) }}
                        </Badge>
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <template v-if="activeSessionId">
                        <Badge variant="outline" class="text-xs gap-1">
                            <MessagesSquare class="h-3 w-3" />
                            {{ messages.length }} messages
                        </Badge>
                    </template>
                    <Button variant="ghost" size="icon-sm" @click="startNewChat" title="New chat">
                        <Plus class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <ChatWindow
                :messages="messages"
                :streaming="streaming"
                :streaming-content="streamingContent"
                :disabled="!selectedAgentId || !selectedAgent"
                placeholder="Ask about your project, write code, review architecture..."
                @send="sendMessage"
                @stop="stopGeneration"
            />

            <div
                v-if="messages.length === 0 && !streaming"
                class="absolute bottom-20 left-1/2 -translate-x-1/2 w-full max-w-lg px-4"
            >
                <div class="rounded-xl border bg-card p-4 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <Sparkles class="h-4 w-4 text-amber-500" />
                        <span class="text-xs font-medium text-muted-foreground">Suggestions</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="suggestion in suggestions"
                            :key="suggestion"
                            class="rounded-lg border bg-muted/50 px-3 py-2.5 text-left text-xs transition-colors hover:bg-accent hover:border-primary/50"
                            @click="selectSuggestion(suggestion)"
                        >
                            <Lightbulb class="mb-1 h-3.5 w-3.5 text-muted-foreground" />
                            {{ suggestion }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

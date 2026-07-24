<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    ArrowRight,
    Bug,
    CheckCircle2,
    CircleDot,
    Clock,
    Code2,
    Columns3,
    GitBranch,
    ListTree,
    Network,
    Plus,
    Sparkles,
} from '@lucide/vue'
import { ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

type Workspace = {
    id: number
    name: string
    slug: string
    description: string | null
    status: string
}

defineProps<{
    workspace: Workspace
}>()

defineOptions({
    layout: (props: { workspace: Workspace }) => ({
        breadcrumbs: [
            {
                title: 'Workspaces',
                href: '/workspaces',
            },
            {
                title: props.workspace.name,
                href: `/workspaces/${props.workspace.id}`,
            },
        ],
    }),
})

type Tab = 'board' | 'backlog' | 'graph'
const activeTab = ref<Tab>('board')

const tabs: { id: Tab; label: string; icon: typeof Columns3 }[] = [
    { id: 'board', label: 'Kanban Board', icon: Columns3 },
    { id: 'backlog', label: 'Backlog Tree', icon: ListTree },
    { id: 'graph', label: 'Graph View', icon: Network },
]

type Column = {
    id: string
    title: string
    items: { id: number; title: string; description: string; priority: string; assignee: string; tags: string[] }[]
}

const columns = ref<Column[]>([
    {
        id: 'backlog',
        title: 'Backlog',
        items: [
            { id: 1, title: 'Design API rate limiting', description: 'Implement rate limiting per API key', priority: 'medium', assignee: 'Alex', tags: ['backend'] },
            { id: 2, title: 'Research vector DB options', description: 'Compare pgvector, Qdrant, Pinecone', priority: 'low', assignee: 'Sam', tags: ['research'] },
            { id: 5, title: 'Write E2E test suite', description: 'Cover core agent execution flows', priority: 'medium', assignee: 'Jordan', tags: ['testing'] },
        ],
    },
    {
        id: 'todo',
        title: 'To Do',
        items: [
            { id: 3, title: 'Agent session persistence', description: 'Save agent state to database on interrupt', priority: 'high', assignee: 'Taylor', tags: ['backend', 'core'] },
            { id: 6, title: 'SSE streaming for chat UI', description: 'Real-time token streaming via Server-Sent Events', priority: 'high', assignee: 'Morgan', tags: ['frontend'] },
        ],
    },
    {
        id: 'in-progress',
        title: 'In Progress',
        items: [
            { id: 4, title: 'Multi-provider failover', description: 'Automatic failover between OpenAI, Anthropic, Gemini', priority: 'high', assignee: 'Taylor', tags: ['backend', 'core', 'ai'] },
        ],
    },
    {
        id: 'done',
        title: 'Done',
        items: [
            { id: 7, title: 'Project scaffolding', description: 'Laravel + Inertia + Vue project setup', priority: 'high', assignee: 'Tristan', tags: ['infra'] },
            { id: 8, title: 'Team invitation flow', description: 'Invite members via email with acceptance', priority: 'high', assignee: 'Tristan', tags: ['auth'] },
            { id: 9, title: 'Dark mode support', description: 'Tailwind dark mode + theme toggle', priority: 'medium', assignee: 'Jordan', tags: ['frontend', 'ui'] },
        ],
    },
])

const priorityStyles: Record<string, string> = {
    high: 'border-red-400 bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 dark:border-red-800',
    medium: 'border-amber-400 bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800',
    low: 'border-sky-400 bg-sky-50 text-sky-700 dark:bg-sky-950/30 dark:text-sky-400 dark:border-sky-800',
}

const tagStyles: Record<string, string> = {
    backend: 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
    frontend: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300',
    testing: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
    research: 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300',
    core: 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
    ai: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-300',
    auth: 'bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300',
    infra: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    ui: 'bg-pink-100 text-pink-700 dark:bg-pink-950/50 dark:text-pink-300',
}
</script>

<template>
    <Head :title="workspace.name" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ workspace.name }}</h1>
                    <Badge
                        variant="outline"
                        :class="workspace.status === 'active' ? 'border-emerald-400 text-emerald-600 dark:text-emerald-400' : ''"
                    >
                        {{ workspace.status }}
                    </Badge>
                </div>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ workspace.description ?? 'No description' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm">
                    <GitBranch class="mr-1.5 h-4 w-4" />
                    main
                </Button>
                <Button size="sm">
                    <Plus class="mr-1.5 h-4 w-4" />
                    Add Task
                </Button>
            </div>
        </div>

        <div class="flex gap-1 rounded-lg bg-muted p-1 w-fit">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                :class="activeTab === tab.id
                    ? 'bg-background text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'"
            >
                <component :is="tab.icon" class="h-4 w-4" />
                {{ tab.label }}
            </button>
        </div>

        <div v-if="activeTab === 'board'" class="overflow-x-auto pb-4">
            <div class="flex gap-4 min-w-max">
                <div
                    v-for="column in columns"
                    :key="column.id"
                    class="flex w-72 flex-shrink-0 flex-col rounded-xl border bg-card"
                >
                    <div class="flex items-center justify-between border-b px-4 py-3">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-medium">{{ column.title }}</h3>
                            <span class="inline-flex size-5 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground">
                                {{ column.items.length }}
                            </span>
                        </div>
                        <Button variant="ghost" size="icon" class="size-6">
                            <Plus class="h-3.5 w-3.5" />
                        </Button>
                    </div>

                    <div class="flex flex-col gap-3 p-3">
                        <div
                            v-for="item in column.items"
                            :key="item.id"
                            class="group rounded-lg border bg-card p-3 shadow-sm transition-all hover:shadow-md cursor-pointer"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="text-sm font-medium leading-snug">{{ item.title }}</h4>
                                <span
                                    class="inline-flex shrink-0 items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium"
                                    :class="priorityStyles[item.priority]"
                                >
                                    <CircleDot class="h-2.5 w-2.5" />
                                    {{ item.priority }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground line-clamp-2">
                                {{ item.description }}
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="tag in item.tags"
                                        :key="tag"
                                        class="inline-flex rounded px-1.5 py-0.5 text-[10px] font-medium"
                                        :class="tagStyles[tag] ?? 'bg-muted text-muted-foreground'"
                                    >
                                        {{ tag }}
                                    </span>
                                </div>
                                <span class="inline-flex size-6 items-center justify-center rounded-full bg-primary/10 text-xs font-medium text-primary">
                                    {{ item.assignee.charAt(0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'backlog'" class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium">Backlog Tree</h3>
                <Button variant="outline" size="sm">
                    <ListTree class="mr-1.5 h-4 w-4" />
                    Expand All
                </Button>
            </div>

            <div class="space-y-1">
                <div
                    v-for="(column) in columns"
                    :key="column.id"
                >
                    <div class="flex items-center gap-2 py-2 text-sm font-medium text-muted-foreground">
                        <ArrowRight class="h-3.5 w-3.5" />
                        {{ column.title }}
                        <span class="text-xs">({{ column.items.length }})</span>
                    </div>
                    <div class="ml-6 space-y-1 border-l pl-4">
                        <div
                            v-for="item in column.items"
                            :key="item.id"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm hover:bg-muted/50 transition-colors cursor-pointer"
                        >
                            <CheckCircle2
                                v-if="column.id === 'done'"
                                class="h-4 w-4 text-emerald-500"
                            />
                            <Clock
                                v-else-if="column.id === 'in-progress'"
                                class="h-4 w-4 text-amber-500"
                            />
                            <CircleDot
                                v-else
                                class="h-4 w-4 text-muted-foreground"
                            />
                            <span :class="column.id === 'done' ? 'line-through text-muted-foreground' : ''">
                                {{ item.title }}
                            </span>
                            <div class="flex gap-1 ml-auto">
                                <span
                                    v-for="tag in item.tags"
                                    :key="tag"
                                    class="inline-flex rounded px-1.5 py-0.5 text-[10px] font-medium"
                                    :class="tagStyles[tag] ?? 'bg-muted text-muted-foreground'"
                                >
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'graph'" class="rounded-xl border bg-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium">Engineering Graph</h3>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm">
                        <Bug class="mr-1.5 h-4 w-4" />
                        Dependencies
                    </Button>
                    <Button variant="outline" size="sm">
                        <Sparkles class="mr-1.5 h-4 w-4" />
                        Auto-layout
                    </Button>
                </div>
            </div>

            <div class="relative flex h-96 items-center justify-center rounded-lg border-2 border-dashed bg-muted/30">
                <div class="flex flex-col items-center gap-3 text-center">
                    <Network class="h-12 w-12 text-muted-foreground/50" />
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Graph Topology Viewer</p>
                        <p class="text-xs text-muted-foreground/60 mt-1">
                            Visualize entity relationships and dependency graphs
                        </p>
                    </div>
                    <Button variant="secondary" size="sm">
                        <Code2 class="mr-1.5 h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

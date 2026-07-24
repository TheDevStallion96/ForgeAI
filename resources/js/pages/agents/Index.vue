<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
    Bot,
    CircleCheck,
    CircleX,
    Cpu,
    MessagesSquare,
    Plus,
} from '@lucide/vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import routes from '@/routes/agents'

defineProps<{
    agents: Array<{
        id: number
        name: string
        primary_model: string
        is_active: boolean
        session_count: number
        created_at: string
    }>
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'AI Agent Studio', href: routes.index().url },
        ],
    },
})
</script>

<template>
    <Head title="AI Agent Studio" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">AI Agent Studio</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Create and manage AI agent personas, models, and system instructions.
                </p>
            </div>
            <Link :href="routes.create().url">
                <Button size="sm">
                    <Plus class="mr-1.5 h-4 w-4" />
                    New Agent
                </Button>
            </Link>
        </div>

        <div v-if="agents.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <Bot class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No agents yet</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Create your first AI agent to get started.
            </p>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="agent in agents"
                :key="agent.id"
                :href="`/agents/${agent.id}/edit`"
                class="group block"
            >
                <Card class="transition-all duration-200 hover:shadow-md hover:border-primary/50 cursor-pointer h-full">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Bot class="h-5 w-5" />
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="agent.is_active
                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400'
                                        : 'bg-gray-50 text-gray-600 dark:bg-gray-900 dark:text-gray-400'"
                                >
                                    <component :is="agent.is_active ? CircleCheck : CircleX" class="h-3 w-3" />
                                    {{ agent.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <CardTitle class="mt-3 text-base">
                            {{ agent.name }}
                        </CardTitle>
                        <CardDescription class="flex items-center gap-1">
                            <Cpu class="h-3.5 w-3.5" />
                            {{ agent.primary_model }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <MessagesSquare class="h-3.5 w-3.5" />
                                {{ agent.session_count }} sessions
                            </span>
                            <span>Created {{ new Date(agent.created_at).toLocaleDateString() }}</span>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>

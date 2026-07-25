<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ArrowLeft, GitPullRequest, GitMerge, CircleX, Plus, MessageSquare } from '@lucide/vue'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import sourceControl from '@/routes/source-control'

type PullRequest = {
    id: number
    number: number
    title: string
    state: string
    body: string | null
    author: string | null
    author_avatar: string | null
    created_at: string
    updated_at: string
    head_branch: string
    base_branch: string
    url: string
    draft: boolean
    merged: boolean
}

type Repo = {
    id: number
    name: string
    default_branch: string
}

const props = defineProps<{
    repo: Repo
    pullRequests: PullRequest[]
    state: string
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Source Control', href: sourceControl.index().url },
            { title: props.repo.name, href: sourceControl.show({ repo: props.repo.id }).url },
            { title: 'Pull Requests', href: '' },
        ],
    },
})

function switchState(newState: string) {
    router.get(sourceControl.pullRequests({ repo: props.repo.id }).url, { state: newState }, {
        preserveScroll: true,
        preserveState: true,
    })
}

function stateIcon(pr: PullRequest) {
    if (pr.merged) return GitMerge
    if (pr.state === 'closed') return CircleX
    return GitPullRequest
}

function stateClass(pr: PullRequest) {
    if (pr.merged) return 'text-purple-500'
    if (pr.state === 'closed') return 'text-muted-foreground'
    return 'text-green-500'
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
    })
}
</script>

<template>
    <Head :title="`${repo.name} - Pull Requests`" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" as-child>
                <Link :href="sourceControl.show({ repo: repo.id }).url">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">{{ repo.name }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">Pull requests</p>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex gap-1 rounded-lg bg-muted p-1">
                <Button
                    variant="ghost"
                    size="sm"
                    :class="state === 'open' ? 'bg-background shadow-sm' : ''"
                    @click="switchState('open')"
                >
                    Open
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    :class="state === 'closed' ? 'bg-background shadow-sm' : ''"
                    @click="switchState('closed')"
                >
                    Closed
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    :class="state === 'all' ? 'bg-background shadow-sm' : ''"
                    @click="switchState('all')"
                >
                    All
                </Button>
            </div>
            <Button variant="outline" size="sm" class="gap-1" as-child>
                <a :href="`https://github.com/${repo.name}/compare`" target="_blank" rel="noopener">
                    <Plus class="h-4 w-4" />
                    New PR
                </a>
            </Button>
        </div>

        <div v-if="pullRequests.length === 0" class="flex flex-col items-center justify-center py-20">
            <GitPullRequest class="mb-4 h-12 w-12 text-muted-foreground" />
            <h3 class="text-lg font-medium">No pull requests found</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                There are no {{ state }} pull requests for this repository.
            </p>
        </div>

        <div v-else class="space-y-2">
            <div
                v-for="pr in pullRequests"
                :key="pr.id"
                class="flex items-start gap-3 rounded-lg border p-3 transition-colors hover:bg-muted/50"
            >
                <component :is="stateIcon(pr)" :class="stateClass(pr)" class="mt-1 h-5 w-5 shrink-0" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium">{{ pr.title }}</p>
                            <p class="text-xs text-muted-foreground">
                                #{{ pr.number }}
                                <span v-if="pr.draft" class="ml-1 text-muted-foreground">(Draft)</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <Badge v-if="pr.merged" variant="secondary">Merged</Badge>
                            <Badge v-else-if="pr.state === 'closed'" variant="outline">Closed</Badge>
                            <Badge v-else variant="default">Open</Badge>
                        </div>
                    </div>
                    <div class="mt-1 flex items-center gap-3 text-xs text-muted-foreground">
                        <span class="flex items-center gap-1">
                            <Avatar class="size-4">
                                <AvatarImage :src="pr.author_avatar ?? undefined" />
                                <AvatarFallback class="text-[8px]">{{ pr.author?.charAt(0)?.toUpperCase() }}</AvatarFallback>
                            </Avatar>
                            {{ pr.author }}
                        </span>
                        <span>{{ formatDate(pr.created_at) }}</span>
                        <span class="font-mono text-xs">{{ pr.head_branch }} &rarr; {{ pr.base_branch }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

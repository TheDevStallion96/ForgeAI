<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    GitBranch,
    GitCommit,
    GitMerge,
    GitPullRequest,
    Star,
} from '@lucide/vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import sourceControl from '@/routes/source-control'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Source Control', href: sourceControl.index().url },
        ],
    },
})

const repos = [
    { name: 'forge-ai/core', branch: 'main', commits: 342, prs: 3, language: 'PHP', stars: 12 },
    { name: 'forge-ai/frontend', branch: 'develop', commits: 187, prs: 5, language: 'TypeScript', stars: 8 },
    { name: 'forge-ai/agents', branch: 'main', commits: 94, prs: 1, language: 'PHP', stars: 4 },
]

const recentCommits = [
    { repo: 'forge-ai/core', message: 'Add workspace module with Kanban views', author: 'Tristan', hash: 'a1b2c3d', time: '2h ago' },
    { repo: 'forge-ai/frontend', message: 'Implement agent session chat UI', author: 'Morgan', hash: 'e4f5g6h', time: '4h ago' },
    { repo: 'forge-ai/core', message: 'Add API key secrets manager', author: 'Taylor', hash: 'i7j8k9l', time: '6h ago' },
    { repo: 'forge-ai/agents', message: 'Add session state machine', author: 'Alex', hash: 'm0n1o2p', time: '8h ago' },
]
</script>

<template>
    <Head title="Source Control" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Source Control</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Git repositories, pull requests, and code review.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <Card v-for="repo in repos" :key="repo.name" class="hover:shadow-md transition-shadow cursor-pointer">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <GitBranch class="h-5 w-5" />
                        </div>
                        <Button variant="ghost" size="icon" class="size-8">
                            <Star class="h-4 w-4" />
                        </Button>
                    </div>
                    <CardTitle class="mt-3 text-base font-mono text-sm">{{ repo.name }}</CardTitle>
                    <CardDescription class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-xs">
                            <GitBranch class="h-3 w-3" />
                            {{ repo.branch }}
                        </span>
                        <Badge variant="outline" class="text-xs">{{ repo.language }}</Badge>
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-4 text-xs text-muted-foreground">
                        <span class="flex items-center gap-1">
                            <GitCommit class="h-3.5 w-3.5" />
                            {{ repo.commits }} commits
                        </span>
                        <span class="flex items-center gap-1">
                            <GitPullRequest class="h-3.5 w-3.5" />
                            {{ repo.prs }} PRs
                        </span>
                        <span class="flex items-center gap-1">
                            <Star class="h-3.5 w-3.5" />
                            {{ repo.stars }}
                        </span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-base flex items-center gap-2">
                    <GitCommit class="h-4 w-4" />
                    Recent Commits
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div v-for="commit in recentCommits" :key="commit.hash" class="flex items-center gap-3 rounded-lg border p-3">
                        <div class="flex size-8 items-center justify-center rounded-full bg-muted font-mono text-xs text-muted-foreground">
                            {{ commit.hash.slice(0, 4) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm truncate">{{ commit.message }}</p>
                            <p class="text-xs text-muted-foreground">{{ commit.repo }} · {{ commit.author }} · {{ commit.time }}</p>
                        </div>
                        <GitMerge class="h-4 w-4 text-muted-foreground" />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

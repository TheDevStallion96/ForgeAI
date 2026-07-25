<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeft, GitCommit, GitBranch, User, Calendar } from '@lucide/vue'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import sourceControl from '@/routes/source-control'

type Commit = {
    sha: string
    short_sha: string
    message: string
    author_name: string
    author_email: string
    author_username: string | null
    author_avatar: string | null
    date: string
    url: string
}

type Repo = {
    id: number
    name: string
    default_branch: string
}

const props = defineProps<{
    repo: Repo
    commits: Commit[]
    branch: string
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Source Control', href: sourceControl.index().url },
            { title: props.repo.name, href: sourceControl.show({ repo: props.repo.id }).url },
            { title: 'Commits', href: '' },
        ],
    },
})

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    })
}
</script>

<template>
    <Head :title="`${repo.name} - Commits`" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" as-child>
                <Link :href="sourceControl.show({ repo: repo.id }).url">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">{{ repo.name }}</h1>
                <p class="mt-1 text-sm text-muted-foreground">Commit history</p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <GitBranch class="h-4 w-4" />
            <span class="font-mono">{{ branch }}</span>
        </div>

        <div v-if="commits.length === 0" class="flex flex-col items-center justify-center py-20">
            <GitCommit class="mb-4 h-12 w-12 text-muted-foreground" />
            <h3 class="text-lg font-medium">No commits found</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Connect your GitHub account and sync repositories to view commits.
            </p>
        </div>

        <div v-else class="space-y-1">
            <div
                v-for="commit in commits"
                :key="commit.sha"
                class="flex items-start gap-3 rounded-lg border p-3 transition-colors hover:bg-muted/50"
            >
                <Avatar class="size-8">
                    <AvatarImage :src="commit.author_avatar ?? undefined" :alt="commit.author_name" />
                    <AvatarFallback>{{ commit.author_name.charAt(0).toUpperCase() }}</AvatarFallback>
                </Avatar>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-medium truncate">{{ commit.message.split('\n')[0] }}</p>
                        <Badge variant="secondary" class="shrink-0 font-mono text-xs">
                            {{ commit.short_sha }}
                        </Badge>
                    </div>
                    <div class="mt-1 flex items-center gap-3 text-xs text-muted-foreground">
                        <span class="flex items-center gap-1">
                            <User class="h-3 w-3" />
                            {{ commit.author_username ?? commit.author_name }}
                        </span>
                        <span class="flex items-center gap-1">
                            <Calendar class="h-3 w-3" />
                            {{ formatDate(commit.date) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

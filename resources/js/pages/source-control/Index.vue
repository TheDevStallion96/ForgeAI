<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import {
    GitBranch,
    GitCommit,
    GitPullRequest,
    RefreshCw,
    Settings2,
    Trash2,
    LogOut,
    Globe,
    Lock,
} from '@lucide/vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import sourceControl from '@/routes/source-control'

type Repo = {
    id: number
    name: string
    url: string
    description: string | null
    language: string | null
    is_private: boolean
    provider: string
    default_branch: string
    status: string
    created_at: string
}

type Installation = {
    id: number
    provider: string
    github_username: string
    created_at: string
}

const props = defineProps<{
    repos: Repo[]
    installation: Installation | null
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Source Control', href: sourceControl.index().url },
        ],
    },
})

function disconnect(installationId: number) {
    router.post(sourceControl.github.disconnect({ installation: installationId }).url, {}, {
        preserveScroll: true,
    })
}

function syncRepos(installationId: number) {
    router.post(sourceControl.github.sync({ installation: installationId }).url, {}, {
        preserveScroll: true,
    })
}

function removeRepo(repoId: number) {
    if (confirm('Remove this repository from the list?')) {
        router.delete(sourceControl.destroy({ repo: repoId }).url, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <Head title="Source Control" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Source Control</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Git repositories, pull requests, and code review.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" class="gap-1" as-child>
                    <Link :href="sourceControl.github.connect().url">
                        <Globe class="h-4 w-4" />
                        {{ installation ? 'Settings' : 'Connect GitHub' }}
                    </Link>
                </Button>
                <Button
                    v-if="installation"
                    variant="outline"
                    size="sm"
                    class="gap-1"
                    @click="syncRepos(installation.id)"
                >
                    <RefreshCw class="h-4 w-4" />
                    Sync
                </Button>
            </div>
        </div>

        <div v-if="installation" class="max-w-lg">
            <Alert>
                <Globe class="h-4 w-4" />
                <AlertTitle>Connected as {{ installation.github_username }}</AlertTitle>
                <AlertDescription>
                    <div class="mt-2 flex items-center gap-2">
                        <Badge variant="outline">{{ installation.provider.toUpperCase() }}</Badge>
                        <span class="text-xs text-muted-foreground">Connected {{ installation.created_at }}</span>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-6 gap-1 text-xs text-destructive hover:text-destructive"
                            @click="disconnect(installation.id)"
                        >
                            <LogOut class="h-3 w-3" />
                            Disconnect
                        </Button>
                    </div>
                </AlertDescription>
            </Alert>
        </div>

        <div v-if="repos.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <GitBranch class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No repositories connected</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Connect your GitHub account and sync repositories to get started.
            </p>
            <Button class="mt-4 gap-1" as-child>
                <Link :href="sourceControl.github.connect().url">
                    <Globe class="h-4 w-4" />
                    Connect GitHub
                </Link>
            </Button>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="repo in repos" :key="repo.id" class="group hover:shadow-md transition-shadow">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <GitBranch class="h-5 w-5" />
                        </div>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="size-8 opacity-0 group-hover:opacity-100 transition-opacity"
                            @click.stop="removeRepo(repo.id)"
                        >
                            <Trash2 class="h-4 w-4 text-muted-foreground hover:text-destructive" />
                        </Button>
                    </div>
                    <Link
                        :href="sourceControl.show({ repo: repo.id }).url"
                        class="mt-3 block"
                    >
                        <CardTitle class="font-mono text-sm hover:text-primary transition-colors">
                            {{ repo.name }}
                        </CardTitle>
                    </Link>
                    <CardDescription class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-xs">
                            <GitBranch class="h-3 w-3" />
                            {{ repo.default_branch }}
                        </span>
                        <Badge variant="outline" class="text-xs">{{ repo.provider }}</Badge>
                        <Badge v-if="repo.is_private" variant="ghost" class="text-xs gap-0.5 px-1">
                            <Lock class="h-3 w-3" />
                        </Badge>
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p v-if="repo.description" class="text-xs text-muted-foreground line-clamp-1">
                        {{ repo.description }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-xs text-muted-foreground">
                        <span v-if="repo.language" class="inline-flex items-center gap-1">
                            <span class="size-2 rounded-full bg-primary" />
                            {{ repo.language }}
                        </span>
                        <span>Synced {{ repo.created_at }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <Button variant="ghost" size="sm" class="h-7 gap-1 text-xs" as-child>
                            <Link :href="sourceControl.commits({ repo: repo.id }).url">
                                <GitCommit class="h-3 w-3" />
                                Commits
                            </Link>
                        </Button>
                        <Button variant="ghost" size="sm" class="h-7 gap-1 text-xs" as-child>
                            <Link :href="sourceControl.pullRequests({ repo: repo.id }).url">
                                <GitPullRequest class="h-3 w-3" />
                                PRs
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

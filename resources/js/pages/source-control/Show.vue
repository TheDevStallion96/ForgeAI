<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import {
    ArrowLeft,
    GitBranch,
    GitCommit,
    GitPullRequest,
    Globe,
    Lock,
    Code,
    ExternalLink,
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
import { Separator } from '@/components/ui/separator'
import sourceControl from '@/routes/source-control'

type Repo = {
    id: number
    name: string
    url: string
    description: string | null
    language: string | null
    is_private: boolean
    default_branch: string
    clone_url: string | null
    ssh_url: string | null
    provider: string
}

const props = defineProps<{
    repo: Repo
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Source Control', href: sourceControl.index().url },
            { title: props.repo.name, href: '' },
        ],
    },
})
</script>

<template>
    <Head :title="repo.name" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" as-child>
                <Link :href="sourceControl.index().url">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold tracking-tight font-mono text-sm">{{ repo.name }}</h1>
                    <Badge variant="outline">{{ repo.provider }}</Badge>
                    <Badge v-if="repo.is_private" variant="secondary" class="gap-1">
                        <Lock class="h-3 w-3" /> Private
                    </Badge>
                    <Badge v-else variant="secondary">Public</Badge>
                </div>
                <p v-if="repo.description" class="mt-1 text-sm text-muted-foreground">
                    {{ repo.description }}
                </p>
            </div>
            <Button variant="outline" size="sm" class="gap-1" as-child>
                <a :href="repo.url" target="_blank" rel="noopener">
                    <ExternalLink class="h-4 w-4" />
                    Open on GitHub
                </a>
            </Button>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card class="cursor-pointer hover:shadow-md transition-shadow" as-child>
                <Link :href="sourceControl.commits({ repo: repo.id }).url">
                    <CardHeader>
                        <GitCommit class="h-5 w-5 text-primary" />
                        <CardTitle class="text-base mt-2">Commits</CardTitle>
                        <CardDescription>View commit history and diffs</CardDescription>
                    </CardHeader>
                </Link>
            </Card>

            <Card class="cursor-pointer hover:shadow-md transition-shadow" as-child>
                <Link :href="sourceControl.pullRequests({ repo: repo.id }).url">
                    <CardHeader>
                        <GitPullRequest class="h-5 w-5 text-primary" />
                        <CardTitle class="text-base mt-2">Pull Requests</CardTitle>
                        <CardDescription>Browse open and closed PRs</CardDescription>
                    </CardHeader>
                </Link>
            </Card>

            <Card class="cursor-pointer hover:shadow-md transition-shadow">
                <CardHeader>
                    <GitBranch class="h-5 w-5 text-primary" />
                    <CardTitle class="text-base mt-2">Branches</CardTitle>
                    <CardDescription>
                        <span class="font-mono">{{ repo.default_branch }}</span> (default)
                    </CardDescription>
                </CardHeader>
            </Card>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="text-base">Clone</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div v-if="repo.clone_url">
                    <p class="text-xs text-muted-foreground mb-1">HTTPS</p>
                    <div class="flex items-center gap-2 rounded-lg bg-muted p-2">
                        <Globe class="h-4 w-4 text-muted-foreground shrink-0" />
                        <code class="text-xs font-mono truncate">{{ repo.clone_url }}</code>
                    </div>
                </div>
                <div v-if="repo.ssh_url">
                    <p class="text-xs text-muted-foreground mb-1">SSH</p>
                    <div class="flex items-center gap-2 rounded-lg bg-muted p-2">
                        <Code class="h-4 w-4 text-muted-foreground shrink-0" />
                        <code class="text-xs font-mono truncate">{{ repo.ssh_url }}</code>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

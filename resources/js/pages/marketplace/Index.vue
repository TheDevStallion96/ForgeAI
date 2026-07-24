<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
    Bot,
    Cpu,
    Download,
    Globe,
    Puzzle,
    Search,
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
import { Input } from '@/components/ui/input'
import marketplace from '@/routes/marketplace'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Marketplace', href: marketplace.index().url },
        ],
    },
})

const plugins = [
    { name: 'GitHub Integration', desc: 'Sync PRs, issues, and commits with your workspace', author: 'Forge AI', installs: 142, rating: 4.5, icon: Globe, category: 'Source Control' },
    { name: 'Slack Notifier', desc: 'Receive agent session updates and alerts in Slack', author: 'Community', installs: 89, rating: 4.2, icon: Bot, category: 'Notifications' },
    { name: 'Web Scraper Tool', desc: 'Extract and index web page content for knowledge base', author: 'Forge AI', installs: 67, rating: 4.0, icon: Globe, category: 'Tools' },
    { name: 'Code Analyzer', desc: 'Automated code review and static analysis for PRs', author: 'Community', installs: 53, rating: 4.8, icon: Cpu, category: 'Development' },
    { name: 'Custom MCP Server', desc: 'Extend agents with custom Model Context Protocol tools', author: 'Forge AI', installs: 41, rating: 4.6, icon: Puzzle, category: 'Extensions' },
    { name: 'Prompt Templates', desc: 'Community-curated prompt templates for common tasks', author: 'Community', installs: 38, rating: 3.9, icon: Bot, category: 'Templates' },
]
</script>

<template>
    <Head title="Marketplace" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Marketplace</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Discover plugins, tools, and templates for your agents.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Search marketplace..." class="pl-8" />
            </div>
            <div class="flex gap-1 rounded-lg bg-muted p-1">
                <button class="rounded-md px-3 py-1.5 text-sm font-medium bg-background shadow-sm">All</button>
                <button class="rounded-md px-3 py-1.5 text-sm font-medium text-muted-foreground hover:text-foreground">Tools</button>
                <button class="rounded-md px-3 py-1.5 text-sm font-medium text-muted-foreground hover:text-foreground">Templates</button>
                <button class="rounded-md px-3 py-1.5 text-sm font-medium text-muted-foreground hover:text-foreground">Extensions</button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="plugin in plugins" :key="plugin.name" class="group hover:shadow-md transition-shadow">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <component :is="plugin.icon" class="h-5 w-5" />
                        </div>
                        <Button variant="ghost" size="sm" class="gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <Download class="h-4 w-4" />
                            Install
                        </Button>
                    </div>
                    <CardTitle class="mt-3 text-base">{{ plugin.name }}</CardTitle>
                    <CardDescription class="line-clamp-2">{{ plugin.desc }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <Badge variant="outline" class="text-xs">{{ plugin.category }}</Badge>
                            <span>by {{ plugin.author }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <Star class="h-3 w-3 fill-amber-400 text-amber-400" />
                            <span>{{ plugin.rating }}</span>
                            <span class="ml-1">{{ plugin.installs }} installs</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

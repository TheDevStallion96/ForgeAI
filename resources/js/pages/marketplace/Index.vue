<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
    Bot,
    Cpu,
    Download,
    Globe,
    Puzzle,
    Search,
    Trash2,
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

const page = usePage()
const searchParam = computed(() => {
    try { return new URL(page.url, window.location.origin).searchParams.get('search') ?? '' } catch { return '' }
})

const props = defineProps<{
    plugins: Array<{
        id: number
        name: string
        slug: string
        description: string | null
        version: string
        author: string | null
        category: string
        icon: string | null
        is_official: boolean
        is_installed: boolean
        tags: string[]
    }>
    categories: Array<{ value: string; label: string }>
    currentCategory: string
}>()

function install(slug: string) {
    router.post(marketplace.install({ plugin: slug }).url, {}, {
        preserveScroll: true,
    })
}

function uninstall(slug: string) {
    router.post(marketplace.uninstall({ plugin: slug }).url, {}, {
        preserveScroll: true,
    })
}

function categoryIcon(category: string) {
    const icons: Record<string, typeof Globe> = {
        tool: Puzzle,
        template: Bot,
        extension: Cpu,
    }
    return icons[category] ?? Globe
}
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
                <Input
                    placeholder="Search marketplace..."
                    class="pl-8"
                    :model-value="searchParam"
                    @input="router.get(marketplace.index().url, { search: ($event.target as HTMLInputElement).value, category: currentCategory }, { preserveScroll: true, preserveState: true })"
                />
            </div>
            <div class="flex gap-1 rounded-lg bg-muted p-1">
                <Link
                    v-for="cat in categories"
                    :key="cat.value"
                    :href="marketplace.index().url"
                    :data="{ category: cat.value === 'all' ? undefined : cat.value }"
                    class="rounded-md px-3 py-1.5 text-sm font-medium"
                    :class="cat.value === currentCategory ? 'bg-background shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                >
                    {{ cat.label }}
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="plugin in plugins" :key="plugin.id" class="group hover:shadow-md transition-shadow">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <component :is="categoryIcon(plugin.category)" class="h-5 w-5" />
                        </div>
                        <Button
                            v-if="plugin.is_installed"
                            variant="secondary"
                            size="sm"
                            class="gap-1"
                            @click="uninstall(plugin.slug)"
                        >
                            <Trash2 class="h-4 w-4" />
                            Uninstall
                        </Button>
                        <Button
                            v-else
                            variant="ghost"
                            size="sm"
                            class="gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            @click="install(plugin.slug)"
                        >
                            <Download class="h-4 w-4" />
                            Install
                        </Button>
                    </div>
                    <CardTitle class="mt-3 text-base">{{ plugin.name }}</CardTitle>
                    <CardDescription class="line-clamp-2">{{ plugin.description }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <Badge variant="outline" class="text-xs">{{ plugin.category }}</Badge>
                            <span v-if="plugin.author">by {{ plugin.author }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span v-if="plugin.is_official" class="text-xs font-medium text-primary">Official</span>
                            <span class="ml-1">v{{ plugin.version }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="plugins.length === 0" class="flex flex-col items-center gap-2 py-16 text-muted-foreground">
            <Puzzle class="h-12 w-12" />
            <p class="text-lg font-medium">No plugins found</p>
            <p class="text-sm">Try adjusting your search or filter.</p>
        </div>
    </div>
</template>

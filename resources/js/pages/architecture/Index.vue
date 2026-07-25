<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    BookMarked,
    FileText,
    GitMerge,
    GitBranch,
    Plus,
    Share2,
    X,
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
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import architecture from '@/routes/architecture'

type Adr = {
    id: number
    title: string
    adr_number: number
    status: string
    context: string | null
    created_at: string
}

defineProps<{
    adrs: Adr[]
    stats: {
        total: number
        accepted: number
        proposed: number
        draft: number
    }
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Architecture Studio', href: architecture.index().url },
        ],
    },
})

const statusStyles: Record<string, string> = {
    accepted: 'border-emerald-400 text-emerald-600 dark:text-emerald-400',
    proposed: 'border-amber-400 text-amber-600 dark:text-amber-400',
    draft: 'border-sky-400 text-sky-600 dark:text-sky-400',
    deprecated: 'border-red-400 text-red-600 dark:text-red-400',
}

const statusIcons: Record<string, typeof BookMarked> = {
    accepted: BookMarked,
    proposed: FileText,
    draft: FileText,
    deprecated: FileText,
}

const showDialog = ref(false)
const form = ref({ title: '', context: '', decision: '', consequences: '' })

function createAdr() {
    router.post(architecture.adrs.store().url, form.value, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showDialog.value = false
            form.value = { title: '', context: '', decision: '', consequences: '' }
        },
    })
}
</script>

<template>
    <Head title="Architecture Studio" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Architecture Studio</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Architectural Decision Records, domain context maps, and C4 diagrams.
                </p>
            </div>
            <Dialog v-model:open="showDialog">
                <DialogTrigger as-child>
                    <Button size="sm">
                        <Plus class="mr-1.5 h-4 w-4" />
                        New ADR
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>New Architectural Decision Record</DialogTitle>
                        <DialogDescription>Document an architectural decision.</DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4">
                        <div>
                            <label class="text-sm font-medium">Title</label>
                            <Input v-model="form.title" placeholder="ADR title..." />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Context</label>
                            <Textarea v-model="form.context" placeholder="The problem or background..." rows="2" />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Decision</label>
                            <Textarea v-model="form.decision" placeholder="What was decided..." rows="2" />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Consequences</label>
                            <Textarea v-model="form.consequences" placeholder="Tradeoffs and impact..." rows="2" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button variant="outline" @click="showDialog = false">Cancel</Button>
                        <Button @click="createAdr">Create ADR</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50">
                        <BookMarked class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3 text-base">ADR Log</CardTitle>
                    <CardDescription>{{ stats.total }} records</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <div
                            v-for="adr in adrs.slice(0, 10)"
                            :key="adr.id"
                            class="flex items-center justify-between rounded-lg border p-2.5 text-sm hover:bg-muted/50 cursor-pointer transition-colors"
                        >
                            <div class="min-w-0">
                                <p class="font-medium truncate">ADR-{{ adr.adr_number }}: {{ adr.title }}</p>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ adr.context?.slice(0, 80) }}</p>
                            </div>
                            <Badge variant="outline" :class="statusStyles[adr.status]" class="shrink-0 ml-2 capitalize">
                                <component :is="statusIcons[adr.status]" class="mr-1 h-3 w-3" />
                                {{ adr.status }}
                            </Badge>
                        </div>
                        <div v-if="adrs.length === 0" class="py-4 text-center text-sm text-muted-foreground">
                            No ADRs yet. Create one to get started.
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-950/50">
                        <Share2 class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3 text-base">Domain Context Map</CardTitle>
                    <CardDescription>Module boundaries and relationships</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed bg-muted/30 py-12">
                        <Share2 class="h-10 w-10 text-muted-foreground/50 mb-2" />
                        <p class="text-sm text-muted-foreground">C4 diagram viewer</p>
                        <p class="text-xs text-muted-foreground/60 mt-1">Interactive topology map</p>
                        <Button variant="secondary" size="sm" class="mt-4">
                            <GitMerge class="mr-1.5 h-4 w-4" />
                            Generate
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-950/50">
                        <GitMerge class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3 text-base">ADR Status</CardTitle>
                    <CardDescription>Decision record overview</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span>Accepted</span>
                            <Badge variant="outline" class="border-emerald-400 text-emerald-600">{{ stats.accepted }}</Badge>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span>Proposed</span>
                            <Badge variant="outline" class="border-amber-400 text-amber-600">{{ stats.proposed }}</Badge>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span>Draft</span>
                            <Badge variant="outline" class="border-sky-400 text-sky-600">{{ stats.draft }}</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

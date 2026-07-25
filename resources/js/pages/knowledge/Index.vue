<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import {
    BookOpen,
    FileText,
    Search,
    Upload,
    Trash2,
    X,
    File as FileIcon,
} from '@lucide/vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import knowledge from '@/routes/knowledge'

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Knowledge Hub', href: knowledge.index().url },
        ],
    },
})

const props = defineProps<{
    assets: Array<{
        id: number
        name: string
        mime_type: string
        file_size: number
        status: string
        created_at: string
    }>
}>()

const uploading = ref(false)
const dragOver = ref(false)
const fileInput = ref<HTMLInputElement>()

function uploadFile(file: File) {
    if (uploading.value) return
    uploading.value = true

    const form = new FormData()
    form.append('file', file)

    router.post(knowledge.store().url, form, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            uploading.value = false
        },
    })
}

function onFilePick(e: Event) {
    const files = (e.target as HTMLInputElement).files
    if (files?.length) uploadFile(files[0])
}

function onDrop(e: DragEvent) {
    dragOver.value = false
    const file = e.dataTransfer?.files?.[0]
    if (file) uploadFile(file)
}

function remove(id: number) {
    router.delete(knowledge.destroy(id).url, {
        preserveState: true,
        preserveScroll: true,
    })
}

function formatSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

const totalChunks = computed(() => props.assets.filter(a => a.status === 'ready').length)
const indexedCount = computed(() => props.assets.filter(a => a.status === 'ready').length)
</script>

<template>
    <Head title="Knowledge Hub" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Knowledge Hub</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Upload documents for AI context and semantic search.
                </p>
            </div>
            <div class="relative">
                <input
                    ref="fileInput"
                    type="file"
                    class="hidden"
                    accept=".txt,.pdf,.md,.csv,.json,.xml,.html,.jpg,.jpeg,.png,.gif,.webp"
                    @change="onFilePick"
                />
                <Button size="sm" :disabled="uploading" @click="fileInput?.click()">
                    <Upload class="mr-1.5 h-4 w-4" />
                    {{ uploading ? 'Uploading...' : 'Upload Document' }}
                </Button>
            </div>
        </div>

        <div
            class="relative flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed p-8 text-sm text-muted-foreground transition-colors hover:border-primary/50 hover:text-foreground"
            :class="{ 'border-primary bg-primary/5': dragOver }"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="onDrop"
            @click="fileInput?.click()"
        >
            <div class="flex flex-col items-center gap-2">
                <Upload class="h-6 w-6" />
                <span>Drop a file here or click to browse</span>
                <span class="text-xs">Supports PDF, Markdown, CSV, JSON, XML, HTML, images (max 10 MB)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Documents</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ assets.length }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">File Types</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ new Set(assets.map(a => a.mime_type)).size }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Indexed Assets</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-2 text-2xl font-bold text-emerald-500">
                        <span class="size-2 rounded-full bg-emerald-500" />
                        {{ indexedCount }}
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input placeholder="Search documents..." class="pl-8" />
            </div>
        </div>

        <div class="space-y-2">
            <div v-if="assets.length === 0" class="flex flex-col items-center gap-2 py-12 text-muted-foreground">
                <FileIcon class="h-10 w-10" />
                <p class="text-sm">No documents uploaded yet</p>
                <p class="text-xs">Drag and drop files or click the upload button above.</p>
            </div>
            <div
                v-for="asset in assets"
                :key="asset.id"
                class="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-muted/50"
            >
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <component :is="asset.mime_type === 'application/pdf' ? BookOpen : FileText" class="h-5 w-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium">{{ asset.name }}</p>
                    <p class="text-xs text-muted-foreground">{{ formatSize(asset.file_size) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="asset.status === 'ready'
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400'
                            : 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400'"
                    >
                        <span
                            class="mr-1 size-1.5 rounded-full"
                            :class="asset.status === 'ready' ? 'bg-emerald-500' : 'bg-amber-500'"
                        />
                        {{ asset.status }}
                    </span>
                    <Button variant="ghost" size="icon" class="size-8 text-muted-foreground hover:text-destructive" @click="remove(asset.id)">
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { CalendarDays, Eye, EyeOff, Key, KeyRound, Plus, Trash2 } from '@lucide/vue'
import { ref } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
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
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import governance from '@/routes/governance'

const props = defineProps<{
    keys: Array<{
        id: number
        provider: string
        name: string | null
        key_preview: string
        is_active: boolean
        last_used_at: string | null
    }>
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Governance', href: governance.apiKeys.index().url },
            { title: 'API Keys', href: governance.apiKeys.index().url },
        ],
    },
})

const visibleKeys = ref<Set<number>>(new Set())
const addDialogOpen = ref(false)
const saving = ref(false)
const deleteConfirmKeyId = ref<number | null>(null)
const deleteDialogOpen = ref(false)
const form = ref({
    provider: '',
    name: '',
    key: '',
})

function toggleVisibility(id: number) {
    if (visibleKeys.value.has(id)) {
        visibleKeys.value.delete(id)
    } else {
        visibleKeys.value.add(id)
    }
}

function submitAdd() {
    saving.value = true
    router.post(governance.apiKeys.store().url, {
        provider: form.value.provider,
        name: form.value.name || undefined,
        key: form.value.key,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            addDialogOpen.value = false
            form.value = { provider: '', name: '', key: '' }
            saving.value = false
        },
        onError: () => {
            saving.value = false
        },
        onFinish: () => {
            saving.value = false
        },
    })
}

function confirmDelete(id: number) {
    deleteConfirmKeyId.value = id
    deleteDialogOpen.value = true
}

function executeDelete() {
    if (deleteConfirmKeyId.value === null) return

    const id = deleteConfirmKeyId.value
    deleteDialogOpen.value = false
    deleteConfirmKeyId.value = null

    router.delete(governance.apiKeys.destroy(id).url, {
        preserveScroll: true,
    })
}

function cancelDelete() {
    deleteDialogOpen.value = false
    deleteConfirmKeyId.value = null
}

const providers = [
    { value: 'openai', label: 'OpenAI', color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' },
    { value: 'anthropic', label: 'Anthropic', color: 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300' },
    { value: 'gemini', label: 'Gemini', color: 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300' },
    { value: 'azure', label: 'Azure', color: 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300' },
    { value: 'groq', label: 'Groq', color: 'bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300' },
    { value: 'xai', label: 'xAI', color: 'bg-zinc-100 text-zinc-700 dark:bg-zinc-950/50 dark:text-zinc-300' },
    { value: 'deepseek', label: 'DeepSeek', color: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-300' },
    { value: 'mistral', label: 'Mistral', color: 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300' },
    { value: 'ollama', label: 'Ollama', color: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/50 dark:text-yellow-300' },
    { value: 'openrouter', label: 'OpenRouter', color: 'bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300' },
    { value: 'bedrock', label: 'Bedrock', color: 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' },
    { value: 'cohere', label: 'Cohere', color: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' },
    { value: 'jina', label: 'Jina', color: 'bg-pink-100 text-pink-700 dark:bg-pink-950/50 dark:text-pink-300' },
    { value: 'voyageai', label: 'VoyageAI', color: 'bg-lime-100 text-lime-700 dark:bg-lime-950/50 dark:text-lime-300' },
    { value: 'eleven', label: 'ElevenLabs', color: 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-950/50 dark:text-fuchsia-300' },
    { value: 'openai-compatible', label: 'OpenAI Compatible', color: 'bg-gray-100 text-gray-700 dark:bg-gray-950/50 dark:text-gray-300' },
]

const providerColorMap = Object.fromEntries(providers.map(p => [p.value, p.color]))
</script>

<template>
    <Head title="API Keys" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">API Keys</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage API keys for AI provider authentication.
                </p>
            </div>
            <Dialog v-model:open="addDialogOpen">
                <DialogTrigger as-child>
                    <Button size="sm">
                        <Plus class="mr-1.5 h-4 w-4" />
                        Add Key
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add API Key</DialogTitle>
                        <DialogDescription>
                            Securely store an API key for an AI provider.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submitAdd" class="space-y-4">
                        <div class="space-y-1.5">
                            <Label for="provider">Provider</Label>
                            <Select v-model="form.provider">
                                <SelectTrigger id="provider">
                                    <SelectValue placeholder="Select a provider..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="p in providers" :key="p.value" :value="p.value">
                                        {{ p.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-1.5">
                            <Label for="name">Label (optional)</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="e.g. Production, Development"
                            />
                        </div>
                        <div class="space-y-1.5">
                            <Label for="key">API Key</Label>
                            <Input
                                id="key"
                                v-model="form.key"
                                type="password"
                                placeholder="sk-..."
                            />
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="saving || !form.provider || !form.key">
                                {{ saving ? 'Saving...' : 'Save Key' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <div v-if="keys.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <KeyRound class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No API keys</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Add provider API keys to enable AI agents.
            </p>
        </div>

        <div v-else class="space-y-3">
            <Card v-for="key in keys" :key="key.id">
                <CardContent class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Key class="h-5 w-5" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">{{ key.name ?? key.provider }}</span>
                                <span
                                    class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                    :class="providerColorMap[key.provider] ?? 'bg-muted text-muted-foreground'"
                                >
                                    {{ key.provider }}
                                </span>
                                <Badge
                                    variant="outline"
                                    :class="key.is_active
                                        ? 'border-emerald-400 text-emerald-600'
                                        : 'border-red-400 text-red-600'"
                                >
                                    {{ key.is_active ? 'Active' : 'Revoked' }}
                                </Badge>
                            </div>
                            <div class="mt-1 flex items-center gap-3 text-xs text-muted-foreground">
                                <span class="font-mono">
                                    {{ visibleKeys.has(key.id) ? key.key_preview : '••••' + key.key_preview.slice(-4) }}
                                </span>
                                <button
                                    @click="toggleVisibility(key.id)"
                                    class="hover:text-foreground transition-colors"
                                >
                                    <component :is="visibleKeys.has(key.id) ? EyeOff : Eye" class="h-3.5 w-3.5" />
                                </button>
                                <span v-if="key.last_used_at" class="flex items-center gap-1">
                                    <CalendarDays class="h-3 w-3" />
                                    Last used {{ new Date(key.last_used_at).toLocaleDateString() }}
                                </span>
                                <span v-else class="italic">Never used</span>
                            </div>
                        </div>
                    </div>
                    <Button
                        v-if="key.is_active"
                        variant="ghost"
                        size="icon"
                        class="size-8 text-muted-foreground hover:text-red-600"
                        @click="confirmDelete(key.id)"
                    >
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </CardContent>
            </Card>
        </div>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Revoke API Key</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to revoke this API key? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelDelete">Cancel</Button>
                    <Button variant="destructive" @click="executeDelete">Revoke</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

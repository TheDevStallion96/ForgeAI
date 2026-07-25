<script setup lang="ts">
import {
    Bot,
    CircleStop,
    File,
    FileImage,
    FileText,
    Mic,
    Paperclip,
    SendHorizonal,
    Square,
    User,
    Wrench,
    X,
} from '@lucide/vue'
import { computed, nextTick, ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import ToolCallCard from '@/components/agent/ToolCallCard.vue'
import type { ToolCall } from '@/components/agent/ToolCallCard.vue'
import HitlApproval from '@/components/agent/HitlApproval.vue'
import type { HitlRequest } from '@/components/agent/HitlApproval.vue'

export type Message = {
    id: string | number
    role: 'user' | 'assistant' | 'tool'
    content: string | null
    tool_calls?: ToolCall[]
    created_at?: string | null
}

export type Attachment = {
    id: string
    file: File
    preview?: string
}

const props = defineProps<{
    messages: Message[]
    streaming?: boolean
    streamingContent?: string
    hitlRequest?: HitlRequest | null
    hitlProcessing?: boolean
    placeholder?: string
    disabled?: boolean
}>()

const emit = defineEmits<{
    send: [prompt: string, attachments?: File[]]
    stop: []
    approve: []
    reject: []
}>()

const prompt = ref('')
const attachments = ref<Attachment[]>([])
const isRecording = ref(false)
const textareaRef = ref<HTMLTextAreaElement | null>(null)
const fileInputRef = ref<HTMLInputElement | null>(null)
const messagesEnd = ref<HTMLDivElement | null>(null)
const dropZoneActive = ref(false)

function submit() {
    const text = prompt.value.trim()
    if (!text && attachments.value.length === 0) return
    if (props.disabled || props.streaming) return

    const files = attachments.value.map(a => a.file)
    const textToSend = text

    prompt.value = ''
    attachments.value = []
    resetTextareaHeight()

    emit('send', textToSend, files.length > 0 ? files : undefined)
}

function stopStreaming() {
    emit('stop')
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault()
        submit()
    }
}

function resetTextareaHeight() {
    if (textareaRef.value) {
        textareaRef.value.style.height = 'auto'
    }
}

function autoResize() {
    const el = textareaRef.value
    if (!el) return
    el.style.height = 'auto'
    el.style.height = `${Math.min(el.scrollHeight, 200)}px`
}

function triggerFileUpload() {
    fileInputRef.value?.click()
}

function handleFileInput(e: Event) {
    const input = e.target as HTMLInputElement
    if (!input.files) return
    addFiles(Array.from(input.files))
    input.value = ''
}

function addFiles(files: File[]) {
    for (const file of files) {
        const id = `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`
        const attachment: Attachment = { id, file }
        if (file.type.startsWith('image/')) {
            attachment.preview = URL.createObjectURL(file)
        }
        attachments.value.push(attachment)
    }
}

function removeAttachment(id: string) {
    const idx = attachments.value.findIndex(a => a.id === id)
    if (idx !== -1) {
        const att = attachments.value[idx]
        if (att.preview) URL.revokeObjectURL(att.preview)
        attachments.value.splice(idx, 1)
    }
}

function toggleRecording() {
    isRecording.value = !isRecording.value
    if (isRecording.value) {
        setTimeout(() => {
            isRecording.value = false
        }, 5000)
    }
}

const fileIcon = computed(() => (file: File) => {
    if (file.type.startsWith('image/')) return FileImage
    if (file.type.includes('pdf')) return FileText
    if (file.type.includes('text')) return FileText
    return File
})

function formatSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

const totalAttachmentSize = computed(() => {
    return attachments.value.reduce((sum, a) => sum + a.file.size, 0)
})

const maxAttachmentSize = 10 * 1024 * 1024

function handleDragOver(e: DragEvent) {
    e.preventDefault()
    dropZoneActive.value = true
}

function handleDragLeave() {
    dropZoneActive.value = false
}

function handleDrop(e: DragEvent) {
    e.preventDefault()
    dropZoneActive.value = false
    if (e.dataTransfer?.files) {
        const validFiles = Array.from(e.dataTransfer.files).filter(f => f.size <= maxAttachmentSize)
        const oversized = Array.from(e.dataTransfer.files).filter(f => f.size > maxAttachmentSize)
        if (validFiles.length > 0) addFiles(validFiles)
    }
}

watch(
    () => props.messages.length + (props.streamingContent?.length ?? 0),
    async () => {
        await nextTick()
        messagesEnd.value?.scrollIntoView({ behavior: 'smooth' })
    },
)

watch(prompt, () => {
    autoResize()
})
</script>

<template>
    <div
        class="flex h-full flex-col"
        @dragover="handleDragOver"
        @dragleave="handleDragLeave"
        @drop="handleDrop"
    >
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-if="messages.length === 0 && !streaming" class="flex flex-col items-center justify-center py-16 text-muted-foreground">
                <Bot class="mb-3 h-10 w-10" />
                <p class="text-sm font-medium">Start a conversation</p>
                <p class="text-xs mt-1">Send a message to begin the agent session.</p>
            </div>

            <div
                v-for="msg in messages"
                :key="msg.id"
                class="flex gap-3"
                :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
            >
                <div
                    class="flex size-8 shrink-0 items-center justify-center rounded-full"
                    :class="msg.role === 'user'
                        ? 'bg-primary text-primary-foreground'
                        : msg.role === 'tool'
                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'
                            : 'bg-muted text-muted-foreground'"
                >
                    <component
                        :is="msg.role === 'user' ? User : (msg.role === 'tool' ? Wrench : Bot)"
                        class="h-4 w-4"
                    />
                </div>

                <div class="max-w-[80%] space-y-2">
                    <div
                        v-if="msg.content"
                        class="rounded-xl px-4 py-3 text-sm whitespace-pre-wrap"
                        :class="msg.role === 'user'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-muted'"
                    >
                        {{ msg.content }}
                    </div>

                    <div v-if="msg.tool_calls && msg.tool_calls.length > 0" class="space-y-1.5">
                        <ToolCallCard
                            v-for="tc in msg.tool_calls"
                            :key="tc.id"
                            :tool="tc"
                        />
                    </div>
                </div>
            </div>

            <div v-if="streaming" class="flex gap-3">
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <Bot class="h-4 w-4" />
                </div>
                <div class="max-w-[80%] rounded-xl bg-muted px-4 py-3 text-sm">
                    <span v-if="streamingContent" class="whitespace-pre-wrap">{{ streamingContent }}</span>
                    <span v-else class="inline-flex gap-1">
                        <span class="size-1.5 animate-bounce rounded-full bg-muted-foreground" style="animation-delay: 0ms" />
                        <span class="size-1.5 animate-bounce rounded-full bg-muted-foreground" style="animation-delay: 150ms" />
                        <span class="size-1.5 animate-bounce rounded-full bg-muted-foreground" style="animation-delay: 300ms" />
                    </span>
                </div>
            </div>

            <div ref="messagesEnd" />
        </div>

        <HitlApproval
            :request="hitlRequest ?? null"
            :processing="hitlProcessing"
            @approve="emit('approve')"
            @reject="emit('reject')"
        />

        <div
            class="relative border-t bg-background"
            :class="{ 'ring-2 ring-primary/30 rounded-t-xl': dropZoneActive }"
        >
            <div v-if="dropZoneActive" class="absolute inset-0 z-10 flex items-center justify-center rounded-t-xl bg-background/80 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-2 text-muted-foreground">
                    <Paperclip class="h-8 w-8" />
                    <span class="text-sm font-medium">Drop files here</span>
                </div>
            </div>

            <div v-if="attachments.length > 0" class="flex gap-2 overflow-x-auto border-b px-4 py-2">
                <div
                    v-for="att in attachments"
                    :key="att.id"
                    class="group relative flex shrink-0 items-center gap-2 rounded-lg border bg-muted/30 px-3 py-1.5 pr-2 text-xs"
                >
                    <component :is="fileIcon(att.file)" class="h-3.5 w-3.5 shrink-0 text-muted-foreground" />
                    <div class="flex flex-col">
                        <span class="max-w-28 truncate font-medium">{{ att.file.name }}</span>
                        <span class="text-[10px] text-muted-foreground">{{ formatSize(att.file.size) }}</span>
                    </div>
                    <button
                        class="ml-1 rounded p-0.5 text-muted-foreground/60 transition-colors hover:bg-destructive/10 hover:text-destructive"
                        @click="removeAttachment(att.id)"
                    >
                        <X class="h-3 w-3" />
                    </button>
                </div>
            </div>

            <div class="flex items-end gap-2 p-3">
                <input
                    ref="fileInputRef"
                    type="file"
                    multiple
                    class="hidden"
                    @change="handleFileInput"
                />

                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <button
                                type="button"
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-accent hover:text-foreground disabled:pointer-events-none disabled:opacity-40"
                                :disabled="disabled"
                                @click="triggerFileUpload"
                            >
                                <Paperclip class="h-4 w-4" />
                            </button>
                        </TooltipTrigger>
                        <TooltipContent side="top">Attach file</TooltipContent>
                    </Tooltip>
                </TooltipProvider>

                <div class="relative flex-1">
                    <textarea
                        ref="textareaRef"
                        v-model="prompt"
                        :placeholder="placeholder ?? 'Ask about your project, write code, review architecture...'"
                        :disabled="disabled"
                        rows="1"
                        class="block w-full resize-none rounded-xl border bg-muted/30 px-3 py-2.5 pr-10 text-sm outline-none transition-colors placeholder:text-muted-foreground/60 focus:border-ring focus:bg-background focus:ring-1 focus:ring-ring disabled:opacity-50"
                        @keydown="handleKeydown"
                    />
                </div>

                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <button
                                type="button"
                                class="flex size-9 shrink-0 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-accent hover:text-foreground disabled:pointer-events-none disabled:opacity-40"
                                :disabled="disabled"
                                @click="toggleRecording"
                            >
                                <Mic
                                    class="h-4 w-4 transition-colors"
                                    :class="isRecording ? 'text-red-500' : ''"
                                />
                            </button>
                        </TooltipTrigger>
                        <TooltipContent side="top">{{ isRecording ? 'Stop recording' : 'Voice input' }}</TooltipContent>
                    </Tooltip>
                </TooltipProvider>

                <div class="flex shrink-0 flex-col items-center">
                    <Button
                        v-if="!streaming"
                        type="submit"
                        size="icon"
                        :disabled="disabled || (!prompt.trim() && attachments.length === 0)"
                        class="size-9 rounded-xl"
                        @click="submit"
                    >
                        <SendHorizonal class="h-4 w-4" />
                    </Button>
                    <Button
                        v-else
                        type="button"
                        size="icon"
                        variant="destructive"
                        class="size-9 rounded-xl"
                        @click="stopStreaming"
                    >
                        <Square class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div class="flex items-center justify-between px-4 pb-2 text-[10px] text-muted-foreground/50">
                <span>Enter to send &middot; Shift+Enter for new line</span>
                <span v-if="prompt.length > 0" class="tabular-nums">{{ prompt.length }}</span>
            </div>
        </div>
    </div>
</template>

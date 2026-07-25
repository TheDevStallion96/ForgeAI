<script setup lang="ts">
import { AlertTriangle, Ban, CheckCircle, Wrench } from '@lucide/vue'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'

export type HitlRequest = {
    id: string
    tool_name: string
    tool_args: Record<string, unknown>
    agent_name: string
    reason?: string
}

defineProps<{
    request: HitlRequest | null
    processing?: boolean
}>()

const emit = defineEmits<{
    approve: []
    reject: []
}>()
</script>

<template>
    <Dialog :open="request !== null">
        <DialogContent>
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <AlertTriangle class="h-5 w-5 text-amber-500" />
                    Human Approval Required
                </DialogTitle>
                <DialogDescription>
                    <strong>{{ request?.agent_name }}</strong> wants to execute a tool that requires your approval.
                </DialogDescription>
            </DialogHeader>

            <div v-if="request" class="space-y-3">
                <div class="rounded-lg border bg-muted/50 p-3">
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <Wrench class="h-4 w-4 text-muted-foreground" />
                        {{ request.tool_name }}
                    </div>
                    <pre class="mt-2 overflow-x-auto rounded bg-background p-2 text-xs">{{ JSON.stringify(request.tool_args, null, 2) }}</pre>
                </div>

                <p v-if="request.reason" class="text-xs text-muted-foreground">
                    Reason: {{ request.reason }}
                </p>
            </div>

            <DialogFooter class="gap-2">
                <Button variant="outline" :disabled="processing" @click="emit('reject')">
                    <Ban class="mr-1.5 h-4 w-4" />
                    Reject
                </Button>
                <Button :disabled="processing" @click="emit('approve')">
                    <CheckCircle class="mr-1.5 h-4 w-4" />
                    {{ processing ? 'Processing...' : 'Approve' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { Bot, Save, Trash2 } from '@lucide/vue'
import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import agents from '@/routes/agents'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

const props = defineProps<{
    agent: {
        id: number
        name: string
        primary_model: string
        fallback_model: string | null
        system_instruction: string | null
        temperature: number
        is_active: boolean
    }
    providers: Array<{ name: string; value: string }>
}>()

defineOptions({
    layout: (props: { agent: { name: string } }) => ({
        breadcrumbs: [
            { title: 'AI Agent Studio', href: agents.index().url },
            { title: props.agent.name, href: `/agents/${props.agent.id}/edit` },
        ],
    }),
})

const form = {
    name: props.agent.name,
    primary_model: props.agent.primary_model,
    fallback_model: props.agent.fallback_model ?? '',
    system_instruction: props.agent.system_instruction ?? '',
    temperature: props.agent.temperature,
    is_active: props.agent.is_active,
}

function save() {
    router.patch(`/agents/${props.agent.id}`, form, {
        preserveScroll: true,
    })
}

function destroy() {
    router.delete(`/agents/${props.agent.id}`, {
        onSuccess: () => router.visit('/agents'),
    })
}
</script>

<template>
    <Head :title="`Edit: ${agent.name}`" />

    <div class="flex flex-1 flex-col gap-6 p-6 max-w-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <Bot class="h-5 w-5" />
                </div>
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ agent.name }}</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Edit agent configuration and settings.
                    </p>
                </div>
            </div>
            <AlertDialog>
                <AlertDialogTrigger as-child>
                    <Button variant="destructive" size="sm">
                        <Trash2 class="mr-1.5 h-4 w-4" />
                        Delete
                    </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Delete Agent</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to delete "{{ agent.name }}"? This action cannot be undone.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction @click="destroy" class="bg-red-600 hover:bg-red-700">
                            Delete
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <form @submit.prevent="save" class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Basic Configuration</CardTitle>
                    <CardDescription>Agent name and identity settings</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-1.5">
                        <Label for="name">Agent Name</Label>
                        <Input id="name" :model-value="form.name" @update:model-value="form.name = $event" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="model">Primary Model</Label>
                        <Select :model-value="form.primary_model" @update:model-value="form.primary_model = $event">
                            <SelectTrigger id="model">
                                <SelectValue :placeholder="form.primary_model" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="p in providers" :key="p.value" :value="p.value">
                                    {{ p.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="fallback">Fallback Model</Label>
                        <Select :model-value="form.fallback_model" @update:model-value="form.fallback_model = $event">
                            <SelectTrigger id="fallback">
                                <SelectValue placeholder="Select fallback model" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="p in providers" :key="p.value" :value="p.value">
                                    {{ p.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">System Instruction</CardTitle>
                    <CardDescription>Define the agent's behavior and constraints</CardDescription>
                </CardHeader>
                <CardContent>
                    <textarea
                        :value="form.system_instruction"
                        @input="form.system_instruction = ($event.target as HTMLTextAreaElement).value"
                        class="w-full rounded-lg border p-3 text-sm min-h-[120px] bg-background"
                        placeholder="You are a helpful AI agent that..."
                    />
                </CardContent>
            </Card>

            <div class="flex justify-end gap-3">
                <Button type="submit">
                    <Save class="mr-1.5 h-4 w-4" />
                    Save Changes
                </Button>
            </div>
        </form>
    </div>
</template>

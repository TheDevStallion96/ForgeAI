<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { Bot, Save } from '@lucide/vue'
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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'AI Agent Studio', href: agents.index().url },
            { title: 'Create Agent', href: agents.create().url },
        ],
    },
})

defineProps<{
    providers: Array<{ name: string; value: string }>
}>()

const form = useForm({
    name: '',
    primary_model: '',
    system_instruction: '',
    temperature: 0.7,
    is_active: true,
    fallback_model: '',
})

function submit() {
    form.post('/agents')
}
</script>

<template>
    <Head title="Create Agent" />

    <div class="flex flex-1 flex-col gap-6 p-6 max-w-2xl">
        <div class="flex items-center gap-3">
            <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                <Bot class="h-5 w-5" />
            </div>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Create Agent</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Configure a new AI agent persona and model settings.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Basic Configuration</CardTitle>
                    <CardDescription>Agent name and identity settings</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-1.5">
                        <Label for="name">Agent Name</Label>
                        <Input id="name" v-model="form.name" placeholder="e.g. Code Reviewer" />
                        <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="model">Primary Model</Label>
                        <Select v-model="form.primary_model">
                            <SelectTrigger id="model">
                                <SelectValue placeholder="Select a model" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="p in providers" :key="p.value" :value="p.value">
                                    {{ p.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="form.errors.primary_model" class="text-sm text-red-500">{{ form.errors.primary_model }}</div>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="fallback">Fallback Model (optional)</Label>
                        <Select v-model="form.fallback_model">
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
                        v-model="form.system_instruction"
                        class="w-full rounded-lg border p-3 text-sm min-h-[120px] bg-background"
                        placeholder="You are a helpful AI agent that..."
                    />
                </CardContent>
            </Card>

            <div class="flex justify-end gap-3">
                <Button type="submit" :disabled="form.processing">
                    <Save class="mr-1.5 h-4 w-4" />
                    {{ form.processing ? 'Creating...' : 'Create Agent' }}
                </Button>
            </div>
        </form>
    </div>
</template>

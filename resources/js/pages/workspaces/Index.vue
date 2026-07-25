<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    CircleCheckBig,
    Code2,
    FlaskConical,
    Kanban,
    Layers,
    Plus,
    Workflow,
} from '@lucide/vue'
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
import { show as workspaceShow } from '@/routes/workspaces'

type Workspace = {
    id: number
    name: string
    slug: string
    description: string | null
    status: string
    created_at: string
}

defineProps<{
    workspaces: Workspace[]
}>()

const showDialog = ref(false)
const form = ref({ name: '', description: '' })

function createWorkspace() {
    router.post('/workspaces', form.value, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showDialog.value = false
            form.value = { name: '', description: '' }
        },
    })
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Workspaces',
                href: '/workspaces',
            },
        ],
    },
})

const statusIcon: Record<string, typeof CircleCheckBig> = {
    active: CircleCheckBig,
    archived: CircleCheckBig,
    pending: CircleCheckBig,
}

const projectIcons = [Code2, Workflow, Kanban, Layers, FlaskConical]

function randomIcon(index: number) {
    return projectIcons[index % projectIcons.length]
}
</script>

<template>
    <Head title="Workspaces" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Workspaces</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage your project workspaces, boards, and backlogs.
                </p>
            </div>
            <Dialog v-model:open="showDialog">
                <DialogTrigger as-child>
                    <Button size="sm">
                        <Plus class="mr-1.5 h-4 w-4" />
                        New Workspace
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>New Workspace</DialogTitle>
                        <DialogDescription>Create a workspace for your project.</DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4">
                        <div>
                            <label class="text-sm font-medium">Name</label>
                            <Input v-model="form.name" placeholder="Workspace name..." />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Description</label>
                            <Textarea v-model="form.description" placeholder="Optional description..." rows="2" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button variant="outline" @click="showDialog = false">Cancel</Button>
                        <Button @click="createWorkspace">Create</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>

        <div v-if="workspaces.length === 0" class="flex flex-col items-center justify-center py-20">
            <div class="mb-4 rounded-full bg-muted p-4">
                <Layers class="h-8 w-8 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium">No workspaces yet</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Create your first workspace to get started.
            </p>
        </div>

        <div
            v-else
            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="(workspace, index) in workspaces"
                :key="workspace.id"
                :href="workspaceShow(workspace).url"
                class="group block"
            >
                <Card class="transition-all duration-200 hover:shadow-md hover:border-primary/50 cursor-pointer h-full">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <component :is="randomIcon(index)" class="h-5 w-5" />
                            </div>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="[
                                    workspace.status === 'active'
                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400'
                                        : 'bg-gray-50 text-gray-600 dark:bg-gray-900 dark:text-gray-400',
                                ]"
                            >
                                <component :is="statusIcon[workspace.status]" class="h-3 w-3" />
                                {{ workspace.status }}
                            </span>
                        </div>
                        <CardTitle class="mt-3 text-base">
                            {{ workspace.name }}
                        </CardTitle>
                        <CardDescription class="line-clamp-2">
                            {{ workspace.description ?? 'No description' }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                            <span>Created {{ workspace.created_at }}</span>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>

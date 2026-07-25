<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Globe, Key, ArrowLeft, CircleCheck, CircleX } from '@lucide/vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
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
import { Label } from '@/components/ui/label'
import sourceControl from '@/routes/source-control'

type Installation = {
    id: number
    provider: string
    github_username: string
    scopes: string[] | null
    created_at: string
}

const props = defineProps<{
    installation: Installation | null
}>()

const token = ref('')
const connecting = ref(false)
const error = ref('')

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Source Control', href: sourceControl.index().url },
            { title: 'Connect GitHub', href: '' },
        ],
    },
})

function submitPat() {
    error.value = ''
    connecting.value = true
    router.post(sourceControl.github.pat().url, { token: token.value }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => { connecting.value = false },
        onError: (err) => {
            error.value = Object.values(err).join(', ')
            connecting.value = false
        },
    })
}

function redirectToOAuth() {
    window.location.href = sourceControl.github.oauth().url
}
</script>

<template>
    <Head title="Connect GitHub" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" as-child>
                <Link :href="sourceControl.index().url">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Connect GitHub</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Link your GitHub account to browse repositories, commits, and pull requests.
                </p>
            </div>
        </div>

        <div v-if="installation" class="max-w-lg">
            <Alert>
                <CircleCheck class="h-4 w-4" />
                <AlertTitle>Connected as {{ installation.github_username }}</AlertTitle>
                <AlertDescription>
                    <div class="mt-2 flex items-center gap-2">
                        <Badge variant="outline">{{ installation.provider.toUpperCase() }}</Badge>
                        <span class="text-xs text-muted-foreground">Connected {{ installation.created_at }}</span>
                    </div>
                </AlertDescription>
            </Alert>
        </div>

        <div class="grid gap-6 md:grid-cols-2 max-w-3xl">
            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <Key class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3">Personal Access Token</CardTitle>
                    <CardDescription>
                        Generate a classic or fine-grained PAT from GitHub Settings with <code>repo</code>
                        and <code>read:user</code> scopes.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitPat" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="token">GitHub Token</Label>
                            <Input
                                id="token"
                                v-model="token"
                                type="password"
                                placeholder="ghp_..."
                                required
                            />
                        </div>
                        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
                        <Button type="submit" :disabled="connecting || !token">
                            {{ connecting ? 'Connecting...' : 'Connect with PAT' }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <Globe class="h-5 w-5" />
                    </div>
                    <CardTitle class="mt-3">OAuth App</CardTitle>
                    <CardDescription>
                        Authorize via GitHub OAuth for a seamless connection with automatic token
                        management.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button variant="outline" class="gap-2" @click="redirectToOAuth">
                        <Globe class="h-4 w-4" />
                        Sign in with GitHub
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { AlertTriangle, Coins, Fuel, Gauge, Save, TrendingUp } from '@lucide/vue'
import { computed, ref, watch } from 'vue'
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
import governance from '@/routes/governance'

const props = defineProps<{
    budget: {
        id: number
        monthly_limit: number
        current_usage: number
        usage_percentage: number
        is_exhausted: boolean
        reset_at: string
    }
}>()

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Governance', href: governance.apiKeys.index().url },
            { title: 'Token Budget', href: governance.budget.show().url },
        ],
    },
})

const monthlyLimit = ref(props.budget.monthly_limit)
const saving = ref(false)
const usagePercent = ref(props.budget.usage_percentage)

watch(() => props.budget.usage_percentage, (v) => {
    usagePercent.value = v
})

function save() {
    saving.value = true
    router.put('/governance/budget', { monthly_limit: monthlyLimit.value }, {
        preserveScroll: true,
        onFinish: () => {
            saving.value = false
        },
    })
}

const gaugeColor = computed(() => {
    if (usagePercent.value >= 100) {
        return 'text-red-500'
    }

    if (usagePercent.value >= 80) {
        return 'text-amber-500'
    }

    return 'text-emerald-500'
})

const gaugeTrackColor = computed(() => {
    if (usagePercent.value >= 100) {
        return 'bg-red-200 dark:bg-red-950'
    }

    if (usagePercent.value >= 80) {
        return 'bg-amber-200 dark:bg-amber-950'
    }

    return 'bg-emerald-200 dark:bg-emerald-950'
})
</script>

<template>
    <Head title="Token Budget" />

    <div class="flex flex-1 flex-col gap-6 p-6 max-w-2xl">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Token Budget</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Monitor and manage your monthly token usage budget.
            </p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-base">
                    <Gauge class="h-5 w-5" />
                    Usage Overview
                </CardTitle>
                <CardDescription>
                    Current billing period ends {{ new Date(props.budget.reset_at).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' }) }}
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="flex items-center justify-center gap-8">
                    <div class="relative flex size-32 items-center justify-center">
                        <svg class="absolute inset-0 size-32 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.5" fill="none" class="stroke-muted" stroke-width="3" />
                            <circle
                                cx="18" cy="18" r="15.5"
                                fill="none"
                                :stroke="usagePercent >= 100 ? '#ef4444' : usagePercent >= 80 ? '#f59e0b' : '#10b981'"
                                stroke-width="3"
                                :stroke-dasharray="`${usagePercent * 0.9722} 100`"
                                stroke-linecap="round"
                            />
                        </svg>
                        <div class="text-center">
                            <span class="text-2xl font-bold" :class="gaugeColor">{{ Math.round(usagePercent) }}%</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-sm">
                            <Coins class="h-4 w-4 text-emerald-500" />
                            <span class="text-muted-foreground">Used:</span>
                            <span class="font-medium">{{ props.budget.current_usage.toLocaleString() }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <Fuel class="h-4 w-4 text-muted-foreground" />
                            <span class="text-muted-foreground">Limit:</span>
                            <span class="font-medium">{{ props.budget.monthly_limit.toLocaleString() }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <TrendingUp class="h-4 w-4 text-muted-foreground" />
                            <span class="text-muted-foreground">Remaining:</span>
                            <span class="font-medium">{{ (props.budget.monthly_limit - props.budget.current_usage).toLocaleString() }}</span>
                        </div>
                        <Badge v-if="props.budget.is_exhausted" variant="destructive" class="gap-1">
                            <AlertTriangle class="h-3 w-3" />
                            Budget Exhausted
                        </Badge>
                    </div>
                </div>

                <div class="h-2 rounded-full" :class="gaugeTrackColor">
                    <div
                        class="h-full rounded-full transition-all duration-500"
                        :class="gaugeColor.replace('text-', 'bg-')"
                        :style="{ width: `${Math.min(usagePercent, 100)}%` }"
                    />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-base">
                    <Coins class="h-5 w-5" />
                    Adjust Limit
                </CardTitle>
                <CardDescription>
                    Update the monthly token budget limit.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="save" class="flex items-end gap-3">
                    <div class="flex-1 space-y-1.5">
                        <Label for="monthly-limit">Monthly Token Limit</Label>
                        <Input
                            id="monthly-limit"
                            v-model.number="monthlyLimit"
                            type="number"
                            min="1000"
                            step="10000"
                        />
                    </div>
                    <Button type="submit" :disabled="saving">
                        <Save class="mr-1.5 h-4 w-4" />
                        {{ saving ? 'Saving...' : 'Save' }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>

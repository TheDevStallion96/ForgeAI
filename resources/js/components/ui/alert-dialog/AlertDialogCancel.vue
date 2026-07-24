<script setup lang="ts">
import type { AlertDialogCancelProps } from "reka-ui"
import { AlertDialogCancel } from "reka-ui"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'

const props = defineProps<AlertDialogCancelProps & { class?: string }>()
</script>

<template>
  <AlertDialogCancel data-slot="alert-dialog-cancel" v-bind="props" as-child>
    <Button variant="outline" :class="cn(props.class)">
      <slot />
    </Button>
  </AlertDialogCancel>
</template>

<script setup lang="ts">
defineProps<{
  budget: { id: number; monthly_limit: number; current_usage: number; usage_percentage: number; is_exhausted: boolean; reset_at: string }
}>()
</script>

<template>
  <div>Budget Show</div>
</template>

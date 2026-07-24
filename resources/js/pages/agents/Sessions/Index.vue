<script setup lang="ts">
defineProps<{
  sessions: { data: Array<Record<string, unknown>> }
  agent: { id: number; name: string } | null
}>()
</script>

<template>
  <div>Sessions Index</div>
</template>

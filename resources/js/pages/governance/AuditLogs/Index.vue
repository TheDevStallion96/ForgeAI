<script setup lang="ts">
defineProps<{
  logs: { data: Array<{ id: number; event_type: string; payload: Record<string, unknown>; metadata: Record<string, unknown>; is_tampered: boolean; created_at: string }> }
}>()
</script>

<template>
  <div>Audit Logs Index</div>
</template>

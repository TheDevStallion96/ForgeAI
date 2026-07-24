<script setup lang="ts">
defineProps<{
  agents: Array<{ id: number; name: string; primary_model: string; is_active: boolean; session_count: number; created_at: string }>
}>()
</script>

<template>
  <div>Agents Index</div>
</template>

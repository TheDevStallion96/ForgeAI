<script setup lang="ts">
defineProps<{
  agent: { id: number; name: string; primary_model: string; fallback_model: string | null; system_instruction: string | null; temperature: number; is_active: boolean }
  providers: Array<{ name: string; value: string }>
}>()
</script>

<template>
  <div>Edit Agent</div>
</template>

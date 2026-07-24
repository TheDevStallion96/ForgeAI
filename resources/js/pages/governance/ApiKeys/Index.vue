<script setup lang="ts">
defineProps<{
  keys: Array<{ id: number; provider: string; name: string | null; key_preview: string; is_active: boolean; last_used_at: string | null }>
}>()
</script>

<template>
  <div>API Keys Index</div>
</template>

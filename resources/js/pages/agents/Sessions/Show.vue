<script setup lang="ts">
defineProps<{
  session: { id: number; agent: { id: number; name: string }; status: string; context_tokens: number; created_at: string }
  messages: Array<{ id: number; role: string; content: string | null; tool_calls: unknown[] | null; created_at: string | null }>
}>()
</script>

<template>
  <div>Session Show</div>
</template>

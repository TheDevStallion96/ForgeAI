<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    prompt: '',
    system_instruction: '',
});

const response = ref('');
const loading = ref(false);

function submit() {
    loading.value = true;
    response.value = '';

    form.post('/ai/chat', {
        onSuccess: () => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}
</script>

<template>
    <Head title="AI Chat" />

    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">AI Chat</h1>

        <div class="mb-4">
            <form @submit.prevent="submit">
                <textarea
                    v-model="form.prompt"
                    class="mb-3 w-full rounded-lg border border-gray-300 p-3 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    rows="4"
                    placeholder="Enter your prompt..."
                ></textarea>

                <div class="mb-3">
                    <input
                        v-model="form.system_instruction"
                        class="w-full rounded-lg border border-gray-300 p-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        placeholder="System instruction (optional)"
                    />
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                    :disabled="form.processing || !form.prompt"
                >
                    {{ loading ? 'Streaming...' : 'Send' }}
                </button>
            </form>
        </div>

        <div v-if="response" class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
            <pre class="whitespace-pre-wrap text-sm">{{ response }}</pre>
        </div>
    </div>
</template>

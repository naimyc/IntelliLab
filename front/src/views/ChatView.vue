<script setup>
import { ref } from 'vue'
import axios from '../lib/axios'

const prompt = ref('')
const response = ref('')
const loading = ref(false)

const sendPrompt = async () => {
    if (!prompt.value.trim()) return

    loading.value = true
    response.value = ''

    try {
        const res = await axios.post('/api/chat', {
            prompt: prompt.value
        })

        response.value =
            res.data.choices?.[0]?.message?.content ??
            JSON.stringify(res.data, null, 2)
    } catch (err) {
    console.error(err);

    if (err.response) {
        console.log(err.response.data);
        response.value = JSON.stringify(err.response.data, null, 2);
    } else {
        response.value = err.message;
    }
}

    loading.value = false
}
</script>

<template>
    <div class="container py-5">
        <h1>AI Chat</h1>

        <textarea
            v-model="prompt"
            class="form-control mb-3"
            rows="6"
            placeholder="Ask something..."
        />

        <button
            class="btn btn-primary"
            @click="sendPrompt"
            :disabled="loading"
        >
            {{ loading ? 'Sending...' : 'Send' }}
        </button>

        <div v-if="response" class="mt-4">
            <h4>Response</h4>
            <pre>{{ response }}</pre>
        </div>
    </div>
</template>
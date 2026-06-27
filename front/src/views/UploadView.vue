<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Sidebar from '../components/Sidebar.vue'
import axios from '../lib/axios'
import { setExercise } from '../stores/exercise'

const router = useRouter()

const topic = ref('')
const loading = ref(false)
const error = ref('')

async function analyze() {
  if (!topic.value.trim() || loading.value) return

  loading.value = true
  error.value = ''

  try {
    const res = await axios.post('/api/generate', {
      topic: topic.value,
    })

    setExercise(res.data)
    router.push('/editor')
  } catch (err) {
    console.error(err)
    const data = err.response?.data
    if (data?.error && data?.raw) {
      error.value = `${data.error}. Bitte erneut versuchen.`
    } else if (data?.error) {
      error.value = data.error
    } else {
      error.value = err.message || 'Unbekannter Fehler. Bitte erneut versuchen.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="upload-page">
    <Sidebar />

    <div class="content">
      <div class="upload-card">
        <h1>Neue Analyse</h1>

        <p>
          Lade eine Aufgabe hoch oder füge den Aufgabentext direkt ein.
        </p>

        <!-- PDF upload comes in the later phase (PDF & OCR) -->
        <div class="upload-box is-disabled" title="Kommt bald: PDF & OCR">
          📄 PDF auswählen
          <span class="soon">bald verfügbar</span>
        </div>

        <p class="oder">ODER</p>

        <textarea
          v-model="topic"
          :disabled="loading"
          placeholder="Füge hier dein Thema oder deine Programmieraufgabe ein… z. B. „Java Vererbung“"
          @keydown.ctrl.enter="analyze"
        ></textarea>

        <p v-if="error" class="error">{{ error }}</p>

        <button :disabled="loading || !topic.trim()" @click="analyze">
          <span v-if="loading" class="spinner"></span>
          {{ loading ? 'Übung wird erstellt…' : 'Analysieren' }}
        </button>

        <p class="hint">Tipp: Strg + Enter zum Starten</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.upload-page {
  min-height: 100vh;
  display: flex;
  background: #f8fafc;
}

.content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px;
}

.upload-card {
  width: 700px;
  max-width: 100%;
  background: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
}

.upload-card h1 {
  margin-bottom: 10px;
}

.upload-box {
  margin-top: 25px;
  border: 2px dashed #7c3aed;
  border-radius: 16px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  position: relative;
}

.upload-box.is-disabled {
  opacity: .5;
  cursor: not-allowed;
  border-color: #c4b5fd;
}

.soon {
  position: absolute;
  top: 10px;
  right: 12px;
  font-size: .7rem;
  background: #ede9fe;
  color: #6d28d9;
  padding: 2px 8px;
  border-radius: 999px;
}

.oder {
  text-align: center;
  margin: 20px 0;
}

textarea {
  width: 100%;
  height: 180px;
  padding: 15px;
  border-radius: 12px;
  border: 1px solid #ddd;
  resize: none;
  font-family: inherit;
  font-size: 1rem;
}

textarea:focus {
  outline: none;
  border-color: #7c3aed;
}

.error {
  margin-top: 12px;
  color: #b91c1c;
  background: #fef2f2;
  border: 1px solid #fecaca;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: .9rem;
}

button {
  width: 100%;
  margin-top: 20px;
  padding: 15px;
  border: none;
  border-radius: 12px;
  background: #7c3aed;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

button:hover:not(:disabled) {
  background: #6d28d9;
}

button:disabled {
  opacity: .6;
  cursor: not-allowed;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, .4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin .7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.hint {
  margin-top: 12px;
  text-align: center;
  color: #94a3b8;
  font-size: .85rem;
}
</style>
import { reactive } from 'vue'

// Lightweight shared store so the generated exercise survives the
// UploadView -> EditorView navigation and a page refresh, without
// pulling in Pinia. sessionStorage clears when the tab closes.

const STORAGE_KEY = 'intellilabs_exercise'

function load() {
  try {
    const raw = sessionStorage.getItem(STORAGE_KEY)
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
}

export const exerciseStore = reactive({
  current: load(),
})

export function setExercise(data) {
  exerciseStore.current = data
  try {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data))
  } catch {
    /* storage full / disabled — in-memory copy still works */
  }
}

export function clearExercise() {
  exerciseStore.current = null
  try {
    sessionStorage.removeItem(STORAGE_KEY)
  } catch {
    /* ignore */
  }
}
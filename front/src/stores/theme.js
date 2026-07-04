import { reactive, watch } from 'vue'

// Globaler Dark/Light-Mode für die gesamte eingeloggte App (Dashboard, Editor,
// Login, etc.) — ein einziger Schalter statt eines pro Editor-Ansicht.
const STORAGE_KEY = 'il_theme_dark'

export const themeStore = reactive({
  dark: localStorage.getItem(STORAGE_KEY) === '1',
})

export function toggleTheme() {
  themeStore.dark = !themeStore.dark
}

export function applyTheme() {
  document.documentElement.classList.toggle('dark', themeStore.dark)
}

watch(() => themeStore.dark, v => {
  localStorage.setItem(STORAGE_KEY, v ? '1' : '0')
  applyTheme()
})

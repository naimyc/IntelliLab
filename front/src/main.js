import './style.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import TreeNode from './components/TreeNode.vue'
import { applyTheme } from './stores/theme'

applyTheme() // vor dem Mount setzen, damit die Seite nicht in hell aufblitzt

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.component('TreeNode', TreeNode)

// Importa auth store DOPO aver installato pinia
import('./stores/auth').then(({ useAuthStore }) => {
  const authStore = useAuthStore()
  authStore.init().finally(() => {
    app.mount('#app')
  })
})

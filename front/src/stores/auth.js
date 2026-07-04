import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../lib/axios'

export const useAuthStore = defineStore('auth', () => {
  const user    = ref(null)
  const loading = ref(false)
  const error   = ref(null)
  const initialized = ref(false)

  const isLoggedIn = computed(() => !!user.value)
  const isAuthenticated = isLoggedIn

  async function getCsrf() { await api.get('/sanctum/csrf-cookie') }

  async function register(name, email, password, passwordConfirmation, rolle = 'Student') {
    loading.value = true; error.value = null
    try {
      await getCsrf()
      const { data } = await api.post('/api/auth/register', {
        name, email, password,
        password_confirmation: passwordConfirmation,
        rolle,
      })
      user.value = data.user
    } catch(e) {
      const msgs = e.response?.data?.errors
      error.value = msgs ? Object.values(msgs).flat().join(' ') : (e.response?.data?.message ?? 'Registrierung fehlgeschlagen.')
    } finally { loading.value = false }
  }

  async function login(email, password) {
    loading.value = true; error.value = null
    try {
      await getCsrf()
      const { data } = await api.post('/api/auth/login', { email, password })
      user.value = data.user
    } catch(e) {
      const msgs = e.response?.data?.errors
      error.value = msgs ? Object.values(msgs).flat().join(' ') : (e.response?.data?.message ?? 'Login fehlgeschlagen.')
    } finally { loading.value = false }
  }

  async function logout() {
    loading.value = true
    try { await api.post('/api/auth/logout'); user.value = null }
    finally { loading.value = false }
  }

  async function fetchUser() {
    try { const { data } = await api.get('/api/auth/me'); user.value = data.user }
    catch { user.value = null }
  }

  async function init() {
    if (initialized.value) return
    loading.value = true
    try { await fetchUser() }
    finally { initialized.value = true; loading.value = false }
  }

  return { user, loading, error, initialized, isLoggedIn, isAuthenticated, register, login, logout, fetchUser, init }
})

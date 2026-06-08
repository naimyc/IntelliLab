<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth   = useAuthStore()

const name     = ref('')
const email    = ref('')
const password = ref('')
const confirm  = ref('')

async function register() {
  await auth.register(name.value, email.value, password.value, confirm.value)
  if (!auth.error) {
    router.push('/dashboard')
  }
}
</script>

<template>
  <div class="register-page">
    <div class="register-card">

      <div class="logo">🧠 IntelliLab</div>
      <h1>Registrieren</h1>

      <div v-if="auth.error" class="error-box">
        {{ auth.error }}
      </div>

      <label>Name</label>
      <input
        v-model="name"
        type="text"
        placeholder="Dein Name"
        required
      />

      <label>E-Mail-Adresse</label>
      <input
        v-model="email"
        type="email"
        placeholder="deine@email.de"
        required
      />

      <label>Passwort</label>
      <input
        v-model="password"
        type="password"
        placeholder="Mindestens 8 Zeichen"
        required
      />

      <label>Passwort bestätigen</label>
      <input
        v-model="confirm"
        type="password"
        placeholder="Passwort wiederholen"
        required
      />

      <button @click="register" :disabled="auth.loading">
        {{ auth.loading ? 'Bitte warten...' : 'Registrieren' }}
      </button>

      <p class="divider">oder weiter mit</p>

      <button class="google-btn">G Google</button>

      <p class="login-link">
        Bereits ein Konto?
        <router-link to="/login">Anmelden</router-link>
      </p>

    </div>
  </div>
</template>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f8fafc;
}

.register-card {
  width: 420px;
  background: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

.register-card h1 {
  margin-bottom: 30px;
}

.error-box {
  background: #fef2f2;
  border: 1px solid #fca5a5;
  color: #b91c1c;
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 0.9rem;
  margin-bottom: 16px;
  line-height: 1.5;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
}

input {
  width: 100%;
  padding: 14px;
  border: 1px solid #ddd;
  border-radius: 10px;
  margin-bottom: 15px;
  font-size: 1rem;
  box-sizing: border-box;
  outline: none;
  transition: border-color .15s;
}

input:focus {
  border-color: #7c3aed;
}

button {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 10px;
  background: #7c3aed;
  color: white;
  cursor: pointer;
  font-size: 1rem;
  transition: background .15s;
}

button:hover:not(:disabled) {
  background: #6d28d9;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.divider {
  text-align: center;
  margin: 20px 0;
  color: #888;
}

.google-btn {
  background: white;
  color: #444;
  border: 1px solid #ddd;
}

.google-btn:hover {
  background: #f3f4f6;
}

.login-link {
  text-align: center;
  margin-top: 20px;
}

.login-link a {
  color: #7c3aed;
  text-decoration: none;
  font-weight: bold;
}

.logo {
  text-align: center;
  font-size: 1.8rem;
  font-weight: bold;
  margin-bottom: 25px;
  color: #7c3aed;
}
</style>
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth   = useAuthStore()

const name     = ref('')
const email    = ref('')
const password = ref('')
const confirm  = ref('')
const rolle    = ref('Student')

const ROLLEN = ['Student','Studentin','Tutor','Tutorin','Dozent','Dozentin','Professor','Professorin']

async function register() {
  await auth.register(name.value, email.value, password.value, confirm.value, rolle.value)
  if (!auth.error) router.push('/dashboard')
}
</script>

<template>
  <div class="page">
    <div class="card">

      <router-link to="/" class="logo">
        <svg width="28" height="28" viewBox="0 0 100 100" fill="none">
          <circle cx="50" cy="50" r="50" fill="#7c3aed"/>
          <path d="M20 50 C20 28 35 18 50 18 C65 18 80 28 80 50 C80 72 65 82 50 82 C35 82 20 72 20 50Z" stroke="#a78bfa" stroke-width="3" fill="none"/>
          <path d="M50 18 C50 18 35 35 35 50 C35 65 50 82 50 82 C50 82 65 65 65 50 C65 35 50 18 50 18Z" stroke="#c4b5fd" stroke-width="2" fill="none"/>
          <path d="M18 50 L82 50" stroke="#a78bfa" stroke-width="2"/>
          <circle cx="50" cy="50" r="6" fill="#c4b5fd"/>
        </svg>
        <span>IntelliLab</span>
      </router-link>

      <h1>Registrieren</h1>

      <div v-if="auth.error" class="err">{{ auth.error }}</div>

      <label>Name</label>
      <input v-model="name" type="text" placeholder="Dein Name"/>

      <label>E-Mail-Adresse</label>
      <input v-model="email" type="email" placeholder="deine@email.de"/>

      <label>Rolle</label>
      <select v-model="rolle">
        <option v-for="r in ROLLEN" :key="r" :value="r">{{ r }}</option>
      </select>

      <label>Passwort</label>
      <input v-model="password" type="password" placeholder="Mindestens 8 Zeichen"/>

      <label>Passwort bestätigen</label>
      <input v-model="confirm" type="password" placeholder="Passwort wiederholen" @keydown.enter="register"/>

      <button @click="register" :disabled="auth.loading">
        {{ auth.loading ? 'Bitte warten…' : 'Registrieren' }}
      </button>

      <p class="link">Bereits ein Konto? <router-link to="/login">Anmelden</router-link></p>
    </div>
  </div>
</template>

<style scoped>
.page { min-height:100vh; display:flex; justify-content:center; align-items:center; background:var(--il-bg); }
.card { width:420px; background:var(--il-card); padding:40px; border-radius:20px; box-shadow:0 10px 25px rgba(0,0,0,.08); }
.logo { display:flex; align-items:center; justify-content:center; gap:10px; text-decoration:none; margin-bottom:24px; }
.logo span { font-size:1.3rem; font-weight:700; color:var(--il-accent); }
.card h1 { text-align:center; font-size:1.4rem; margin:0 0 24px; color:var(--il-text); }
.err { background:#fef2f2; border:1px solid #fca5a5; color:#b91c1c; border-radius:10px; padding:12px 14px; font-size:.88rem; margin-bottom:16px; }
label { display:block; font-weight:600; font-size:.85rem; color:var(--il-text); margin-bottom:5px; }
input, select { width:100%; padding:11px 13px; border:1.5px solid var(--il-border); border-radius:9px; margin-bottom:14px; font-size:.9rem; box-sizing:border-box; outline:none; background:var(--il-card); }
input:focus, select:focus { border-color:var(--il-accent); }
button { width:100%; padding:13px; border:none; border-radius:10px; background:#7c3aed; color:white; cursor:pointer; font-size:.95rem; font-weight:600; margin-top:4px; }
button:hover:not(:disabled) { background:#6d28d9; }
button:disabled { opacity:.6; cursor:not-allowed; }
.link { text-align:center; margin-top:18px; font-size:.88rem; color:var(--il-text-2); }
.link a { color:var(--il-accent); font-weight:600; text-decoration:none; }
</style>

<style scoped>
@media (max-width: 480px) {
  .card { padding: 26px 20px; }
  .page { padding: 14px; align-items: flex-start; padding-top: 30px; }
}
</style>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Sidebar from '../components/Sidebar.vue'

const auth   = useAuthStore()
const router = useRouter()

// Initialen für Avatar (z.B. "Ada Lovelace" → "AL")
const initials = auth.user?.name
  ?.split(' ')
  .map(w => w[0].toUpperCase())
  .join('')
  .slice(0, 2) ?? '?'

// "2024-01-15T..." → "Januar 2024"
const memberSince = auth.user?.created_at
  ? new Date(auth.user.created_at).toLocaleDateString('de', { month: 'long', year: 'numeric' })
  : '—'

async function logout() {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <div class="profile-page">

    <Sidebar />

    <div class="content">
      <div class="profile-card">

        <div class="avatar">{{ initials }}</div>

        <h1>Mein Profil</h1>

        <div class="info">
          <label>Name</label>
          <p>{{ auth.user?.name ?? '—' }}</p>
        </div>

        <div class="info">
          <label>E-Mail</label>
          <p>{{ auth.user?.email ?? '—' }}</p>
        </div>

        <div class="info">
          <label>E-Mail verifiziert</label>
          <p>{{ auth.user?.email_verified_at ? 'Ja' : 'Nein' }}</p>
        </div>

        <div class="info">
          <label>Analysen durchgeführt</label>
          <p>0</p>
        </div>

        <div class="info">
          <label>Mitglied seit</label>
          <p>{{ memberSince }}</p>
        </div>

        <div class="info">
          <label>Rolle</label>
          <p>Student</p>
        </div>

        <div class="actions">
          <button class="edit-btn" @click="alert('Profil bearbeiten wird später umgesetzt.')">
            Profil bearbeiten
          </button>
          <button class="logout-btn" :disabled="auth.loading" @click="logout">
            {{ auth.loading ? 'Abmelden...' : 'Abmelden' }}
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<style scoped>
.profile-page {
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

.profile-card {
  width: 600px;
  background: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,.08);
}

/* Initialen-Avatar statt Emoji */
.avatar {
  width: 80px;
  height: 80px;
  background: #ede9fe;
  color: #7c3aed;
  border-radius: 50%;
  font-size: 1.8rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.profile-card h1 {
  text-align: center;
  margin-bottom: 30px;
}

.info {
  margin-bottom: 20px;
}

.info label {
  display: block;
  font-weight: bold;
  color: #666;
  margin-bottom: 5px;
}

.info p {
  font-size: 1.1rem;
}

.actions {
  margin-top: 28px;
  display: flex;
  gap: 12px;
}

button {
  flex: 1;
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  transition: background .15s;
}

.edit-btn {
  background: #7c3aed;
  color: white;
}

.edit-btn:hover {
  background: #6d28d9;
}

.logout-btn {
  background: white;
  color: #7c3aed;
  border: 1px solid #7c3aed;
}

.logout-btn:hover:not(:disabled) {
  background: #f5f3ff;
}

.logout-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
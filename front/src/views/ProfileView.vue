<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { projectStore, fetchStats } from '../stores/projects'
import AppLayout from '../components/AppLayout.vue'
import api from '../lib/axios'

const router = useRouter()
const auth = useAuthStore()
onMounted(() => fetchStats())

const showEdit = ref(false)
const editName = ref(''); const editRolle = ref(''); const editLoading = ref(false); const editError = ref('')
const avatarInput = ref(null)
const ROLLEN = ['Student','Studentin','Tutor','Tutorin','Dozent','Dozentin','Professor','Professorin']

const initials = computed(() => auth.user?.name?.split(' ').map(w=>w[0]?.toUpperCase()).join('').slice(0,2) ?? '?')
const memberSince = computed(() => auth.user?.created_at ? new Date(auth.user.created_at).toLocaleDateString('de',{month:'long',year:'numeric'}) : '—')

function openEdit() { editName.value=auth.user?.name||''; editRolle.value=auth.user?.rolle||'Student'; editError.value=''; showEdit.value=true }

async function saveEdit() {
  editLoading.value=true; editError.value=''
  try {
    const {data} = await api.patch('/api/profile',{name:editName.value,rolle:editRolle.value})
    auth.user = data.user; showEdit.value=false
  } catch(e) { editError.value=e.response?.data?.message||'Fehler.' }
  finally { editLoading.value=false }
}

async function uploadAvatar(e) {
  const file=e.target.files[0]; if (!file) return
  const fd=new FormData(); fd.append('avatar',file)
  try {
    const {data}=await api.post('/api/profile/avatar',fd,{headers:{'Content-Type':'multipart/form-data'}})
    auth.user={...auth.user,avatar_url:data.avatar_url}
  } catch { alert('Avatar konnte nicht hochgeladen werden.') }
}

async function logout() { await auth.logout(); router.push('/login') }
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="profile-card">
        <div class="avatar-wrap">
          <div class="avatar" @click="avatarInput.click()">
            <img v-if="auth.user?.avatar_url" :src="auth.user.avatar_url" alt="Avatar"/>
            <span v-else>{{ initials }}</span>
            <div class="av-overlay">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
          </div>
          <input ref="avatarInput" type="file" accept="image/*" hidden @change="uploadAvatar"/>
          <p class="av-hint">Klicke um das Bild zu ändern</p>
        </div>

        <h1>{{ auth.user?.name ?? '—' }}</h1>
        <span class="rolle-badge">{{ auth.user?.rolle ?? 'Student' }}</span>

        <div class="info-grid">
          <div class="info-item"><label>E-Mail</label><p>{{ auth.user?.email ?? '—' }}</p></div>
          <div class="info-item">
            <label>E-Mail verifiziert</label>
            <p><span class="verified" v-if="auth.user?.email_verified_at">✓ Verifiziert</span><span class="not-verified" v-else>✗ Nicht verifiziert</span></p>
          </div>
          <div class="info-item"><label>Abgeschlossene Projekte</label><p>{{ projectStore.stats.done }} / {{ projectStore.stats.total }}</p></div>
          <div class="info-item"><label>Mitglied seit</label><p>{{ memberSince }}</p></div>
        </div>

        <div class="pw-hint" v-if="!auth.user?.email_verified_at">
          ⓘ Passwort ändern und „Passwort vergessen?" sind nur mit verifizierter E-Mail verfügbar.
        </div>

        <div class="actions">
          <button class="btn-edit" @click="openEdit">Profil bearbeiten</button>
          <button class="btn-logout" @click="logout" :disabled="auth.loading">{{ auth.loading ? 'Abmelden…' : 'Abmelden' }}</button>
        </div>
      </div>
    </div>
  </AppLayout>

  <div v-if="showEdit" class="modal-bg" @click.self="showEdit=false">
    <div class="modal">
      <h2>Profil bearbeiten</h2>
      <label>Name</label><input v-model="editName" placeholder="Dein Name"/>
      <label>Rolle</label>
      <select v-model="editRolle"><option v-for="r in ROLLEN" :key="r" :value="r">{{ r }}</option></select>
      <p v-if="editError" class="err">{{ editError }}</p>
      <div class="modal-btns">
        <button class="cancel" @click="showEdit=false">Abbrechen</button>
        <button class="ok" @click="saveEdit" :disabled="editLoading">{{ editLoading?'Speichert…':'Speichern' }}</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page { padding:24px 28px; display:flex; justify-content:center; }
.profile-card { width:100%; max-width:520px; background:var(--il-card); padding:32px; border-radius:18px; box-shadow:0 8px 28px rgba(0,0,0,.08); }

.avatar-wrap { display:flex; flex-direction:column; align-items:center; margin-bottom:18px; }
.avatar { width:80px; height:80px; border-radius:50%; position:relative; background:var(--il-accent-soft); color:var(--il-accent); font-size:1.6rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; border:3px solid #c4b5fd; }
.avatar img { width:100%; height:100%; object-fit:cover; }
.av-overlay { position:absolute; inset:0; background:rgba(0,0,0,.4); display:flex; align-items:center; justify-content:center; opacity:0; transition:.15s; }
.avatar:hover .av-overlay { opacity:1; }
.av-hint { font-size:.7rem; color:var(--il-text-3); margin-top:6px; }

h1 { text-align:center; font-size:1.3rem; margin:0 0 6px; color:var(--il-text); }
.rolle-badge { display:block; text-align:center; font-size:.8rem; color:var(--il-accent); font-weight:600; background:var(--il-accent-soft); padding:3px 12px; border-radius:999px; width:fit-content; margin:0 auto 22px; }

.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; }
.info-item label { display:block; font-size:.72rem; font-weight:600; color:var(--il-text-3); margin-bottom:3px; text-transform:uppercase; letter-spacing:.04em; }
.info-item p { font-size:.88rem; color:var(--il-text); margin:0; }
.verified { color:#059669; font-weight:600; }
.not-verified { color:#dc2626; }

.pw-hint { background:#fef9c3; border:1px solid #fde68a; border-radius:8px; padding:10px 12px; font-size:.78rem; color:#92400e; margin-bottom:18px; }

.actions { display:flex; gap:10px; flex-wrap:wrap; }
.btn-edit { flex:1; padding:11px; background:#7c3aed; color:white; border:none; border-radius:10px; font-size:.88rem; font-weight:600; cursor:pointer; }
.btn-edit:hover { background:#6d28d9; }
.btn-logout { flex:1; padding:11px; background:var(--il-card); color:var(--il-accent); border:1.5px solid #c4b5fd; border-radius:10px; font-size:.88rem; font-weight:600; cursor:pointer; }
.btn-logout:hover:not(:disabled) { background:#faf5ff; }
.btn-logout:disabled { opacity:.5; cursor:not-allowed; }

/* Modal */
.modal-bg { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; display:flex; align-items:center; justify-content:center; padding:20px; }
.modal { background:var(--il-card); border-radius:16px; padding:26px; width:360px; max-width:100%; box-shadow:0 20px 50px rgba(0,0,0,.15); }
.modal h2 { font-size:1rem; margin:0 0 18px; color:var(--il-text); }
label { display:block; font-weight:600; font-size:.8rem; color:var(--il-text); margin-bottom:5px; }
input,select { width:100%; padding:10px 12px; border:1.5px solid var(--il-border); border-radius:9px; margin-bottom:14px; font-size:.88rem; box-sizing:border-box; outline:none; }
input:focus,select:focus { border-color:var(--il-accent); }
.err { color:#b91c1c; background:#fef2f2; padding:8px 12px; border-radius:8px; font-size:.82rem; margin-bottom:12px; }
.modal-btns { display:flex; gap:10px; margin-top:4px; }
.cancel { flex:1; padding:10px; background:var(--il-card); border:1.5px solid var(--il-border); color:var(--il-text-2); border-radius:9px; cursor:pointer; font-size:.86rem; }
.ok { flex:1; padding:10px; background:#7c3aed; color:white; border:none; border-radius:9px; cursor:pointer; font-size:.86rem; font-weight:600; }
.ok:disabled { opacity:.5; cursor:not-allowed; }

@media (max-width:600px) {
  .page { padding:16px; }
  .profile-card { padding:22px; }
  .info-grid { grid-template-columns:1fr; }
}
</style>

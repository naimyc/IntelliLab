<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { themeStore, toggleTheme } from '../stores/theme'

const router = useRouter()
const auth   = useAuthStore()

const mobileOpen = ref(false)
const isMobile   = ref(window.innerWidth < 768)

function onResize() {
  isMobile.value = window.innerWidth < 768
  if (!isMobile.value) mobileOpen.value = false
}
onMounted(()  => window.addEventListener('resize', onResize))
onUnmounted(()=> window.removeEventListener('resize', onResize))

const initials = computed(() =>
  auth.user?.name?.split(' ').map(w => w[0]?.toUpperCase()).join('').slice(0,2) ?? '?'
)

// Der Logo-Link soll eingeloggte Nutzer nicht auf die öffentliche Marketing-
// Homepage schicken — das ergibt innerhalb der App keinen Sinn.
const homeTarget = computed(() => auth.isLoggedIn ? '/dashboard' : '/')

function closeMobile() { mobileOpen.value = false }
</script>

<template>
  <!-- MOBILE: topbar -->
  <div v-if="isMobile" class="mobile-topbar">
    <router-link :to="homeTarget" class="top-logo">
      <img src="/ilab.png" alt="IntelliLab" style="height:26px;width:auto;"/>
      <span>IntelliLab</span>
    </router-link>
    <div style="display:flex;align-items:center;gap:4px;">
      <button class="theme-toggle" @click="toggleTheme" :title="themeStore.dark ? 'Light Modus' : 'Night Modus'">{{ themeStore.dark ? '☀' : '🌙' }}</button>
      <button class="hamburger" @click="mobileOpen = !mobileOpen">
        <svg v-if="!mobileOpen" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
        <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="6" y1="6" x2="18" y2="18"/>
          <line x1="18" y1="6" x2="6" y2="18"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- MOBILE MENU OVERLAY -->
  <div v-if="isMobile && mobileOpen" class="mobile-overlay" @click="closeMobile">
    <div class="mobile-menu" @click.stop>
      <nav class="nav">
        <router-link to="/dashboard" class="nav-item" @click="closeMobile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </router-link>
        <router-link to="/tasks" class="nav-item" @click="closeMobile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          Meine Aufgaben
        </router-link>
        <router-link to="/upload" class="nav-item" @click="closeMobile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          Neue Analyse
        </router-link>
        <router-link to="/analysis" class="nav-item" @click="closeMobile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Analyse Ergebnis
        </router-link>
      </nav>
      <router-link to="/profile" class="profile-link" @click="closeMobile">
        <div class="avatar" v-if="auth.user?.avatar_url">
          <img :src="auth.user.avatar_url" alt="Avatar"/>
        </div>
        <div class="avatar initials" v-else>{{ initials }}</div>
        <div class="profile-info">
          <span class="pname">{{ auth.user?.name ?? '—' }}</span>
          <span class="prolle">{{ auth.user?.rolle ?? 'Student' }}</span>
        </div>
      </router-link>
    </div>
  </div>

  <!-- DESKTOP SIDEBAR -->
  <div v-if="!isMobile" class="sidebar">
    <router-link :to="homeTarget" class="logo-link">
      <img src="/ilab.png" alt="IntelliLab" style="height:30px;width:auto;"/>
      <span>IntelliLab</span>
    </router-link>

    <nav class="nav">
      <router-link to="/dashboard" class="nav-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
      </router-link>
      <router-link to="/tasks" class="nav-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Meine Aufgaben
      </router-link>
      <router-link to="/upload" class="nav-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Neue Analyse
      </router-link>
      <router-link to="/analysis" class="nav-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        Analyse Ergebnis
      </router-link>
    </nav>

    <button class="theme-toggle sidebar-theme-toggle" @click="toggleTheme">
      <span>{{ themeStore.dark ? '☀ Light Modus' : '🌙 Night Modus' }}</span>
    </button>

    <router-link to="/profile" class="profile-link">
      <div class="avatar" v-if="auth.user?.avatar_url">
        <img :src="auth.user.avatar_url" alt="Avatar"/>
      </div>
      <div class="avatar initials" v-else>{{ initials }}</div>
      <div class="profile-info">
        <span class="pname">{{ auth.user?.name ?? '—' }}</span>
        <span class="prolle">{{ auth.user?.rolle ?? 'Student' }}</span>
      </div>
    </router-link>
  </div>
</template>

<style scoped>
/* ── DESKTOP SIDEBAR ── */
.sidebar {
  width: 220px;
  min-height: 100vh;
  height: 100%;
  background: #0f172a;
  color: white;
  display: flex;
  flex-direction: column;
  padding: 18px 0;
  flex-shrink: 0;
  position: sticky;
  top: 0;
}

.logo-link {
  display: flex; align-items: center; gap: 10px;
  padding: 0 18px 20px;
  text-decoration: none;
  border-bottom: 1px solid rgba(255,255,255,.08);
  margin-bottom: 10px;
}
.logo-link span { font-size: 1rem; font-weight: 700; color: #c4b5fd; }

.nav { flex: 1; display: flex; flex-direction: column; padding: 0 10px; gap: 2px; }

.nav-item {
  display: flex; align-items: center; gap: 11px;
  padding: 10px 11px; border-radius: 8px;
  text-decoration: none; color: #94a3b8;
  font-size: .86rem; font-weight: 500;
  transition: all .15s;
}
.nav-item:hover { background: rgba(255,255,255,.06); color: white; }
.nav-item.router-link-active { background: rgba(124,58,237,.25); color: #c4b5fd; }

.profile-link {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 14px; margin: 10px 10px 0;
  border-top: 1px solid rgba(255,255,255,.08);
  text-decoration: none; border-radius: 8px;
  transition: background .15s; cursor: pointer;
}
.profile-link:hover { background: rgba(255,255,255,.06); }

.avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: #7c3aed; color: white;
  display: flex; align-items: center; justify-content: center;
  font-size: .78rem; font-weight: 700; flex-shrink: 0; overflow: hidden;
}
.avatar img { width: 100%; height: 100%; object-fit: cover; }

.profile-info { display: flex; flex-direction: column; min-width: 0; }
.pname { font-size: .8rem; font-weight: 600; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prolle { font-size: .7rem; color: #6c7086; }

/* ── MOBILE TOPBAR ── */
.mobile-topbar {
  width: 100%;
  height: 52px;
  background: #0f172a;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  position: sticky;
  top: 0;
  z-index: 100;
  border-bottom: 1px solid rgba(255,255,255,.08);
  flex-shrink: 0;
}

.top-logo {
  display: flex; align-items: center; gap: 8px;
  text-decoration: none;
  font-size: .95rem; font-weight: 700; color: #c4b5fd;
}

.hamburger {
  background: none; border: none; color: #94a3b8;
  cursor: pointer; padding: 4px; display: flex;
}
.hamburger:hover { color: white; }

.theme-toggle {
  background: rgba(255,255,255,.08); border: none; color: #e2e8f0;
  cursor: pointer; border-radius: 8px; font-size: .95rem;
  padding: 6px 9px; display: flex; align-items: center; justify-content: center;
}
.theme-toggle:hover { background: rgba(255,255,255,.16); }
.sidebar-theme-toggle {
  margin: 4px 10px 0; padding: 9px 11px; font-size: .82rem; font-weight: 600;
  justify-content: flex-start; gap: 8px;
}

/* ── MOBILE OVERLAY MENU ── */
.mobile-overlay {
  position: fixed; inset: 52px 0 0 0;
  background: rgba(0,0,0,.5);
  z-index: 99;
}
.mobile-menu {
  width: 260px; height: 100%;
  background: #0f172a;
  display: flex; flex-direction: column;
  padding: 16px 0;
  border-right: 1px solid rgba(255,255,255,.08);
}
.mobile-menu .nav { flex: 1; padding: 0 10px; }
.mobile-menu .profile-link { margin: 10px 10px 0; }
</style>

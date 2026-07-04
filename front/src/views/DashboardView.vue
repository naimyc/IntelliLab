<script setup>
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '../components/AppLayout.vue'
import { useAuthStore } from '../stores/auth'
import { projectStore, fetchStats } from '../stores/projects'
import { setExercise } from '../stores/exercise'

const router = useRouter()
const auth = useAuthStore()
onMounted(() => fetchStats())

const greeting = computed(() => {
  const h = new Date().getHours()
  return h < 12 ? 'Guten Morgen' : h < 18 ? 'Guten Tag' : 'Guten Abend'
})

const langColor = {
  'Java':'#f97316','Python':'#3b82f6','JavaScript':'#eab308','C':'#6366f1',
  'C++':'#8b5cf6','C#':'#059669','HTML / CSS':'#ec4899','Vue.js':'#10b981',
  'React':'#0ea5e9','PHP / Laravel':'#7c3aed'
}
const lc = l => langColor[l] || '#7c3aed'

function openProject(p) {
  if (p.exercise_data) {
    setExercise(p.exercise_data)
    projectStore.currentProjectId = p.id
    router.push('/editor')
  }
}
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="welcome-row">
        <div>
          <h1>{{ greeting }}, {{ auth.user?.name?.split(' ')[0] }}! 👋</h1>
          <p class="sub">Hier findest du deine aktuellen Projekte und deinen Lernfortschritt.</p>
        </div>
        <button class="btn-new" @click="router.push('/upload')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Neue Analyse
        </button>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📁</div>
          <div><div class="stat-val">{{ projectStore.stats.total || 0 }}</div><div class="stat-lbl">Projekte gesamt</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🏆</div>
          <div><div class="stat-val">{{ projectStore.stats.done || 0 }} / {{ projectStore.stats.total || 0 }}</div><div class="stat-lbl">Abgeschlossen</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📈</div>
          <div>
            <div class="stat-val">{{ projectStore.stats.total > 0 ? Math.round((projectStore.stats.done/projectStore.stats.total)*100) : 0 }}%</div>
            <div class="stat-lbl">Fortschritt</div>
          </div>
        </div>
      </div>

      <div class="section">
        <div class="section-head">
          <h2>Meine letzten Aufgaben</h2>
          <button class="link-btn" @click="router.push('/tasks')">Alle anzeigen →</button>
        </div>
        <div v-if="projectStore.loading" class="empty">Lädt…</div>
        <div v-else-if="!projectStore.stats.recent?.length" class="empty">
          <span>Noch keine Projekte.</span>
          <button class="btn-sm" @click="router.push('/upload')">Jetzt starten →</button>
        </div>
        <div v-else class="task-list">
          <div v-for="p in projectStore.stats.recent" :key="p.id" class="task-row" @click="openProject(p)">
            <div class="task-left">
              <div class="ldot" :style="{background: lc(p.language)}"></div>
              <div>
                <div class="task-name">{{ p.name }}</div>
                <div class="task-meta">{{ p.language }}{{ p.topic ? ' · ' + p.topic : '' }}</div>
              </div>
            </div>
            <div class="task-right">
              <span class="prog-txt">{{ p.completed_tasks }} / {{ p.total_tasks }}</span>
              <span class="badge" :class="p.abgeschlossen ? 'done' : 'wip'">
                {{ p.abgeschlossen ? 'Abgeschlossen' : 'In Bearbeitung' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding: 24px 28px; max-width: 900px; width: 100%; margin: 0 auto; }

.welcome-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; gap:12px; flex-wrap:wrap; }
.welcome-row h1 { font-size: clamp(1.2rem, 3vw, 1.5rem); margin:0 0 5px; color:var(--il-text); }
.sub { color:var(--il-text-2); margin:0; font-size:.86rem; }

.btn-new { display:flex; align-items:center; gap:7px; background:#7c3aed; color:white; border:none; padding:10px 16px; border-radius:10px; font-size:.86rem; font-weight:600; cursor:pointer; white-space:nowrap; flex-shrink:0; }
.btn-new:hover { background:#6d28d9; }

.stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
.stat-card { background:var(--il-card); border-radius:12px; padding:16px 18px; display:flex; align-items:center; gap:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.stat-icon { font-size:1.4rem; }
.stat-val { font-size:1.3rem; font-weight:700; color:var(--il-text); }
.stat-lbl { font-size:.76rem; color:var(--il-text-2); margin-top:2px; }

.section { background:var(--il-card); border-radius:14px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.section-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.section-head h2 { font-size:.96rem; margin:0; color:var(--il-text); }
.link-btn { background:none; border:none; color:var(--il-accent); font-size:.84rem; cursor:pointer; font-weight:600; }

.empty { color:var(--il-text-3); text-align:center; padding:24px; display:flex; flex-direction:column; align-items:center; gap:10px; }
.btn-sm { background:#7c3aed; color:white; border:none; padding:8px 16px; border-radius:8px; font-size:.84rem; cursor:pointer; }

.task-list { display:flex; flex-direction:column; gap:8px; }
.task-row { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:var(--il-bg); border-radius:10px; cursor:pointer; transition:background .12s; border:1px solid #f1f5f9; flex-wrap:wrap; gap:8px; }
.task-row:hover { background:var(--il-bg-alt); }
.task-left { display:flex; align-items:center; gap:10px; }
.ldot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.task-name { font-size:.87rem; font-weight:600; color:var(--il-text); }
.task-meta { font-size:.73rem; color:var(--il-text-3); margin-top:2px; }
.task-right { display:flex; align-items:center; gap:10px; }
.prog-txt { font-size:.75rem; color:var(--il-text-2); }
.badge { font-size:.72rem; padding:3px 9px; border-radius:999px; font-weight:600; white-space:nowrap; }
.done { background:#dcfce7; color:#166534; }
.wip  { background:#fef3c7; color:#92400e; }

@media (max-width:600px) {
  .page { padding: 16px; }
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .task-right { width:100%; }
}
@media (max-width:400px) {
  .stats-grid { grid-template-columns: 1fr; }
}
</style>

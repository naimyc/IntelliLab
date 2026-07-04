<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '../components/AppLayout.vue'
import { projectStore, fetchProjects, deleteProject } from '../stores/projects'
import { setExercise } from '../stores/exercise'

const router = useRouter()
const filter = ref('alle')
const search = ref('')
onMounted(() => fetchProjects())

const filtered = computed(() => {
  let list = projectStore.projects
  if (filter.value === 'wip')  list = list.filter(p => !p.abgeschlossen)
  if (filter.value === 'done') list = list.filter(p =>  p.abgeschlossen)
  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    list = list.filter(p => p.name.toLowerCase().includes(q) || p.language.toLowerCase().includes(q))
  }
  return list
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
async function del(id, e) {
  e.stopPropagation()
  if (!confirm('Projekt wirklich löschen?')) return
  await deleteProject(id)
}
function fmt(dt) {
  return new Date(dt).toLocaleDateString('de', { day:'2-digit', month:'short', year:'numeric' })
}
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="head">
        <div>
          <h1>Meine Aufgaben</h1>
          <p class="sub">{{ projectStore.projects.length }} Projekt{{ projectStore.projects.length !== 1 ? 'e' : '' }} gesamt</p>
        </div>
        <button class="btn-new" @click="router.push('/upload')">+ Neue Analyse</button>
      </div>

      <div class="toolbar">
        <div class="filter-tabs">
          <button :class="{active: filter==='alle'}"  @click="filter='alle'">Alle</button>
          <button :class="{active: filter==='wip'}"   @click="filter='wip'">In Bearbeitung</button>
          <button :class="{active: filter==='done'}"  @click="filter='done'">Abgeschlossen</button>
        </div>
        <input v-model="search" class="search" placeholder="Suchen…"/>
      </div>

      <div v-if="projectStore.loading" class="empty">Lädt…</div>

      <div v-else-if="!filtered.length" class="empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c4b5fd" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        <span>Keine Projekte gefunden.</span>
        <button class="btn-sm" @click="router.push('/upload')">Jetzt starten →</button>
      </div>

      <div v-else class="task-list">
        <div v-for="p in filtered" :key="p.id" class="task-card" @click="openProject(p)">
          <div class="card-left">
            <div class="lang-badge" :style="{background:lc(p.language)+'22',color:lc(p.language),borderColor:lc(p.language)+'55'}">
              {{ p.language }}
            </div>
            <div class="card-info">
              <div class="card-name">{{ p.name }}</div>
              <div class="card-meta">{{ p.topic || '—' }} · {{ fmt(p.updated_at) }}</div>
            </div>
          </div>
          <div class="card-right">
            <div class="prog-wrap">
              <span class="prog-txt">{{ p.completed_tasks }} / {{ p.total_tasks }}</span>
              <div class="prog-bar">
                <div :style="{width: p.total_tasks ? (p.completed_tasks/p.total_tasks*100)+'%' : '0%'}"></div>
              </div>
            </div>
            <span class="badge" :class="p.abgeschlossen ? 'done' : 'wip'">
              {{ p.abgeschlossen ? 'Abgeschlossen' : 'In Bearbeitung' }}
            </span>
            <button class="del-btn" @click="del(p.id, $event)" title="Löschen">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding:24px 28px; width:100%; }
.head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; gap:12px; flex-wrap:wrap; }
.head h1 { font-size:clamp(1.2rem,3vw,1.5rem); margin:0 0 4px; color:var(--il-text); }
.sub { color:var(--il-text-2); margin:0; font-size:.86rem; }
.btn-new { background:#7c3aed; color:white; border:none; padding:10px 16px; border-radius:10px; font-size:.86rem; font-weight:600; cursor:pointer; white-space:nowrap; flex-shrink:0; }
.btn-new:hover { background:#6d28d9; }

.toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; gap:12px; flex-wrap:wrap; }
.filter-tabs { display:flex; background:var(--il-card); border:1px solid var(--il-border); border-radius:10px; padding:3px; gap:2px; }
.filter-tabs button { padding:6px 12px; border:none; background:none; border-radius:7px; font-size:.8rem; color:var(--il-text-2); cursor:pointer; font-weight:500; transition:all .12s; white-space:nowrap; }
.filter-tabs button.active { background:#7c3aed; color:white; }
.search { padding:9px 13px; border:1px solid var(--il-border); border-radius:10px; font-size:.84rem; outline:none; min-width:0; flex:1; max-width:200px; transition:border .12s; }
.search:focus { border-color:var(--il-accent); }

.empty { display:flex; flex-direction:column; align-items:center; gap:12px; padding:48px 20px; color:var(--il-text-3); text-align:center; }
.btn-sm { background:#7c3aed; color:white; border:none; padding:8px 16px; border-radius:8px; font-size:.84rem; cursor:pointer; }

.task-list { display:flex; flex-direction:column; gap:10px; }
.task-card { background:var(--il-card); border-radius:12px; padding:16px 18px; display:flex; justify-content:space-between; align-items:center; cursor:pointer; transition:all .12s; border:1px solid #f1f5f9; box-shadow:0 1px 5px rgba(0,0,0,.05); flex-wrap:wrap; gap:10px; }
.task-card:hover { border-color:#c4b5fd; box-shadow:0 4px 14px rgba(124,58,237,.1); }
.card-left { display:flex; align-items:center; gap:12px; min-width:0; }
.lang-badge { font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:6px; border:1px solid; white-space:nowrap; flex-shrink:0; }
.card-name { font-size:.88rem; font-weight:600; color:var(--il-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }
.card-meta { font-size:.72rem; color:var(--il-text-3); margin-top:2px; }
.card-right { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.prog-wrap { display:flex; flex-direction:column; align-items:flex-end; gap:3px; min-width:70px; }
.prog-txt { font-size:.7rem; color:var(--il-text-2); }
.prog-bar { width:70px; height:4px; background:var(--il-bg-alt); border-radius:999px; overflow:hidden; }
.prog-bar div { height:100%; background:#7c3aed; transition:width .3s; }
.badge { font-size:.72rem; padding:3px 9px; border-radius:999px; font-weight:600; white-space:nowrap; }
.done { background:#dcfce7; color:#166534; }
.wip  { background:#fef3c7; color:#92400e; }
.del-btn { background:none; border:none; color:#cbd5e1; cursor:pointer; padding:4px; border-radius:6px; display:flex; flex-shrink:0; }
.del-btn:hover { color:#ef4444; background:#fef2f2; }

@media (max-width:600px) {
  .page { padding:16px; }
  .search { max-width:100%; }
  .card-name { max-width:140px; }
  .card-right { width:100%; justify-content:space-between; }
}
</style>

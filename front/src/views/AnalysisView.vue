<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '../components/AppLayout.vue'
import api from '../lib/axios'

const router = useRouter()
const step = ref('select')
const languages = ref([])
const selected = ref('')
const result = ref(null)
const errMsg = ref('')
const loading = ref(false)

const langColor = {'Java':'#f97316','Python':'#3b82f6','JavaScript':'#eab308','C':'#6366f1','C++':'#8b5cf6','C#':'#059669','HTML / CSS':'#ec4899','Vue.js':'#10b981','React':'#0ea5e9','PHP / Laravel':'#7c3aed'}
const lc = l => langColor[l] || '#7c3aed'

onMounted(async () => {
  try { const {data} = await api.get('/api/analyse-ergebnis'); languages.value = data.languages || [] }
  catch(e) { console.error(e) }
})

async function selectLanguage(lang) {
  selected.value = lang; loading.value = true; errMsg.value = ''; result.value = null
  try {
    const {data} = await api.get('/api/analyse-ergebnis', {params:{language:lang}})
    if (data.error) errMsg.value = data.message
    else { result.value = data.ergebnis; step.value = 'result' }
  } catch { errMsg.value = 'Fehler beim Laden.' }
  finally { loading.value = false }
}
function goBack() { step.value='select'; result.value=null; errMsg.value='' }
</script>

<template>
  <AppLayout>
    <div class="page">
      <div v-if="step==='select'">
        <h1>Analyse Ergebnis</h1>
        <p class="sub">Wähle eine Programmiersprache um dein Lernfortschritt zu sehen.<br/>Nur Sprachen mit abgeschlossenen Projekten sind verfügbar.</p>

        <div v-if="!languages.length" class="empty">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#c4b5fd" stroke-width="1.5"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          <span>Noch keine abgeschlossenen Projekte.<br/>Schließe zuerst ein Projekt ab.</span>
          <button class="btn-sm" @click="router.push('/upload')">Jetzt starten →</button>
        </div>

        <div v-else class="lang-grid">
          <button v-for="lang in languages" :key="lang" class="lang-card" :style="{'--lc':lc(lang)}" @click="selectLanguage(lang)" :disabled="loading && selected===lang">
            <div class="ldot" :style="{background:lc(lang)}"></div>
            <span>{{ lang }}</span>
            <svg v-if="loading&&selected===lang" class="spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </div>

        <div v-if="errMsg" class="err-box">⚠️ {{ errMsg }}</div>
      </div>

      <div v-else>
        <button class="back-btn" @click="goBack">← Andere Sprache wählen</button>
        <div class="result-head">
          <div class="ldot" :style="{background:lc(selected)}"></div>
          <h1>{{ selected }} — Lernanalyse</h1>
        </div>

        <div v-if="!result" class="empty">
          <p>Noch keine Analyse vorhanden. Schließe ein Projekt ab und klicke „Prüfen" im Editor.</p>
        </div>

        <div v-else class="result-grid">
          <div class="rc learned">
            <div class="rc-icon">🎓</div>
            <div class="rc-title">Was du gelernt hast</div>
            <div class="rc-text">{{ result.gelernt || 'Noch keine Daten.' }}</div>
          </div>
          <div class="rc improve">
            <div class="rc-icon">🔧</div>
            <div class="rc-title">Wo du dich verbessern kannst</div>
            <div class="rc-text">{{ result.verbessern || 'Noch keine Daten.' }}</div>
          </div>
          <div class="rc todo">
            <div class="rc-icon">📚</div>
            <div class="rc-title">Was du noch lernen solltest</div>
            <div class="rc-text">{{ result.noch_lernen || 'Noch keine Daten.' }}</div>
          </div>
          <div class="hint-box">
            ⓘ Diese Analyse wird automatisch aktualisiert wenn du im Editor auf <strong>Prüfen</strong> klickst.
            Zuletzt: {{ result.updated_at ? new Date(result.updated_at).toLocaleDateString('de') : '—' }}
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding:24px 28px; max-width:820px; width:100%; margin:0 auto; }
h1 { font-size:clamp(1.2rem,3vw,1.5rem); margin:0 0 8px; color:var(--il-text); }
.sub { color:var(--il-text-2); margin:0 0 24px; line-height:1.6; font-size:.88rem; }

.empty { display:flex; flex-direction:column; align-items:center; gap:14px; padding:48px 20px; color:var(--il-text-3); text-align:center; line-height:1.6; }
.btn-sm { background:#7c3aed; color:white; border:none; padding:10px 20px; border-radius:10px; font-size:.86rem; cursor:pointer; }

.lang-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:10px; margin-bottom:18px; }
.lang-card { display:flex; align-items:center; gap:10px; padding:14px 16px; background:var(--il-card); border:1.5px solid var(--il-border); border-radius:12px; cursor:pointer; font-size:.88rem; font-weight:600; color:var(--il-text); transition:all .15s; justify-content:space-between; }
.lang-card:hover { border-color:var(--lc); box-shadow:0 4px 14px rgba(0,0,0,.08); }
.lang-card:disabled { opacity:.6; cursor:wait; }
.ldot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.err-box { padding:12px 16px; background:#fef9c3; border:1px solid #fde68a; border-radius:10px; color:#92400e; font-size:.84rem; }

.back-btn { background:none; border:1px solid var(--il-border); color:var(--il-text-2); padding:7px 14px; border-radius:8px; font-size:.82rem; cursor:pointer; margin-bottom:16px; }
.back-btn:hover { border-color:var(--il-accent); color:var(--il-accent); }
.result-head { display:flex; align-items:center; gap:10px; margin-bottom:20px; }

.result-grid { display:flex; flex-direction:column; gap:14px; }
.rc { background:var(--il-card); border-radius:13px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid transparent; }
.learned { border-left-color:#059669; }
.improve { border-left-color:#f59e0b; }
.todo    { border-left-color:var(--il-accent); }
.rc-icon { font-size:1.3rem; margin-bottom:8px; }
.rc-title { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--il-text-3); margin-bottom:10px; }
.rc-text { font-size:.88rem; line-height:1.7; color:var(--il-text); white-space:pre-wrap; }
.hint-box { background:var(--il-bg); border:1px solid var(--il-border); border-radius:10px; padding:12px 14px; font-size:.78rem; color:var(--il-text-2); }

@keyframes spin { to { transform:rotate(360deg); } }
.spin { animation:spin .8s linear infinite; }

@media (max-width:600px) {
  .page { padding:16px; }
  .lang-grid { grid-template-columns:1fr 1fr; }
}
</style>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '../components/AppLayout.vue'
import api from '../lib/axios'
import { setExercise } from '../stores/exercise'
import { createProject, projectStore } from '../stores/projects'

const router = useRouter()
const projektName = ref(''); const projektTyp = ref(''); const sprache = ref(''); const schritt = ref(1)
const SPRACHEN = { programmierung:['Java','Python','C','C++','C#','JavaScript'], web:['HTML / CSS','JavaScript','Vue.js','React','PHP / Laravel'] }
const aufgabenText = ref(''); const datei = ref(null); const dateiName = ref('')
const loading = ref(false); const error = ref('')

function weiterZuSprache() { if (projektName.value.trim()&&projektTyp.value) schritt.value=2 }
function weiterZuAufgabe()  { if (sprache.value) schritt.value=3 }
function zurueck() { schritt.value-- }
function waehleDatei(e) { const f=e.target.files[0]; if(!f) return; datei.value=f; dateiName.value=f.name }
function dragOver(e) { e.preventDefault() }
function dropDatei(e) { e.preventDefault(); const f=e.dataTransfer.files[0]; if(f){datei.value=f;dateiName.value=f.name} }

async function readAsText(file) {
  return new Promise(resolve => { const r=new FileReader(); r.onload=e=>resolve(e.target.result); r.onerror=()=>resolve(''); r.readAsText(file) })
}

// Liest den tatsächlichen Text aus TXT/MD/PDF/DOCX. PDF und DOCX werden
// clientseitig über echte npm-Pakete (per Vite gebündelt, per import() code-
// gesplittet) extrahiert, damit die KI echten Text und keinen Binärmüll bekommt.
async function extractFileText(file) {
  const name = file.name.toLowerCase()

  if (name.endsWith('.pdf')) {
    const [pdfjsLib, { default: workerUrl }] = await Promise.all([
      import('pdfjs-dist'),
      import('pdfjs-dist/build/pdf.worker.min.mjs?url'),
    ])
    pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl
    const buf = await file.arrayBuffer()
    const pdf = await pdfjsLib.getDocument({ data: buf }).promise
    let text = ''
    for (let i = 1; i <= pdf.numPages; i++) {
      const page = await pdf.getPage(i)
      const content = await page.getTextContent()
      text += content.items.map(it => it.str).join(' ') + '\n'
    }
    return text
  }

  if (name.endsWith('.docx')) {
    const mammoth = await import('mammoth')
    const buf = await file.arrayBuffer()
    const { value } = await mammoth.extractRawText({ arrayBuffer: buf })
    return value
  }

  return readAsText(file)
}

const aufgabeOk = computed(() => {
  if (datei.value) return true
  return aufgabenText.value.trim().length >= 30
})

async function analysieren() {
  if (!aufgabeOk.value||loading.value) return
  loading.value=true; error.value=''
  try {
    let rawText = datei.value ? await extractFileText(datei.value) : aufgabenText.value.trim()
    if (datei.value && (!rawText||rawText.trim().length<20)) {
      error.value='Die Datei konnte nicht gelesen werden oder enthält keinen erkennbaren Text.'; return
    }

    // Die KI validiert (ist es überhaupt eine Programmieraufgabe?) und teilt
    // in Teilaufgaben auf — nur Aufgabe 1 bekommt bereits Schritte, der Rest
    // wird erst on-demand im Editor generiert.
    const res = await api.post('/api/tasks/analyse', {
      project_name: projektName.value.trim(),
      language: sprache.value,
      text: rawText,
    })
    const data = res.data
    const exerciseData = { ...data, feedbackHistory: [], schritteHistory: [], treeNodes: null }
    setExercise(exerciseData)
    try {
      const proj=await createProject({name:projektName.value.trim(),language:sprache.value,topic:data.title||'',total_tasks:data.tasks?.length||1,exercise_data:exerciseData})
      if (proj) projectStore.currentProjectId=proj.id
    } catch(pe) { console.warn(pe) }
    router.push('/editor')
  } catch(err) {
    if (err.response?.status === 422) error.value = `❌ ${err.response.data?.message || 'Kein gültiger Programmierauftrag erkannt.'}`
    else if (err instanceof SyntaxError) error.value='Die KI hat kein gültiges Format zurückgegeben. Bitte erneut versuchen.'
    else error.value=err.response?.data?.message||err.message||'Unbekannter Fehler.'
  } finally { loading.value=false }
}
</script>

<template>
  <AppLayout>
    <div class="page">
      <div class="card">
        <div class="wizard">
          <div class="ws" :class="{active:schritt>=1,done:schritt>1}"><span class="wn">{{schritt>1?'✓':'1'}}</span>Name</div>
          <div class="wl" :class="{done:schritt>1}"></div>
          <div class="ws" :class="{active:schritt>=2,done:schritt>2}"><span class="wn">{{schritt>2?'✓':'2'}}</span>Sprache</div>
          <div class="wl" :class="{done:schritt>2}"></div>
          <div class="ws" :class="{active:schritt>=3}"><span class="wn">3</span>Aufgabe</div>
        </div>

        <div v-if="schritt===1">
          <h1>Neues Projekt</h1>
          <p class="sub">Gib deinem Projekt einen Namen und wähle die Kategorie.</p>
          <label>Projektname</label>
          <input v-model="projektName" placeholder="z. B. Java_Vererbung" @keydown.enter="weiterZuSprache"/>
          <label style="margin-top:16px">Kategorie</label>
          <div class="typ-grid">
            <button class="typ-btn" :class="{sel:projektTyp==='programmierung'}" @click="projektTyp='programmierung'">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
              Programmierung<span class="tsub">Java, Python…</span>
            </button>
            <button class="typ-btn" :class="{sel:projektTyp==='web'}" @click="projektTyp='web'">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              Web-Entwicklung<span class="tsub">HTML, CSS, Vue…</span>
            </button>
          </div>
          <button class="btn-p" :disabled="!projektName.trim()||!projektTyp" @click="weiterZuSprache">Weiter →</button>
        </div>

        <div v-if="schritt===2">
          <h1>Programmiersprache</h1>
          <p class="sub">Projekt: <strong>{{ projektName }}</strong></p>
          <div class="lang-grid">
            <button v-for="l in SPRACHEN[projektTyp]" :key="l" class="lang-btn" :class="{sel:sprache===l}" @click="sprache=l">{{ l }}</button>
          </div>
          <div class="btn-row"><button class="btn-g" @click="zurueck">← Zurück</button><button class="btn-p" :disabled="!sprache" @click="weiterZuAufgabe">Weiter →</button></div>
        </div>

        <div v-if="schritt===3">
          <h1>Aufgabe eingeben</h1>
          <p class="sub"><strong>{{ projektName }}</strong> · <strong>{{ sprache }}</strong></p>
          <div class="drop-zone" :class="{'has-file':dateiName}" @dragover="dragOver" @drop="dropDatei" @click="$refs.fi.click()">
            <input ref="fi" type="file" accept=".txt,.md,.docx,.pdf" hidden @change="waehleDatei"/>
            <svg v-if="!dateiName" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span v-if="!dateiName">TXT, Markdown, PDF oder Word (.docx) hier ablegen</span>
            <span v-else class="fc">📄 {{ dateiName }} <button @click.stop="datei=null;dateiName=''">×</button></span>
          </div>
          <div class="oder">ODER</div>
          <label>Aufgabentext direkt eingeben</label>
          <textarea v-model="aufgabenText" :disabled="!!datei" class="ta" placeholder="Gib hier den Aufgabentext ein (mind. 30 Zeichen)…" @keydown.ctrl.enter="analysieren"></textarea>
          <div v-if="aufgabenText.trim().length>0&&aufgabenText.trim().length<30" class="warn">⚠️ Text zu kurz (mind. 30 Zeichen).</div>
          <p v-if="error" class="err">{{ error }}</p>
          <div class="btn-row">
            <button class="btn-g" @click="zurueck" :disabled="loading">← Zurück</button>
            <button class="btn-p" :disabled="loading||!aufgabeOk" @click="analysieren">
              <span v-if="loading" class="spin"></span>
              {{ loading ? 'Analysiere…' : 'Analysieren →' }}
            </button>
          </div>
          <p class="hint">Strg+Enter zum Starten</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.page { padding:24px 20px; display:flex; justify-content:center; }
.card { width:100%; max-width:580px; background:var(--il-card); padding:32px; border-radius:18px; box-shadow:0 8px 28px rgba(0,0,0,.08); }
h1 { font-size:1.3rem; margin:0 0 5px; color:var(--il-text); }
.sub { color:var(--il-text-2); margin:0 0 20px; font-size:.88rem; }
.wizard { display:flex; align-items:center; margin-bottom:24px; }
.ws { display:flex; align-items:center; gap:6px; font-size:.78rem; color:var(--il-text-3); font-weight:500; }
.ws.active { color:var(--il-accent); } .ws.done { color:#059669; }
.wn { width:23px; height:23px; border-radius:50%; background:var(--il-bg-alt); color:var(--il-text-3); font-size:.7rem; font-weight:700; display:grid; place-items:center; flex-shrink:0; }
.ws.active .wn { background:#7c3aed; color:white; } .ws.done .wn { background:#059669; color:white; }
.wl { flex:1; height:2px; background:#e2e8f0; margin:0 7px; } .wl.done { background:#059669; }
label { display:block; font-size:.8rem; font-weight:600; color:var(--il-text); margin-bottom:5px; }
input,select { width:100%; padding:10px 12px; border:1.5px solid var(--il-border); border-radius:9px; margin-bottom:14px; font-size:.88rem; box-sizing:border-box; outline:none; transition:border .15s; }
input:focus,select:focus { border-color:var(--il-accent); }
.typ-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:20px; }
.typ-btn { display:flex; flex-direction:column; align-items:center; gap:5px; padding:16px 10px; border:2px solid #e2e8f0; border-radius:12px; background:var(--il-card); cursor:pointer; font-size:.86rem; font-weight:600; color:var(--il-text); transition:all .15s; }
.typ-btn.sel,.typ-btn:hover { border-color:var(--il-accent); background:var(--il-accent-soft); color:#6d28d9; }
.tsub { font-size:.68rem; font-weight:400; color:var(--il-text-3); }
.lang-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:22px; }
.lang-btn { padding:11px 6px; border:2px solid #e2e8f0; border-radius:9px; background:var(--il-card); cursor:pointer; font-size:.82rem; font-weight:600; color:var(--il-text); transition:all .15s; }
.lang-btn.sel,.lang-btn:hover { border-color:var(--il-accent); background:var(--il-accent-soft); color:#6d28d9; }
.drop-zone { border:2px dashed #c4b5fd; border-radius:12px; padding:24px; text-align:center; cursor:pointer; color:var(--il-accent); font-size:.86rem; display:flex; flex-direction:column; align-items:center; gap:6px; margin-bottom:4px; transition:background .15s; }
.drop-zone:hover,.drop-zone.has-file { background:#faf5ff; }
.drop-zone.has-file { border-color:#86efac; color:#15803d; background:#f0fdf4; }
.fc { display:flex; align-items:center; gap:7px; font-weight:600; }
.fc button { background:none; border:none; cursor:pointer; color:#ef4444; font-size:16px; }
.oder { text-align:center; margin:12px 0; color:var(--il-text-3); font-size:.82rem; }
.ta { width:100%; height:140px; padding:11px 12px; border:1.5px solid var(--il-border); border-radius:9px; resize:vertical; font-family:inherit; font-size:.86rem; outline:none; box-sizing:border-box; transition:border .15s; }
.ta:focus { border-color:var(--il-accent); } .ta:disabled { background:var(--il-bg); cursor:not-allowed; }
.warn { margin-top:5px; font-size:.76rem; color:#b45309; background:#fef9c3; border:1px solid #fde68a; padding:7px 11px; border-radius:7px; }
.err { color:#b91c1c; background:#fef2f2; border:1px solid #fca5a5; padding:10px 12px; border-radius:9px; font-size:.82rem; margin-top:12px; }
.btn-row { display:flex; gap:10px; margin-top:18px; }
.btn-p { flex:1; padding:12px; border:none; border-radius:11px; background:#7c3aed; color:white; font-size:.9rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; }
.btn-p:hover:not(:disabled) { background:#6d28d9; } .btn-p:disabled { opacity:.55; cursor:not-allowed; }
.btn-g { padding:12px 16px; border:1.5px solid var(--il-border); border-radius:11px; background:var(--il-card); color:var(--il-text-2); font-size:.86rem; cursor:pointer; }
.btn-g:hover:not(:disabled) { border-color:var(--il-accent); color:var(--il-accent); } .btn-g:disabled { opacity:.5; cursor:not-allowed; }
.hint { text-align:center; color:var(--il-text-3); font-size:.74rem; margin-top:7px; }
.spin { width:14px; height:14px; border:2px solid rgba(255,255,255,.4); border-top-color:white; border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
@media (max-width:480px) { .typ-grid { grid-template-columns:1fr; } .lang-grid { grid-template-columns:1fr 1fr; } }
</style>

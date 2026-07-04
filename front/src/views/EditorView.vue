<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted, watch, shallowRef } from 'vue'
import { useRouter } from 'vue-router'
import Sidebar from '../components/Sidebar.vue'
import api from '../lib/axios'
import { exerciseStore, setExercise } from '../stores/exercise'
import { projectStore, updateProject, updateAnalyse } from '../stores/projects'
import { themeStore, toggleTheme } from '../stores/theme'

const router = useRouter()
const isMobile = ref(window.innerWidth < 768)
function onResize() { isMobile.value = window.innerWidth < 768 }
onMounted(() => { window.addEventListener('resize', onResize) })
onUnmounted(() => { window.removeEventListener('resize', onResize); cmView.value?.destroy() })

onMounted(() => {
  if (!exerciseStore.current) { router.replace('/upload'); return }
  if (exerciseStore.current?.treeNodes?.length) restoreTree()
  else buildInitialTree()
  if (!isMobile.value) nextTick(() => initCodeMirror())
})

const ex = computed(() => exerciseStore.current)
// Der Night/Light-Toggle steuert den globalen Theme-Store (wirkt auf die
// gesamte App), nicht nur den Editor — CodeMirror folgt hier nur mit.
watch(() => themeStore.dark, () => updateCmTheme())

// Mobile panel: 'files' | 'editor' | 'schritte' | 'feedback'
const mobilePanel = ref('schritte')

// ── Aufgabe ────────────────────────────────────────────────────────────────
const taskIndex  = computed(() => ex.value?.current_task_index ?? 0)
const curTask    = computed(() => ex.value?.tasks?.[taskIndex.value] || null)
const totalTasks = computed(() => ex.value?.tasks?.length ?? 0)
const projName   = computed(() => ex.value?.project_name || 'Projekt')
const lang       = computed(() => ex.value?.language || 'Java')
const rightPanel = ref('schritte')

// Storico feedback e schritte
const feedbackHistory = ref(ex.value?.feedbackHistory || [])   // [{taskIndex, text, date}]
const schritteHistory = ref(ex.value?.schritteHistory || [])   // salviamo gli steps dei task precedenti
const viewingHistory  = ref(false)
const historyIndex    = ref(null)   // quale task history stiamo vedendo

// ── File Tree ──────────────────────────────────────────────────────────────
const treeNodes   = ref([])
const expandedIds = ref(new Set())
const activeNodeId = ref('')
const tabs        = ref([])
const activeTabId = ref('')
const activeTab   = computed(() => tabs.value.find(t => t.id === activeTabId.value) || null)
let nodeSeq = 0
const uid = () => 'n' + (++nodeSeq)

function buildInitialTree() {
  const l = lang.value
  // Der Nutzer baut Klassen/Struktur selbst — die KI liefert kein Scaffolding.
  // Nur ein minimales, statisches Grundgerüst zum Loslegen (wie bei "Neue Klasse").
  const starter = l==='Java'
    ? 'public class Main {\n    public static void main(String[] args) {\n\n    }\n}\n'
    : ''
  const proj = { id: uid(), name: projName.value, type:'project', content:'', parentId:null }
  let main
  if (l === 'Java') {
    const src = { id:uid(), name:'src', type:'src', content:'', parentId:proj.id }
    const pkg = { id:uid(), name:'default', type:'package', content:'', parentId:src.id }
    main = { id:uid(), name:'Main.java', type:'class', content:starter, parentId:pkg.id }
    treeNodes.value = [proj,src,pkg,main]
    expandedIds.value = new Set([proj.id,src.id,pkg.id])
  } else if (l === 'Python') {
    main = { id:uid(), name:'main.py', type:'file', content:starter, parentId:proj.id }
    treeNodes.value = [proj,main]; expandedIds.value = new Set([proj.id])
  } else {
    const ext = l==='Vue.js'?'.vue':l==='React'?'.jsx':l==='PHP / Laravel'?'.php':'.js'
    main = { id:uid(), name:'index'+ext, type:'file', content:starter, parentId:proj.id }
    treeNodes.value = [proj,main]; expandedIds.value = new Set([proj.id])
  }
  openTab(main)
}

// Stellt den kompletten Dateibaum (inkl. selbst angelegter Klassen/Pakete)
// aus exercise_data wieder her, statt ihn bei jedem Editor-Besuch neu
// aufzubauen — sonst gehen zusätzlich erstellte Dateien verloren.
function restoreTree() {
  treeNodes.value = ex.value.treeNodes.map(n => ({ ...n }))
  expandedIds.value = new Set(ex.value.expandedIdsArr || [])
  nodeSeq = ex.value.nodeSeq || treeNodes.value.length

  const openIds = ex.value.openTabs || []
  tabs.value = openIds
    .map(id => treeNodes.value.find(n => n.id === id))
    .filter(Boolean)
    .map(n => ({ id: n.id, name: n.name, content: n.content || '' }))

  if (!tabs.value.length) {
    const firstFile = treeNodes.value.find(n => !isFolder(n))
    if (firstFile) tabs.value = [{ id: firstFile.id, name: firstFile.name, content: firstFile.content || '' }]
  }

  activeTabId.value = tabs.value.find(t => t.id === ex.value.activeTabId)
    ? ex.value.activeTabId
    : (tabs.value[0]?.id || '')
  activeNodeId.value = activeTabId.value
  nextTick(() => setCmContent(activeTab.value?.content || ''))
}

const childrenOf = pid => treeNodes.value.filter(n => n.parentId === pid)
const isFolder   = n => ['project','src','package'].includes(n.type)
const nodeIcon   = n => {
  if (n.type==='project') return expandedIds.value.has(n.id)?'📂':'📁'
  if (n.type==='src') return '📂'
  if (n.type==='package') return '📦'
  if (n.type==='class') return '🟦'
  if (n.type==='interface') return '🔷'
  return '📄'
}

function clickNode(node) {
  if (isFolder(node)) {
    const s = new Set(expandedIds.value)
    s.has(node.id)?s.delete(node.id):s.add(node.id)
    expandedIds.value = s
  } else {
    openTab(node); activeNodeId.value = node.id
    if (isMobile.value) mobilePanel.value = 'editor'
  }
}

function openTab(node) {
  activeNodeId.value = node.id
  if (!tabs.value.find(t => t.id === node.id))
    tabs.value.push({ id:node.id, name:node.name, content:node.content||'' })
  activeTabId.value = node.id
  nextTick(() => setCmContent(node.content || ''))
}
function closeTab(id) {
  const i = tabs.value.findIndex(t => t.id===id); if (i===-1) return
  tabs.value.splice(i,1)
  const next = tabs.value[Math.max(0,i-1)]
  activeTabId.value = next?.id||''
  nextTick(() => setCmContent(next?.content||''))
}
function switchTab(id) {
  activeTabId.value = id
  const t = tabs.value.find(t=>t.id===id)
  nextTick(() => setCmContent(t?.content||''))
}
watch(()=>activeTab.value?.content, val => {
  if (!activeTabId.value) return
  const node = treeNodes.value.find(n=>n.id===activeTabId.value)
  if (node) node.content = val
  autoSave()
})

// ── AutoSave ───────────────────────────────────────────────────────────────
// Speichert nicht nur den Tab-Inhalt, sondern den kompletten Dateibaum
// (inkl. selbst angelegter Klassen/Pakete), damit beim erneuten Öffnen des
// Editors nichts verloren geht (siehe restoreTree()).
let saveTimer = null
function autoSave() {
  clearTimeout(saveTimer)
  saveTimer = setTimeout(() => {
    if (!ex.value) return
    const snapshot = {
      ...ex.value,
      treeNodes: treeNodes.value,
      expandedIdsArr: Array.from(expandedIds.value),
      nodeSeq,
      openTabs: tabs.value.map(t => t.id),
      activeTabId: activeTabId.value,
    }
    setExercise(snapshot)
    if (projectStore.currentProjectId) {
      updateProject(projectStore.currentProjectId, { exercise_data: snapshot }).catch(()=>{})
    }
  }, 2000)
}

// ── Neue Datei Modal ────────────────────────────────────────────────────────
const showNewModal  = ref(false)
const newType       = ref('Klasse')
const newName       = ref('')
const newParentId   = ref('')
const newNameInput  = ref(null)
const showShortcuts = ref(false)

function openNewModal(type, parentId) {
  newType.value=type; newParentId.value=parentId; newName.value=''
  showNewModal.value=true; nextTick(()=>newNameInput.value?.focus())
}
function confirmNew() {
  if (!newName.value.trim()) return
  const raw=newName.value.trim(); let name,type,content
  if (newType.value==='Klasse') {
    name=raw.endsWith('.java')?raw:raw+'.java'; type='class'
    content=`public class ${name.replace('.java','')} {\n\n}\n`
  } else if (newType.value==='Interface') {
    name=raw.endsWith('.java')?raw:raw+'.java'; type='interface'
    content=`public interface ${raw.replace('.java','')} {\n\n}\n`
  } else if (newType.value==='Paket') {
    name=raw; type='package'; content=''
  } else {
    const l=lang.value
    name=raw.includes('.')?raw:raw+(l==='Python'?'.py':l==='Vue.js'?'.vue':'.js')
    type='file'; content=''
  }
  const pId = newParentId.value || treeNodes.value[0]?.id
  const node = { id:uid(), name, type, content, parentId:pId }
  treeNodes.value.push(node)
  const s=new Set(expandedIds.value); s.add(pId); expandedIds.value=s
  showNewModal.value=false
  if (type!=='package') openTab(node)
  autoSave()
}

// ── Lösung ─────────────────────────────────────────────────────────────────
// Die Musterlösung wird NICHT beim Projektstart erzeugt (die KI generiert dort
// nur Aufgaben/Schritte), sondern erst on-demand bei Klick auf "Lösung" —
// und dann pro Aufgabe zwischengespeichert, damit sie nicht jedes Mal neu
// generiert werden muss.
const loesungCache   = ref({})
const loesungLoading = ref(false)

async function loesungEinfuegen() {
  const task=curTask.value; if (!task || loesungLoading.value) return
  const idx = taskIndex.value

  if (!loesungCache.value[idx]) {
    loesungLoading.value = true
    try {
      const res = await api.post('/api/tasks/solution', {
        language: lang.value, task_title: task.task_title, question: task.question, steps: task.steps||[],
      })
      loesungCache.value = { ...loesungCache.value, [idx]: res.data?.code || '// Keine Lösung' }
    } catch(e) {
      logLine('Fehler beim Generieren der Lösung: '+(e.response?.data?.message||e.message),'error')
      loesungLoading.value = false
      return
    }
    loesungLoading.value = false
  }

  const l=lang.value; const ext=l==='Java'?'.java':l==='Python'?'.py':l==='Vue.js'?'.vue':'.js'
  const lName=`${projName.value}_loesung_${idx+1}${ext}`
  treeNodes.value = treeNodes.value.filter(n=>!n.id.startsWith('loes_'))
  const lProj={id:'loes_proj',name:`${projName.value}_loesung`,type:'project',content:'',parentId:null}
  const code = loesungCache.value[idx]
  let lFile
  if (l==='Java') {
    const lSrc={id:'loes_src',name:'src',type:'src',content:'',parentId:'loes_proj'}
    const lPkg={id:'loes_pkg',name:'loesung',type:'package',content:'',parentId:'loes_src'}
    lFile={id:'loes_file',name:lName,type:'class',content:code,parentId:'loes_pkg'}
    treeNodes.value.push(lProj,lSrc,lPkg,lFile)
    const s=new Set(expandedIds.value);['loes_proj','loes_src','loes_pkg'].forEach(id=>s.add(id));expandedIds.value=s
  } else {
    lFile={id:'loes_file',name:lName,type:'file',content:code,parentId:'loes_proj'}
    treeNodes.value.push(lProj,lFile)
    const s=new Set(expandedIds.value);s.add('loes_proj');expandedIds.value=s
  }
  openTab(lFile)
}

// ── KI Source-Generierung (Methoden, Konstruktor, Getter/Setter) ────────────
const showSourceMenu = ref(false)
const kiGenLoading   = ref(false)

async function kiGeneriere(type) {
  if (kiGenLoading.value) return
  kiGenLoading.value = true; showSourceMenu.value = false
  const code = activeTab.value?.content || ''
  const prompts = {
    getter:  `Analysiere diesen ${lang.value}-Code und generiere für alle privaten Felder Getter- und Setter-Methoden. Gib NUR den Methoden-Code zurück, ohne Erklärung, ohne Klassen-Wrapper:\n\n${code}`,
    ctor:    `Generiere einen passenden Konstruktor für diesen ${lang.value}-Code. Gib NUR den Konstruktor zurück:\n\n${code}`,
    tostr:   `Generiere eine toString()-Methode für diesen ${lang.value}-Code. Gib NUR die Methode zurück:\n\n${code}`,
    equals:  `Generiere equals() und hashCode() Methoden für diesen ${lang.value}-Code. Gib NUR die Methoden zurück:\n\n${code}`,
    method:  `Generiere eine sinnvolle neue leere Methode für diesen ${lang.value}-Code passend zum Kontext. Gib NUR die Methode zurück:\n\n${code}`,
  }
  try {
    const res = await api.post('/api/gpt', {
      prompt: prompts[type],
      system: `Du bist ein ${lang.value}-Code-Generator. Antworte NUR mit dem angeforderten Code, keine Markdown-Backticks, keine Erklärungen.`,
      temperature: 0.2, max_tokens: 500
    })
    const result = res.data?.content || ''
    const val = activeTab.value.content
    const lb = val.lastIndexOf('}')
    activeTab.value.content = lb !== -1
      ? val.slice(0,lb) + '\n\n  ' + result.trim().replace(/\n/g,'\n  ') + '\n' + val.slice(lb)
      : val + '\n\n' + result
    logLine(`✓ ${type==='getter'?'Getter/Setter':type==='ctor'?'Konstruktor':type==='tostr'?'toString()':type==='equals'?'equals()/hashCode()':'Methode'} generiert.`,'success')
  } catch(e) { logLine('KI-Fehler: '+(e.response?.data?.error||e.message),'error') }
  finally { kiGenLoading.value = false }
}

// ── Schritte ───────────────────────────────────────────────────────────────
const doneSteps = ref([])
watch(taskIndex, () => { doneSteps.value = [] })
function toggleStep(i) {
  doneSteps.value = doneSteps.value.includes(i)?doneSteps.value.filter(x=>x!==i):[...doneSteps.value,i]
  autoSave()
}
const progress = computed(() => {
  const t=curTask.value?.steps?.length||0; return t?Math.round((doneSteps.value.length/t)*100):0
})

// Storico schritte del task precedente
const prevTaskSteps = computed(() => {
  if (taskIndex.value === 0) return null
  return ex.value?.tasks?.[taskIndex.value-1]?.steps || null
})

// ── CodeMirror ─────────────────────────────────────────────────────────────
const cmContainer = shallowRef(null)
const cmView      = shallowRef(null)
let CM = null

async function loadCM() {
  if (CM) return CM
  const [
    {EditorView,keymap,lineNumbers,highlightActiveLine,highlightActiveLineGutter,drawSelection},
    {defaultKeymap,history,historyKeymap,indentWithTab},
    {EditorState},
    {syntaxHighlighting,defaultHighlightStyle,bracketMatching,foldGutter,indentOnInput},
    {autocompletion,completionKeymap,closeBrackets,closeBracketsKeymap},
    {lintGutter,lintKeymap,setDiagnostics},
    {javascript},{java},{python},{html},{css},{oneDark},
  ] = await Promise.all([
    import('https://esm.sh/@codemirror/view@6'),
    import('https://esm.sh/@codemirror/commands@6'),
    import('https://esm.sh/@codemirror/state@6'),
    import('https://esm.sh/@codemirror/language@6'),
    import('https://esm.sh/@codemirror/autocomplete@6'),
    import('https://esm.sh/@codemirror/lint@6'),
    import('https://esm.sh/@codemirror/lang-javascript@6'),
    import('https://esm.sh/@codemirror/lang-java@6'),
    import('https://esm.sh/@codemirror/lang-python@6'),
    import('https://esm.sh/@codemirror/lang-html@6'),
    import('https://esm.sh/@codemirror/lang-css@6'),
    import('https://esm.sh/@codemirror/theme-one-dark@6'),
  ])
  CM={EditorView,keymap,lineNumbers,highlightActiveLine,highlightActiveLineGutter,drawSelection,
    defaultKeymap,history,historyKeymap,indentWithTab,EditorState,
    syntaxHighlighting,defaultHighlightStyle,bracketMatching,foldGutter,indentOnInput,
    autocompletion,completionKeymap,closeBrackets,closeBracketsKeymap,
    lintGutter,lintKeymap,setDiagnostics,javascript,java,python,html,css,oneDark}
  return CM
}

function getLangExt() {
  if (!CM) return []
  const l=lang.value
  if (l==='Java') return [CM.java()]
  if (l==='Python') return [CM.python()]
  if (l==='HTML / CSS') return [CM.html(),CM.css()]
  if (l==='Vue.js'||l==='React') return [CM.javascript({jsx:true})]
  return [CM.javascript()]
}

function buildExtensions(content) {
  const {EditorView,EditorState,keymap,lineNumbers,highlightActiveLine,highlightActiveLineGutter,
    drawSelection,bracketMatching,foldGutter,indentOnInput,autocompletion,completionKeymap,
    closeBrackets,closeBracketsKeymap,history,historyKeymap,defaultKeymap,indentWithTab,
    lintGutter,lintKeymap,oneDark,setDiagnostics,syntaxHighlighting,defaultHighlightStyle} = CM

  const updateListener = EditorView.updateListener.of(upd => {
    if (!upd.docChanged) return
    const c = upd.state.doc.toString()
    if (activeTab.value) { activeTab.value.content=c; const n=treeNodes.value.find(n=>n.id===activeTabId.value); if(n) n.content=c }
    scheduleLint(c)
  })

  return [
    lineNumbers(), highlightActiveLine(), highlightActiveLineGutter(),
    drawSelection(), bracketMatching(), foldGutter(), indentOnInput(), history(),
    autocompletion(), closeBrackets(), lintGutter(),
    keymap.of([...defaultKeymap,...historyKeymap,...completionKeymap,...closeBracketsKeymap,...lintKeymap,indentWithTab]),
    ...getLangExt(),
    themeStore.dark ? oneDark : syntaxHighlighting(defaultHighlightStyle,{fallback:true}),
    updateListener,
    EditorView.theme({'&':{height:'100%',fontSize:'13.5px'},'.cm-scroller':{fontFamily:"'Fira Code','JetBrains Mono','Consolas',monospace",overflow:'auto'},'.cm-content':{padding:'10px 0'}}),
  ]
}

async function initCodeMirror() {
  await loadCM()
  if (!cmContainer.value) return
  const state = CM.EditorState.create({ doc: activeTab.value?.content||'', extensions: buildExtensions() })
  cmView.value = new CM.EditorView({ state, parent: cmContainer.value })
}

function setCmContent(content) {
  if (!cmView.value) return
  const cur = cmView.value.state.doc.toString()
  if (cur===content) return
  cmView.value.dispatch({ changes:{from:0,to:cur.length,insert:content} })
}

function updateCmTheme() {
  if (!cmView.value||!CM) return
  const content = cmView.value.state.doc.toString()
  cmView.value.destroy()
  const state = CM.EditorState.create({ doc:content, extensions:buildExtensions() })
  cmView.value = new CM.EditorView({ state, parent: cmContainer.value })
}

// ── Lint ───────────────────────────────────────────────────────────────────
let lintTimer=null
function scheduleLint(code) { clearTimeout(lintTimer); lintTimer=setTimeout(()=>runLint(code),800) }
function runLint(code) {
  if (!cmView.value||!CM) return
  const diags=[]; let ob=0,op=0
  const lines=code.split('\n')
  lines.forEach((line,ln)=>{
    let pos=code.split('\n').slice(0,ln).join('\n').length+(ln>0?1:0)
    for(let ch=0;ch<line.length;ch++){
      const c=line[ch]
      if(c==='{')ob++;else if(c==='}'){if(--ob<0){diags.push({from:pos+ch,to:pos+ch+1,severity:'error',message:'Unerwartete "}" — fehlende öffnende Klammer'});ob=0}}
      if(c==='(')op++;else if(c===')'){if(--op<0){diags.push({from:pos+ch,to:pos+ch+1,severity:'error',message:'Unerwartete ")" — fehlende öffnende Klammer'});op=0}}
    }
  })
  if(ob>0) diags.push({from:Math.max(0,code.length-1),to:code.length,severity:'warning',message:`${ob} nicht geschlossene geschweifte Klammer(n)`})
  cmView.value.dispatch(CM.setDiagnostics(cmView.value.state,diags))
}

// ── Konsole ────────────────────────────────────────────────────────────────
const consoleLogs=ref([]);const konsoleRef=ref(null)
const isRunning=ref(false);const hasRun=ref(false);const testResults=ref([])
function logLine(msg,type='info'){
  consoleLogs.value.push({msg,type})
  nextTick(()=>{if(konsoleRef.value) konsoleRef.value.scrollTop=konsoleRef.value.scrollHeight})
}
function clearAll(){consoleLogs.value=[];testResults.value=[];hasRun.value=false}
function allCode(){return tabs.value.map(t=>`// === ${t.name} ===\n${t.content}`).join('\n\n')}

function runJs(src){
  const logs=[];const push=(...a)=>logs.push(a.map(x=>typeof x==='object'?JSON.stringify(x):String(x)).join(' '))
  try{new Function('console',src)({log:push,error:push,warn:push,info:push});return{ok:true,output:logs.join('\n')}}
  catch(e){return{ok:false,output:logs.join('\n'),error:String(e)}}
}

async function runCode(){
  if(isRunning.value || isMobile.value) return
  isRunning.value=true;clearAll();hasRun.value=true
  const exp=curTask.value?.test_cases?.map(tc=>String(tc.expected_output??'').trim())||[]
  if(lang.value==='JavaScript'){
    const r=runJs(activeTab.value?.content||'')
    logLine(r.error?`${r.output?r.output+'\n':''}Fehler: ${r.error}`:r.output||'(keine Ausgabe)',r.error?'error':'success')
    testResults.value=curTask.value?.test_cases?.map((tc,i)=>({...tc,pass:r.output?.includes(exp[i])})) ||[]
    isRunning.value=false;return
  }
  try{
    const res=await api.post('/api/gpt',{
      prompt:`Führe diesen ${lang.value}-Code aus. NUR Konsolenausgabe ohne Kommentar. Bei Fehler: "FEHLER: <Meldung>".\n\`\`\`\n${allCode()}\n\`\`\``,
      system:`Du bist ein ${lang.value}-Interpreter. Antworte NUR mit der Programmausgabe.`,
      temperature:0.1,max_tokens:500
    })
    const out=res.data?.content||''
    logLine(out,out.startsWith('FEHLER')?'error':'success')
    testResults.value=curTask.value?.test_cases?.map((tc,i)=>({...tc,pass:out.includes(exp[i])}))||[]
  }catch(e){logLine('Verbindungsfehler: '+(e.response?.data?.error||e.message),'error')}
  finally{isRunning.value=false}
}

// ── Prüfen ─────────────────────────────────────────────────────────────────
const isPruefen=ref(false);const feedback=ref('');const pruefenOk=ref(false)

async function pruefen(){
  if(isPruefen.value || isMobile.value) return
  isPruefen.value=true;feedback.value='';pruefenOk.value=false;rightPanel.value='feedback'
  logLine('Code wird überprüft…','info')
  const task=curTask.value
  try{
    const res=await api.post('/api/tasks/check',{
      language: lang.value,
      task_title: task?.task_title||'',
      question: task?.question||'',
      steps: task?.steps||[],
      code: allCode(),
    })
    const { bestanden, feedback: fbText, gelernt, verbessern, noch_lernen } = res.data
    feedback.value = fbText || 'Kein Feedback.'
    pruefenOk.value = !!bestanden

    // Feedback in history speichern
    const histEntry={taskIndex:taskIndex.value,text:feedback.value,date:new Date().toLocaleString('de')}
    feedbackHistory.value.push(histEntry)

    // Schritte des aktuellen Tasks in history speichern
    if (!schritteHistory.value[taskIndex.value]) {
      schritteHistory.value[taskIndex.value] = task?.steps || []
    }

    // Wenn abgeschlossen → Projekt updaten + Analyse Ergebnis
    if (pruefenOk.value) {
      const newCompleted = Math.min((ex.value.current_task_index||0)+1, totalTasks.value)
      const isAllDone = newCompleted >= totalTasks.value

      if (projectStore.currentProjectId) {
        try {
          await updateAnalyse(projectStore.currentProjectId,{
            ki_feedback:feedback.value, gelernt, verbessern, noch_lernen,
          })
        } catch(ae){console.warn('Analyse update:',ae)}

        await updateProject(projectStore.currentProjectId,{
          completed_tasks: newCompleted,
          abgeschlossen: isAllDone,
          ki_feedback: feedback.value,
        })
      }

      setExercise({...ex.value,feedbackHistory:feedbackHistory.value,schritteHistory:schritteHistory.value})
    }

    logLine(pruefenOk.value?'✓ Aufgabe abgeschlossen!':'Prüfung abgeschlossen.', pruefenOk.value?'success':'info')
  }catch(e){logLine('Fehler: '+(e.response?.data?.message||e.message),'error')}
  finally{isPruefen.value=false}
}

// ── Nächste Aufgabe ─────────────────────────────────────────────────────────
// Die Schritte der nächsten Aufgabe werden nicht im Voraus generiert, sondern
// erst hier on-demand geholt (steps_generated===false), bevor gewechselt wird.
const naechsteLoading = ref(false)

async function naechsteAufgabe(){
  if(taskIndex.value>=totalTasks.value-1 || naechsteLoading.value) return
  const newIndex=taskIndex.value+1
  const tasksArr=[...ex.value.tasks]
  const nextTask=tasksArr[newIndex]

  if (!nextTask.steps_generated) {
    naechsteLoading.value=true
    try {
      const res=await api.post('/api/tasks/steps',{ question: nextTask.question, language: lang.value })
      tasksArr[newIndex]={ ...nextTask, steps: res.data?.steps||[], steps_generated:true }
    } catch(e) {
      logLine('Fehler beim Generieren der Schritte: '+(e.response?.data?.message||e.message),'error')
      naechsteLoading.value=false
      return
    }
    naechsteLoading.value=false
  }

  setExercise({...ex.value,tasks:tasksArr,current_task_index:newIndex,feedbackHistory:feedbackHistory.value,schritteHistory:schritteHistory.value})
  feedback.value='';pruefenOk.value=false;clearAll();doneSteps.value=[];rightPanel.value='schritte'
  if(isMobile.value) mobilePanel.value='schritte'
}

// ── Globale Shortcuts ────────────────────────────────────────────────────────
const saveFlash=ref(false)
function speichern() {
  saveFlash.value = true
  autoSave()
  setTimeout(() => saveFlash.value = false, 1500)
}

function wechselPanel(panel) {
  rightPanel.value = panel
  if (isMobile.value) mobilePanel.value = panel
}

function globalKey(e){
  if((e.ctrlKey||e.metaKey)&&e.key==='r'){e.preventDefault();runCode()}
  if((e.ctrlKey||e.metaKey)&&e.shiftKey&&e.key==='P'){e.preventDefault();pruefen()}
  if((e.ctrlKey||e.metaKey)&&e.key==='n'){
    e.preventDefault()
    const pkg=treeNodes.value.find(n=>n.type==='package')
    openNewModal('Klasse',pkg?.id||treeNodes.value[0]?.id)
  }
  if((e.ctrlKey||e.metaKey)&&e.key==='s'){
    e.preventDefault();saveFlash.value=true;autoSave();setTimeout(()=>saveFlash.value=false,1500)
  }
  if((e.ctrlKey||e.metaKey)&&e.key==='w'){e.preventDefault();if(activeTabId.value) closeTab(activeTabId.value)}
  if(e.key==='F1'){e.preventDefault();showShortcuts.value=!showShortcuts.value}
}
onMounted(()=>window.addEventListener('keydown',globalKey))
onUnmounted(()=>window.removeEventListener('keydown',globalKey))
</script>

<template>
  <div v-if="ex" class="il-root" :class="{night:themeStore.dark}">

    <!-- ═══ MENU BAR ═══ -->
    <nav class="menubar">
      <div class="mb-left">
        <router-link to="/dashboard" class="logo-link">
          <svg width="26" height="26" viewBox="0 0 100 100" fill="none">
            <circle cx="50" cy="50" r="50" fill="#7c3aed"/>
            <path d="M20 50 C20 28 35 18 50 18 C65 18 80 28 80 50 C80 72 65 82 50 82 C35 82 20 72 20 50Z" stroke="#a78bfa" stroke-width="3" fill="none"/>
            <path d="M50 18 C50 18 35 35 35 50 C35 65 50 82 50 82 C50 82 65 65 65 50 C65 35 50 18 50 18Z" stroke="#c4b5fd" stroke-width="2" fill="none"/>
            <path d="M18 50 L82 50" stroke="#a78bfa" stroke-width="2"/>
            <circle cx="50" cy="50" r="6" fill="#c4b5fd"/>
          </svg>
          <span class="logo-txt">IntelliLab</span>
        </router-link>
        <div class="mb-sep"></div>

        <!-- Desktop menus -->
        <template v-if="!isMobile">
          <div class="mi" tabindex="0">Datei<div class="dd">
            <button @click="openNewModal('Klasse',treeNodes.find(n=>n.type==='package')?.id||treeNodes[0]?.id)">Neue Klasse <kbd>Strg+N</kbd></button>
            <button @click="openNewModal('Interface',treeNodes.find(n=>n.type==='package')?.id||treeNodes[0]?.id)">Neues Interface</button>
            <button @click="openNewModal('Paket',treeNodes.find(n=>n.type==='src')?.id||treeNodes[0]?.id)">Neues Paket</button>
            <hr/><button @click="speichern">Speichern <kbd>Strg+S</kbd></button>
            <button @click="activeTabId && closeTab(activeTabId)">Tab schließen <kbd>Strg+W</kbd></button>
          </div></div>

          <div class="mi" tabindex="0">Editor<div class="dd">
            <button @click="showShortcuts=true">Tastaturkürzel <kbd>F1</kbd></button>
          </div></div>

          <!-- Source mit Generierung -->
          <div class="mi" tabindex="0">Source<div class="dd">
            <div class="dd-section">Code generieren</div>
            <button @click="kiGeneriere('getter')" :disabled="kiGenLoading">Getter/Setter generieren</button>
            <button @click="kiGeneriere('ctor')"   :disabled="kiGenLoading">Konstruktor generieren</button>
            <button @click="kiGeneriere('tostr')"  :disabled="kiGenLoading">toString() generieren</button>
            <button @click="kiGeneriere('equals')" :disabled="kiGenLoading">equals()/hashCode() generieren</button>
            <button @click="kiGeneriere('method')" :disabled="kiGenLoading">Neue Methode generieren</button>
            <hr/>
            <button @click="openNewModal('Klasse',treeNodes.find(n=>n.type==='package')?.id||treeNodes[0]?.id)">Neue Klasse</button>
            <button @click="openNewModal('Interface',treeNodes.find(n=>n.type==='package')?.id||treeNodes[0]?.id)">Neues Interface</button>
          </div></div>

          <div class="mi" tabindex="0">Navigate<div class="dd">
            <button @click="router.push('/upload')">Neue Analyse</button>
            <button @click="router.push('/tasks')">Meine Aufgaben</button>
            <button @click="router.push('/dashboard')">Dashboard</button>
          </div></div>

          <button class="mb-run" @click="runCode" :disabled="isRunning">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Run
          </button>
          <button class="mb-pruefen" @click="pruefen" :disabled="isPruefen">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Prüfen
          </button>
        </template>

        <!-- Mobile: nur Lösung + Night/Light (siehe .mb-right) — kein Run/Prüfen, nur Lesen -->

        <button class="mb-loesung" @click="loesungEinfuegen" :disabled="loesungLoading">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          {{ loesungLoading ? 'Lädt…' : 'Lösung' }}
        </button>
      </div>

      <div class="mb-right">
        <span v-if="saveFlash" class="save-ok">✓</span>
        <button class="mb-night" @click="toggleTheme">{{ themeStore.dark?'☀':'🌙' }}</button>
      </div>
    </nav>

    <!-- ═══ MOBILE PANEL TABS ═══ -->
    <div v-if="isMobile" class="mobile-tabs">
      <button :class="{active:mobilePanel==='files'}"    @click="mobilePanel='files'">📁 Dateien</button>
      <button :class="{active:mobilePanel==='editor'}"   @click="mobilePanel='editor'">💻 Editor</button>
      <button :class="{active:mobilePanel==='schritte'}" @click="mobilePanel='schritte'">📋 Schritte</button>
      <button :class="{active:mobilePanel==='feedback'}" @click="mobilePanel='feedback'">💬 Feedback</button>
    </div>

    <!-- ═══ BODY ═══ -->
    <div class="body" :class="{'mobile-body':isMobile}">

      <!-- ── FILE TREE (desktop sempre / mobile solo se panel=files) ── -->
      <aside class="file-tree" v-show="!isMobile||mobilePanel==='files'">
        <div class="ft-head"><span>FILES</span>
          <button @click="openNewModal('Klasse',treeNodes.find(n=>n.type==='package')?.id||treeNodes[0]?.id)" title="Strg+N">+</button>
        </div>
        <div class="ft-body">
          <template v-for="root in treeNodes.filter(n=>!n.parentId)" :key="root.id">
            <div class="ftr" :class="{active:activeNodeId===root.id}" :style="{paddingLeft:'8px'}" @click="clickNode(root)">
              <span class="fta">{{ isFolder(root)?(expandedIds.has(root.id)?'▾':'▸'):' ' }}</span>
              <span class="fti">{{ nodeIcon(root) }}</span><span class="ftn">{{ root.name }}</span>
            </div>
            <template v-if="expandedIds.has(root.id)">
              <template v-for="n1 in childrenOf(root.id)" :key="n1.id">
                <div class="ftr" :class="{active:activeNodeId===n1.id}" :style="{paddingLeft:'22px'}" @click="clickNode(n1)">
                  <span class="fta">{{ isFolder(n1)?(expandedIds.has(n1.id)?'▾':'▸'):' ' }}</span>
                  <span class="fti">{{ nodeIcon(n1) }}</span><span class="ftn">{{ n1.name }}</span>
                </div>
                <template v-if="expandedIds.has(n1.id)">
                  <template v-for="n2 in childrenOf(n1.id)" :key="n2.id">
                    <div class="ftr" :class="{active:activeNodeId===n2.id}" :style="{paddingLeft:'36px'}" @click="clickNode(n2)">
                      <span class="fta">{{ isFolder(n2)?(expandedIds.has(n2.id)?'▾':'▸'):' ' }}</span>
                      <span class="fti">{{ nodeIcon(n2) }}</span><span class="ftn">{{ n2.name }}</span>
                    </div>
                    <template v-if="expandedIds.has(n2.id)">
                      <div v-for="n3 in childrenOf(n2.id)" :key="n3.id" class="ftr" :class="{active:activeNodeId===n3.id}" :style="{paddingLeft:'50px'}" @click="clickNode(n3)">
                        <span class="fta"> </span><span class="fti">{{ nodeIcon(n3) }}</span><span class="ftn">{{ n3.name }}</span>
                      </div>
                    </template>
                  </template>
                </template>
              </template>
            </template>
          </template>
        </div>
      </aside>

      <!-- ── CENTER: Editor + Konsole ── -->
      <div class="center" v-show="!isMobile||mobilePanel==='editor'">
        <!-- Mobile Warnung -->
        <div v-if="isMobile" class="mobile-warn">
          ⚠️ Der Code-Editor ist nur auf dem Desktop verfügbar. Hier kannst du deinen Code lesen aber nicht bearbeiten.
        </div>

        <!-- Tabs -->
        <div class="tabs">
          <div v-for="t in tabs" :key="t.id" class="tab" :class="{active:activeTabId===t.id}" @click="switchTab(t.id)">
            {{ t.name }}<button class="tx" @click.stop="closeTab(t.id)">×</button>
          </div>
          <div v-if="!tabs.length" class="tab-e">Strg+N</div>
        </div>

        <!-- CodeMirror (Desktop) -->
        <div v-if="!isMobile" class="cm-wrap" ref="cmContainer"></div>
        <!-- Mobile: nur Lesen, kein CodeMirror-Editor -->
        <pre v-else class="mobile-code">{{ activeTab?.content || '// Keine Datei geöffnet' }}</pre>

        <!-- Konsole (nur Desktop, da Run auf Mobile nicht verfügbar ist) -->
        <div v-if="!isMobile" class="kon-sec">
          <div class="kon-head">
            <span>KONSOLE</span>
            <div style="display:flex;align-items:center;gap:8px">
              <span v-if="isRunning" class="kon-run">● läuft…</span>
              <button class="kon-clr" @click="clearAll">Leeren</button>
            </div>
          </div>
          <div class="konsole" ref="konsoleRef">
            <span v-if="!consoleLogs.length" class="kon-e">Strg+R zum Ausführen</span>
            <div v-for="(l,i) in consoleLogs" :key="i" class="kl" :class="l.type">
              <span class="kp">{{ l.type==='error'?'✕':l.type==='success'?'✓':'›' }}</span>
              <pre class="km">{{ l.msg }}</pre>
            </div>
          </div>
        </div>
      </div>

      <!-- ── RIGHT: Schritte / Feedback ── -->
      <aside class="right" v-show="!isMobile||mobilePanel==='schritte'||mobilePanel==='feedback'">
        <div class="rp-tabs">
          <button :class="{active:rightPanel==='schritte'}" @click="wechselPanel('schritte')">Schritte</button>
          <button :class="{active:rightPanel==='feedback'}" @click="wechselPanel('feedback')">Feedback</button>
        </div>
        <div class="rp-task" v-if="curTask">
          <span class="rp-num">Aufgabe {{ taskIndex+1 }} / {{ totalTasks }}</span>
          <span class="rp-ttl">{{ curTask.task_title }}</span>
        </div>

        <!-- SCHRITTE -->
        <div v-if="rightPanel==='schritte'" class="rp-body">
          <!-- Vorherige Schritte anzeigen -->
          <div class="rp-block" v-if="taskIndex>0&&prevTaskSteps">
            <details class="hist-details">
              <summary class="rp-lbl">📋 Schritte Aufgabe {{ taskIndex }} (vorherige)</summary>
              <ol class="steps" style="opacity:.7;margin-top:8px">
                <li v-for="(s,i) in prevTaskSteps" :key="i" class="done">
                  <span class="sdot">✓</span><span class="stxt">{{ s }}</span>
                </li>
              </ol>
            </details>
          </div>

          <div class="rp-block">
            <div class="rp-lbl">FRAGESTELLUNG</div>
            <p class="rp-txt">{{ curTask?.question }}</p>
          </div>
          <div class="rp-block">
            <div class="rp-lblrow">
              <span class="rp-lbl">SCHRITTE</span><span class="rp-pct">{{ progress }}%</span>
            </div>
            <div class="prog"><div :style="{width:progress+'%'}"></div></div>
            <ol class="steps">
              <li v-for="(s,i) in curTask?.steps||[]" :key="i" :class="{done:doneSteps.includes(i)}" @click="toggleStep(i)">
                <span class="sdot">{{ doneSteps.includes(i)?'✓':i+1 }}</span>
                <span class="stxt">{{ s }}</span>
              </li>
            </ol>
          </div>
          <div class="rp-block" v-if="curTask?.test_cases?.length">
            <div class="rp-lbl">TESTFÄLLE</div>
            <ul class="tc-list">
              <li v-for="(tc,i) in (hasRun&&testResults.length?testResults:curTask.test_cases)" :key="i" :class="{pass:tc.pass===true,fail:hasRun&&tc.pass===false}">
                <span class="tc-ic">{{ tc.pass===true?'✓':(hasRun?'✕':'●') }}</span>
                <code>{{ tc.expected_output }}</code>
              </li>
            </ul>
          </div>
        </div>

        <!-- FEEDBACK -->
        <div v-else class="rp-body">
          <!-- Feedback History -->
          <div class="rp-block" v-if="feedbackHistory.length">
            <details class="hist-details">
              <summary class="rp-lbl">🕐 Vorherige Feedbacks ({{ feedbackHistory.length }})</summary>
              <div v-for="(h,i) in feedbackHistory" :key="i" class="hist-fb">
                <div class="hist-meta">Aufgabe {{ h.taskIndex+1 }} · {{ h.date }}</div>
                <div class="hist-txt">{{ h.text.slice(0,200) }}{{ h.text.length>200?'…':'' }}</div>
              </div>
            </details>
          </div>

          <div class="rp-block" v-if="isMobile" style="background:#fef9c3;border-left:3px solid #fde68a;">
            <p class="rp-txt" style="color:#92400e;">⚠️ Prüfen ist nur auf dem Desktop verfügbar. Öffne das Projekt am Computer, um deinen Code bewerten zu lassen.</p>
          </div>
          <div class="rp-block" v-if="!isMobile&&!feedback&&!isPruefen">
            <p class="rp-txt">Klicke <strong>Prüfen</strong> (Strg+Shift+P) um deinen Code bewerten zu lassen.</p>
          </div>
          <div class="rp-block" v-if="isPruefen">
            <div class="fb-load"><span class="spin"></span>KI überprüft…</div>
          </div>
          <div class="rp-block" v-if="feedback">
            <div class="rp-lbl">AKTUELLES FEEDBACK</div>
            <div class="fb-txt" :class="{ok:pruefenOk}">{{ feedback }}</div>
            <button v-if="pruefenOk&&taskIndex<totalTasks-1" class="btn-next" @click="naechsteAufgabe" :disabled="naechsteLoading">
              <span v-if="naechsteLoading" class="spin"></span>
              {{ naechsteLoading ? 'Schritte werden generiert…' : 'Nächste Aufgabe →' }}
            </button>
            <div v-else-if="pruefenOk&&taskIndex>=totalTasks-1" class="all-done">🎉 Alle Aufgaben abgeschlossen!</div>
          </div>
        </div>
      </aside>
    </div>

    <!-- ═══ MODALS ═══ -->
    <div v-if="showNewModal" class="modal-bg" @click.self="showNewModal=false">
      <div class="modal">
        <div class="modal-ttl">Neue {{ newType }} erstellen</div>
        <input ref="newNameInput" v-model="newName" class="modal-inp" :placeholder="newType==='Klasse'?'z. B. Animal':newType==='Interface'?'z. B. Flyable':'z. B. utils'" @keydown.enter="confirmNew"/>
        <div class="modal-row">
          <button class="mc" @click="showNewModal=false">Abbrechen</button>
          <button class="mo" @click="confirmNew" :disabled="!newName.trim()">Erstellen</button>
        </div>
      </div>
    </div>

    <div v-if="showShortcuts" class="modal-bg" @click.self="showShortcuts=false">
      <div class="modal shortcuts-modal">
        <div class="modal-ttl">⌨ Tastaturkürzel</div>
        <div class="sc-grid">
          <div class="sc-sec"><div class="sc-h">Allgemein</div>
            <div class="sc-r"><kbd>Strg+N</kbd><span>Neue Klasse</span></div>
            <div class="sc-r"><kbd>Strg+S</kbd><span>Speichern</span></div>
            <div class="sc-r"><kbd>Strg+W</kbd><span>Tab schließen</span></div>
            <div class="sc-r"><kbd>F1</kbd><span>Shortcuts</span></div>
          </div>
          <div class="sc-sec"><div class="sc-h">Ausführen</div>
            <div class="sc-r"><kbd>Strg+R</kbd><span>Run</span></div>
            <div class="sc-r"><kbd>Strg+Shift+P</kbd><span>Prüfen</span></div>
          </div>
          <div class="sc-sec"><div class="sc-h">Editor</div>
            <div class="sc-r"><kbd>Tab</kbd><span>Einrücken</span></div>
            <div class="sc-r"><kbd>Strg+Z</kbd><span>Rückgängig</span></div>
            <div class="sc-r"><kbd>Strg+Y</kbd><span>Wiederholen</span></div>
            <div class="sc-r"><kbd>Strg+/</kbd><span>Kommentieren</span></div>
            <div class="sc-r"><kbd>Strg+F</kbd><span>Suchen</span></div>
            <div class="sc-r"><kbd>Strg+D</kbd><span>Zeile duplizieren</span></div>
            <div class="sc-r"><kbd>Strg+Space</kbd><span>Autovervollständigung</span></div>
            <div class="sc-r"><kbd>{</kbd><span>Auto → { }</span></div>
          </div>
        </div>
        <button class="mo" style="margin-top:14px;width:100%" @click="showShortcuts=false">Schließen</button>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* ── VARIABLEN ── */
.il-root {
  --mb:#1e1e2e;--ft:#1a1a2a;--rt:#ffffff;--rbdr:#e2e8f0;
  --rtxt:#1e293b;--rtxt2:#64748b;--rtxt3:#94a3b8;
  --acc:#7c3aed;--acc2:#ede9fe;--kon:#0f172a;--tabs:#1a1a2a;--tbdr:#2a2a3c;
  --etext:#cdd6f4;--eln:#45475a;--ebdr:#2a2a3c;
  display:flex;flex-direction:column;height:100vh;overflow:hidden;
  font-family:'Segoe UI',system-ui,sans-serif;
  background:var(--mb);color:var(--etext);
}
.il-root.night{--rt:#1a1a2e;--rbdr:#2a2a3c;--rtxt:#cdd6f4;--rtxt2:#a6adc8;--rtxt3:#6c7086;--acc2:#2a1e4a;}
.il-root:not(.night) .right{background:var(--rt);}

/* MENUBAR */
.menubar{height:40px;background:var(--mb);display:flex;align-items:center;justify-content:space-between;padding:0 10px;flex-shrink:0;border-bottom:1px solid rgba(255,255,255,.07);z-index:50;}
.mb-left,.mb-right{display:flex;align-items:center;gap:3px;}
.logo-link{display:flex;align-items:center;gap:8px;text-decoration:none;margin-right:4px;}
.logo-txt{font-size:.85rem;font-weight:700;color:#c4b5fd;}
.mb-sep{width:1px;height:20px;background:rgba(255,255,255,.12);margin:0 5px;}
.mi{position:relative;padding:5px 10px;font-size:.76rem;color:#d4d4d8;cursor:pointer;border-radius:4px;outline:none;user-select:none;}
.mi:hover,.mi:focus-within{background:rgba(255,255,255,.09);}
.dd{display:none;position:absolute;top:calc(100% + 4px);left:0;background:#2d2d3f;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:4px;min-width:220px;box-shadow:0 10px 30px rgba(0,0,0,.5);z-index:200;}
.mi:hover .dd,.mi:focus-within .dd{display:flex;flex-direction:column;}
.dd button{background:none;border:none;color:#e2e8f0;font-size:.76rem;padding:7px 12px;border-radius:4px;cursor:pointer;text-align:left;display:flex;justify-content:space-between;align-items:center;gap:8px;}
.dd button:hover{background:rgba(255,255,255,.1);}.dd button:disabled{opacity:.4;cursor:not-allowed;}
.dd hr{border:none;border-top:1px solid rgba(255,255,255,.1);margin:3px 0;}
.dd kbd{font-size:.68rem;background:rgba(255,255,255,.15);padding:1px 5px;border-radius:3px;font-family:monospace;}
.dd-section{font-size:.65rem;font-weight:700;letter-spacing:.07em;color:#6c7086;padding:7px 12px 3px;text-transform:uppercase;}
.mb-run{display:flex;align-items:center;gap:5px;padding:5px 12px;background:#16a34a;color:white;border:none;border-radius:6px;font-size:.78rem;font-weight:700;cursor:pointer;margin-left:6px;}
.mb-run:disabled{opacity:.5;cursor:wait;}
.mb-pruefen{display:flex;align-items:center;gap:5px;padding:5px 12px;background:#0284c7;color:white;border:none;border-radius:6px;font-size:.78rem;font-weight:700;cursor:pointer;margin-left:3px;}
.mb-pruefen:disabled{opacity:.5;cursor:wait;}
.mb-loesung{display:flex;align-items:center;gap:5px;padding:5px 11px;background:rgba(255,255,255,.1);color:#d4d4d8;border:none;border-radius:6px;font-size:.76rem;cursor:pointer;margin-left:3px;}
.mb-loesung:hover{background:rgba(255,255,255,.17);}
.mb-loesung:disabled{opacity:.5;cursor:wait;}
.mb-night{background:rgba(255,255,255,.1);border:none;color:#d4d4d8;font-size:.78rem;padding:5px 10px;border-radius:6px;cursor:pointer;margin-left:4px;}
.mb-night:hover{background:rgba(255,255,255,.17);}
.save-ok{font-size:.73rem;color:#a6e3a1;margin-right:5px;}

/* MOBILE TABS */
.mobile-tabs{display:flex;background:#181825;border-bottom:1px solid #2a2a3c;flex-shrink:0;}
.mobile-tabs button{flex:1;padding:10px 4px;background:none;border:none;color:#6c7086;font-size:.72rem;cursor:pointer;border-bottom:2px solid transparent;}
.mobile-tabs button.active{color:#c4b5fd;border-bottom-color:#7c3aed;}

/* MOBILE WARN */
.mobile-warn{background:#fef9c3;color:#92400e;padding:8px 12px;font-size:.78rem;text-align:center;border-bottom:1px solid #fde68a;flex-shrink:0;}

/* BODY */
.body{display:grid;grid-template-columns:215px 1fr 265px;flex:1;overflow:hidden;min-height:0;}
.mobile-body{grid-template-columns:1fr;}

/* FILE TREE */
.file-tree{background:var(--ft);border-right:1px solid rgba(255,255,255,.06);display:flex;flex-direction:column;overflow:hidden;}
.ft-head{display:flex;align-items:center;justify-content:space-between;padding:7px 10px;font-size:.67rem;font-weight:700;letter-spacing:.07em;color:rgba(255,255,255,.38);flex-shrink:0;border-bottom:1px solid rgba(255,255,255,.05);}
.ft-head button{background:none;border:none;color:rgba(255,255,255,.4);font-size:17px;cursor:pointer;line-height:1;}
.ft-head button:hover{color:white;}
.ft-body{flex:1;overflow-y:auto;padding:4px 0;}
.ftr{display:flex;align-items:center;gap:5px;height:25px;cursor:pointer;font-size:.76rem;color:#a6adc8;user-select:none;transition:background .1s;padding-right:8px;}
.ftr:hover{background:rgba(255,255,255,.06);}.ftr.active{background:rgba(124,58,237,.22);color:#cdd6f4;}
.fta{font-size:.6rem;width:10px;flex-shrink:0;color:#6c7086;}
.fti{font-size:11px;flex-shrink:0;}.ftn{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* CENTER */
.center{display:flex;flex-direction:column;overflow:hidden;background:#1e1e2e;}
.tabs{display:flex;align-items:center;background:var(--tabs);border-bottom:1px solid var(--tbdr);min-height:34px;overflow-x:auto;flex-shrink:0;}
.tab{display:flex;align-items:center;gap:5px;padding:0 12px;height:34px;font-size:.73rem;color:#6c7086;cursor:pointer;border-right:1px solid var(--tbdr);white-space:nowrap;font-family:ui-monospace,monospace;}
.tab:hover{color:#a6adc8;}.tab.active{color:#cdd6f4;background:#1e1e2e;border-top:2px solid #7c3aed;}
.tx{background:none;border:none;color:#45475a;font-size:13px;cursor:pointer;padding:0;line-height:1;}
.tx:hover{color:#f38ba8;}.tab-e{padding:0 14px;font-size:.73rem;color:#45475a;line-height:34px;}
.cm-wrap{flex:1;overflow:hidden;min-height:0;}
:deep(.cm-editor){height:100%;}:deep(.cm-scroller){overflow:auto;}
.mobile-code{flex:1;margin:0;overflow:auto;padding:12px;font-family:ui-monospace,monospace;font-size:.78rem;line-height:1.6;color:#cdd6f4;white-space:pre-wrap;word-break:break-word;}
/* Light mode CM overrides */
.il-root:not(.night) .cm-wrap :deep(.cm-editor){background:#fff!important;}
.il-root:not(.night) .cm-wrap :deep(.cm-gutters){background:#f8fafc!important;border-right:1px solid #e2e8f0!important;color:#94a3b8!important;}
.il-root:not(.night) .cm-wrap :deep(.cm-content){color:#1e293b!important;}
.il-root:not(.night) .cm-wrap :deep(.cm-activeLine){background:#faf5ff!important;}

/* KONSOLE */
.kon-sec{background:#13131e;border-top:1px solid #2a2a3c;flex-shrink:0;display:flex;flex-direction:column;max-height:185px;}
.kon-head{display:flex;justify-content:space-between;align-items:center;padding:5px 10px;border-bottom:1px solid #2a2a3c;font-size:.65rem;font-weight:700;letter-spacing:.07em;color:#6c7086;}
.kon-run{font-size:.68rem;color:#a6e3a1;animation:pulse 1.2s ease-in-out infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
.kon-clr{background:none;border:none;color:#6c7086;font-size:.68rem;cursor:pointer;}.kon-clr:hover{color:#f38ba8;}
.konsole{flex:1;overflow-y:auto;padding:8px 10px;min-height:55px;}
.kon-e{font-size:.73rem;color:#45475a;font-family:monospace;}
.kl{display:flex;gap:5px;align-items:flex-start;margin-bottom:2px;}
.kp{font-size:.73rem;flex-shrink:0;margin-top:1px;}
.kl.error .kp{color:#f38ba8;}.kl.success .kp{color:#a6e3a1;}.kl.info .kp{color:#89b4fa;}
.km{font-size:.73rem;font-family:ui-monospace,monospace;color:#e2e8f0;margin:0;white-space:pre-wrap;word-break:break-all;line-height:1.45;}
.kl.error .km{color:#f38ba8;}.kl.success .km{color:#a6e3a1;}

/* RIGHT */
.right{background:var(--rt);border-left:1px solid var(--rbdr);display:flex;flex-direction:column;overflow:hidden;}
.rp-tabs{display:flex;border-bottom:1px solid var(--rbdr);flex-shrink:0;}
.rp-tabs button{flex:1;padding:9px;background:none;border:none;font-size:.76rem;font-weight:600;color:var(--rtxt3);cursor:pointer;border-bottom:2px solid transparent;transition:all .12s;}
.rp-tabs button.active{color:var(--acc);border-bottom-color:var(--acc);}
.rp-task{padding:8px 12px;background:var(--acc2);border-bottom:1px solid var(--rbdr);flex-shrink:0;}
.rp-num{font-size:.63rem;font-weight:700;color:var(--acc);text-transform:uppercase;letter-spacing:.05em;display:block;}
.rp-ttl{font-size:.8rem;font-weight:600;color:var(--rtxt);display:block;margin-top:2px;}
.rp-body{flex:1;overflow-y:auto;display:flex;flex-direction:column;}
.rp-block{padding:12px;border-bottom:1px solid var(--rbdr);}
.rp-lbl{font-size:.62rem;font-weight:700;letter-spacing:.07em;color:var(--rtxt3);margin-bottom:7px;text-transform:uppercase;}
.rp-lblrow{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;}
.rp-pct{font-size:.74rem;font-weight:700;color:var(--acc);}
.rp-txt{font-size:.8rem;line-height:1.6;color:var(--rtxt2);margin:0;}
.prog{height:4px;background:var(--acc2);border-radius:999px;overflow:hidden;margin-bottom:10px;}
.prog div{height:100%;background:var(--acc);transition:width .3s;}
.steps{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:3px;}
.steps li{display:flex;gap:7px;align-items:flex-start;padding:6px 7px;border-radius:6px;cursor:pointer;font-size:.78rem;line-height:1.45;color:var(--rtxt);transition:background .1s;}
.steps li:hover{background:var(--acc2);}.steps li.done{color:var(--rtxt3);}
.steps li.done .stxt{text-decoration:line-through;}
.sdot{flex:0 0 20px;height:20px;border-radius:50%;background:var(--acc2);color:var(--acc);font-size:.68rem;font-weight:700;display:grid;place-items:center;}
.done .sdot{background:var(--acc);color:white;}.stxt{flex:1;}
.tc-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px;}
.tc-list li{display:flex;gap:6px;align-items:center;padding:6px 8px;border-radius:6px;border:1px solid var(--rbdr);font-size:.75rem;background:var(--acc2);}
.tc-list li.pass{background:#f0fdf4;border-color:#86efac;}.tc-list li.fail{background:#fef2f2;border-color:#fca5a5;}
.tc-ic{font-weight:700;color:var(--rtxt3);}.tc-list li.pass .tc-ic{color:#16a34a;}.tc-list li.fail .tc-ic{color:#dc2626;}
.tc-list code{font-family:ui-monospace,monospace;color:var(--rtxt2);font-size:.72rem;}

/* History */
.hist-details summary{cursor:pointer;font-size:.68rem;font-weight:700;letter-spacing:.07em;color:var(--rtxt3);text-transform:uppercase;padding:4px 0;list-style:none;}
.hist-details summary::-webkit-details-marker{display:none;}
.hist-fb{margin-top:8px;padding:8px;background:var(--acc2);border-radius:7px;border-left:3px solid var(--acc);}
.hist-meta{font-size:.65rem;color:var(--rtxt3);margin-bottom:4px;}
.hist-txt{font-size:.76rem;color:var(--rtxt2);line-height:1.5;}

/* Feedback */
.fb-load{display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--rtxt2);}
.spin{width:14px;height:14px;border:2px solid var(--acc2);border-top-color:var(--acc);border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.fb-txt{font-size:.8rem;line-height:1.65;color:var(--rtxt);background:var(--acc2);border:1px solid var(--rbdr);border-radius:7px;padding:10px;white-space:pre-wrap;margin-bottom:10px;}
.fb-txt.ok{border-color:#86efac;background:#f0fdf4;}
.il-root.night .fb-txt{background:#1e1e2e;}.il-root.night .fb-txt.ok{background:#0f2e1a;border-color:#166534;}
.btn-next{width:100%;padding:9px;background:var(--acc);color:white;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;}
.btn-next:hover{opacity:.85;}
.all-done{text-align:center;padding:12px;font-size:.85rem;color:#059669;background:#f0fdf4;border-radius:7px;border:1px solid #86efac;font-weight:600;}
.il-root.night .all-done{background:#0f2e1a;}

/* MODALS */
.modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;display:flex;align-items:center;justify-content:center;padding:16px;}
.modal{background:#1e1e2e;border:1px solid #45475a;border-radius:13px;padding:22px;width:320px;max-width:100%;box-shadow:0 20px 60px rgba(0,0,0,.5);}
.shortcuts-modal{width:640px;}
.modal-ttl{font-size:.92rem;font-weight:700;color:#cdd6f4;margin-bottom:13px;}
.modal-inp{width:100%;padding:8px 11px;background:#11111b;border:1px solid #313244;color:#cdd6f4;font-size:.85rem;border-radius:7px;outline:none;box-sizing:border-box;font-family:ui-monospace,monospace;}
.modal-inp:focus{border-color:#7c3aed;}
.modal-row{display:flex;justify-content:flex-end;gap:8px;margin-top:13px;}
.mc{background:none;border:1px solid #45475a;color:#a6adc8;font-size:.8rem;padding:6px 14px;border-radius:6px;cursor:pointer;}
.mo{background:#7c3aed;border:none;color:white;font-size:.8rem;font-weight:600;padding:6px 14px;border-radius:6px;cursor:pointer;}
.mo:disabled{opacity:.5;cursor:not-allowed;}
.sc-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
.sc-sec{display:flex;flex-direction:column;gap:3px;}
.sc-h{font-size:.68rem;font-weight:700;letter-spacing:.07em;color:#6c7086;text-transform:uppercase;margin-bottom:4px;padding-bottom:4px;border-bottom:1px solid #313244;}
.sc-r{display:flex;align-items:center;gap:8px;padding:3px 0;font-size:.76rem;color:#a6adc8;}
.sc-r kbd{font-size:.67rem;background:#313244;padding:2px 6px;border-radius:4px;font-family:monospace;color:#cdd6f4;border:1px solid #45475a;white-space:nowrap;}

::-webkit-scrollbar{width:4px;height:4px;}
::-webkit-scrollbar-thumb{background:rgba(100,100,120,.35);border-radius:2px;}

@media(max-width:768px){
  .body{grid-template-columns:1fr;height:calc(100vh - 88px);}
  .file-tree,.center,.right{height:100%;}
  .shortcuts-modal{width:95vw;}.sc-grid{grid-template-columns:1fr 1fr;}
}
</style>

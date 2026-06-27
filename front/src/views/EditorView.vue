<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Sidebar from '../components/Sidebar.vue'
import { exerciseStore } from '../stores/exercise'

const router = useRouter()

const ex = computed(() => exerciseStore.current)

// --- guard: no exercise loaded -> back to upload ---
onMounted(() => {
  if (!exerciseStore.current) router.replace('/upload')
})

// --- editor state ---
const code = ref(ex.value?.exercise?.starter_code || '')
const showSolution = ref(false)
const consoleOutput = ref('')
const hasRun = ref(false)

// --- step progress (left pane) ---
const steps = computed(() => ex.value?.steps || [])
const done = ref([])
function toggleStep(i) {
  done.value = done.value.includes(i)
    ? done.value.filter((x) => x !== i)
    : [...done.value, i]
}
const progress = computed(() =>
  steps.value.length ? Math.round((done.value.length / steps.value.length) * 100) : 0
)

// --- difficulty badge ---
const difficultyClass = computed(() => {
  const d = (ex.value?.difficulty || '').toLowerCase()
  if (d.includes('easy') || d.includes('leicht')) return 'badge-easy'
  if (d.includes('hard') || d.includes('schwer')) return 'badge-hard'
  return 'badge-medium'
})

// --- language detection (JS runnable in-browser, Python not yet) ---
const lang = computed(() => {
  const c = `${ex.value?.exercise?.starter_code || ''}\n${ex.value?.solution?.final_code || ''}`
  if (/\bdef\s+\w+\s*\(/.test(c) || (/\bprint\s*\(/.test(c) && !/console\.log/.test(c))) {
    return 'python'
  }
  return 'javascript'
})

const testCases = computed(() => ex.value?.test_cases || [])
const testResults = ref([])

function runJs(source) {
  const logs = []
  const push = (...args) =>
    logs.push(
      args
        .map((a) => (typeof a === 'object' ? JSON.stringify(a) : String(a)))
        .join(' ')
    )
  const sandboxConsole = { log: push, error: push, warn: push, info: push }
  try {
    // eslint-disable-next-line no-new-func
    const fn = new Function('console', source)
    fn(sandboxConsole)
    return { ok: true, output: logs.join('\n') }
  } catch (e) {
    return { ok: false, output: logs.join('\n'), error: String(e) }
  }
}

function run() {
  hasRun.value = true

  if (lang.value !== 'javascript') {
    consoleOutput.value =
      '⚠️  In-Browser-Ausführung gibt es aktuell nur für JavaScript.\n' +
      'Python-Ausführung folgt in einer späteren Phase. ' +
      'Du kannst die Lösung unten einblenden.'
    testResults.value = []
    return
  }

  const result = runJs(code.value)
  consoleOutput.value = result.error
    ? `${result.output ? result.output + '\n' : ''}❌ Fehler: ${result.error}`
    : result.output || '(keine Ausgabe — nutze console.log, um etwas auszugeben)'

  // Heuristic check: does the output contain each expected_output?
  testResults.value = testCases.value.map((tc) => {
    const expected = String(tc.expected_output ?? '').trim()
    const pass = expected !== '' && result.output.includes(expected)
    return { ...tc, pass }
  })
}

function resetCode() {
  code.value = ex.value?.exercise?.starter_code || ''
  consoleOutput.value = ''
  testResults.value = []
  hasRun.value = false
}

function newExercise() {
  router.push('/upload')
}
</script>

<template>
  <div class="editor-page" v-if="ex">
    <Sidebar />

    <div class="workspace">
      <!-- top bar -->
      <header class="topbar">
        <div class="title-wrap">
          <h1>{{ ex.title || 'Übung' }}</h1>
          <div class="meta">
            <span class="badge" :class="difficultyClass">{{ ex.difficulty || '—' }}</span>
            <span class="topic">{{ ex.topic }}</span>
            <span class="lang">{{ lang }}</span>
          </div>
        </div>
        <button class="ghost" @click="newExercise">＋ Neue Aufgabe</button>
      </header>

      <div class="panes">
        <!-- LEFT: steps / instructions -->
        <aside class="pane steps-pane">
          <section class="block">
            <h2>Konzept</h2>
            <p class="explanation">{{ ex.explanation }}</p>
          </section>

          <section class="block">
            <div class="steps-head">
              <h2>Schritte</h2>
              <span class="progress-label">{{ progress }}%</span>
            </div>
            <div class="progress-bar"><div :style="{ width: progress + '%' }"></div></div>

            <ol class="steps">
              <li
                v-for="(step, i) in steps"
                :key="i"
                :class="{ checked: done.includes(i) }"
                @click="toggleStep(i)"
              >
                <span class="dot">{{ done.includes(i) ? '✓' : i + 1 }}</span>
                <span>{{ step }}</span>
              </li>
            </ol>
          </section>

          <section class="block task">
            <h2>Aufgabe</h2>
            <p>{{ ex.exercise?.question }}</p>
          </section>
        </aside>

        <!-- CENTER: code editor -->
        <main class="pane editor-pane">
          <div class="editor-head">
            <span class="filename">solution.{{ lang === 'python' ? 'py' : 'js' }}</span>
            <div class="editor-actions">
              <button class="link" @click="resetCode">Zurücksetzen</button>
              <button class="link" @click="showSolution = !showSolution">
                {{ showSolution ? 'Lösung verbergen' : 'Lösung anzeigen' }}
              </button>
              <button class="run" @click="run">▶ Ausführen</button>
            </div>
          </div>

          <textarea
            v-model="code"
            class="code"
            spellcheck="false"
            placeholder="// Schreibe deinen Code hier…"
          ></textarea>

          <transition name="fade">
            <div v-if="showSolution" class="solution">
              <h3>Lösung</h3>
              <pre class="code-block">{{ ex.solution?.final_code }}</pre>
              <p class="sol-explain">{{ ex.solution?.explanation }}</p>
            </div>
          </transition>
        </main>

        <!-- RIGHT: console + tests -->
        <aside class="pane console-pane">
          <div class="console-head">Konsole</div>
          <pre class="console">{{ consoleOutput || 'Drücke „Ausführen“, um deinen Code laufen zu lassen.' }}</pre>

          <div v-if="testCases.length" class="tests">
            <div class="tests-head">Testfälle</div>
            <ul>
              <li
                v-for="(tc, i) in (hasRun && testResults.length ? testResults : testCases)"
                :key="i"
                :class="{ pass: tc.pass === true, fail: hasRun && tc.pass === false }"
              >
                <span class="status">
                  {{ tc.pass === true ? '✓' : (hasRun ? '✕' : '•') }}
                </span>
                <div class="tc-body">
                  <code v-if="tc.input">in: {{ tc.input }}</code>
                  <code>erwartet: {{ tc.expected_output }}</code>
                </div>
              </li>
            </ul>
            <p class="tests-note">Heuristische Prüfung: vergleicht deine Ausgabe mit dem erwarteten Wert.</p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<style scoped>
.editor-page {
  min-height: 100vh;
  display: flex;
  background: #f8fafc;
}

.workspace {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* top bar */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 18px 24px;
  background: white;
  border-bottom: 1px solid #ececf1;
}

.title-wrap h1 {
  font-size: 1.25rem;
  margin: 0 0 6px;
}

.meta {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.badge {
  font-size: .72rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 999px;
  text-transform: capitalize;
}

.badge-easy   { background: #dcfce7; color: #15803d; }
.badge-medium { background: #fef3c7; color: #b45309; }
.badge-hard   { background: #fee2e2; color: #b91c1c; }

.topic, .lang {
  font-size: .8rem;
  color: #64748b;
}

.lang {
  background: #f1f5f9;
  padding: 2px 8px;
  border-radius: 6px;
}

.ghost {
  background: #ede9fe;
  color: #6d28d9;
  border: none;
  padding: 9px 14px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  white-space: nowrap;
}

.ghost:hover { background: #ddd6fe; }

/* three panes */
.panes {
  flex: 1;
  display: grid;
  grid-template-columns: 340px 1fr 320px;
  min-height: 0;
}

.pane {
  overflow-y: auto;
  min-width: 0;
}

/* left */
.steps-pane {
  background: white;
  border-right: 1px solid #ececf1;
  padding: 22px;
}

.block { margin-bottom: 26px; }

.block h2 {
  font-size: .8rem;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: #94a3b8;
  margin: 0 0 10px;
}

.explanation { line-height: 1.55; color: #334155; }

.steps-head {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
}

.progress-label { font-size: .8rem; color: #7c3aed; font-weight: 600; }

.progress-bar {
  height: 6px;
  background: #ede9fe;
  border-radius: 999px;
  overflow: hidden;
  margin: 4px 0 14px;
}

.progress-bar div {
  height: 100%;
  background: #7c3aed;
  transition: width .25s ease;
}

.steps {
  list-style: none;
  margin: 0;
  padding: 0;
}

.steps li {
  display: flex;
  gap: 10px;
  padding: 10px;
  border-radius: 10px;
  cursor: pointer;
  line-height: 1.45;
  color: #334155;
}

.steps li:hover { background: #faf5ff; }
.steps li.checked { color: #94a3b8; }
.steps li.checked span:last-child { text-decoration: line-through; }

.dot {
  flex: 0 0 24px;
  height: 24px;
  border-radius: 50%;
  background: #ede9fe;
  color: #6d28d9;
  font-size: .8rem;
  font-weight: 700;
  display: grid;
  place-items: center;
}

.checked .dot { background: #7c3aed; color: white; }

.task { background: #faf5ff; border-radius: 12px; padding: 16px; }
.task p { line-height: 1.55; color: #334155; }

/* center */
.editor-pane {
  display: flex;
  flex-direction: column;
  background: #1e1e2e;
}

.editor-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: #181825;
  border-bottom: 1px solid #2a2a3c;
}

.filename { color: #a6adc8; font-size: .82rem; font-family: ui-monospace, monospace; }

.editor-actions { display: flex; gap: 8px; }

.link {
  background: transparent;
  border: 1px solid #3a3a4f;
  color: #cdd6f4;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-size: .8rem;
}

.link:hover { background: #2a2a3c; }

.run {
  background: #7c3aed;
  border: none;
  color: white;
  padding: 6px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-size: .8rem;
  font-weight: 600;
}

.run:hover { background: #6d28d9; }

.code {
  flex: 1;
  min-height: 240px;
  resize: none;
  border: none;
  outline: none;
  padding: 18px;
  background: #1e1e2e;
  color: #cdd6f4;
  font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace;
  font-size: .9rem;
  line-height: 1.6;
  tab-size: 2;
}

.solution {
  background: #181825;
  border-top: 1px solid #2a2a3c;
  padding: 16px 18px;
}

.solution h3 { color: #f9e2af; margin: 0 0 10px; font-size: .9rem; }

.code-block {
  background: #11111b;
  color: #a6e3a1;
  padding: 14px;
  border-radius: 10px;
  overflow-x: auto;
  font-family: ui-monospace, monospace;
  font-size: .85rem;
  line-height: 1.55;
  white-space: pre-wrap;
}

.sol-explain { color: #bac2de; line-height: 1.55; margin-top: 12px; font-size: .9rem; }

/* right */
.console-pane {
  background: white;
  border-left: 1px solid #ececf1;
  display: flex;
  flex-direction: column;
}

.console-head, .tests-head {
  font-size: .75rem;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: #94a3b8;
  padding: 14px 18px 8px;
}

.console {
  margin: 0 18px;
  background: #0f172a;
  color: #e2e8f0;
  border-radius: 10px;
  padding: 14px;
  font-family: ui-monospace, monospace;
  font-size: .82rem;
  line-height: 1.55;
  white-space: pre-wrap;
  word-break: break-word;
  min-height: 120px;
}

.tests { padding: 8px 18px 22px; }

.tests ul { list-style: none; margin: 0; padding: 0; }

.tests li {
  display: flex;
  gap: 10px;
  padding: 10px;
  border-radius: 10px;
  background: #f8fafc;
  margin-bottom: 8px;
}

.tests li.pass { background: #f0fdf4; }
.tests li.fail { background: #fef2f2; }

.status { font-weight: 700; color: #94a3b8; }
.pass .status { color: #16a34a; }
.fail .status { color: #dc2626; }

.tc-body { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
.tc-body code {
  font-size: .78rem;
  color: #475569;
  font-family: ui-monospace, monospace;
  word-break: break-all;
}

.tests-note { font-size: .72rem; color: #94a3b8; margin-top: 8px; line-height: 1.4; }

/* transitions */
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* responsive: stack panes */
@media (max-width: 1100px) {
  .panes { grid-template-columns: 1fr; }
  .steps-pane, .console-pane { border: none; border-bottom: 1px solid #ececf1; }
  .pane { overflow: visible; }
  .code { min-height: 320px; }
}
</style>
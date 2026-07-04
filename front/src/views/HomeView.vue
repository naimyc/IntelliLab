<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
const router = useRouter()
const auth = useAuthStore()
const mobileOpen = ref(false)
</script>

<template>
  <div class="home">
    <!-- NAV -->
    <nav class="nav">
      <router-link to="/" class="nav-logo">
        <svg width="32" height="32" viewBox="0 0 100 100" fill="none">
          <circle cx="50" cy="50" r="50" fill="#7c3aed"/>
          <path d="M20 50 C20 28 35 18 50 18 C65 18 80 28 80 50 C80 72 65 82 50 82 C35 82 20 72 20 50Z" stroke="#a78bfa" stroke-width="3" fill="none"/>
          <path d="M50 18 C50 18 35 35 35 50 C35 65 50 82 50 82 C50 82 65 65 65 50 C65 35 50 18 50 18Z" stroke="#c4b5fd" stroke-width="2" fill="none"/>
          <path d="M18 50 L82 50" stroke="#a78bfa" stroke-width="2"/>
          <circle cx="50" cy="50" r="6" fill="#c4b5fd"/>
        </svg>
        <span>IntelliLab</span>
      </router-link>
      <div class="nav-links">
        <a href="#features">Funktionen</a>
        <a href="#how">So funktioniert's</a>
        <router-link v-if="auth.isLoggedIn" to="/dashboard" class="btn-login">Dashboard</router-link>
        <template v-else>
          <router-link to="/login" class="btn-login">Anmelden</router-link>
          <router-link to="/register" class="btn-register">Kostenlos starten</router-link>
        </template>
      </div>
      <!-- Mobile Hamburger -->
      <button class="hamburger" @click="mobileOpen=!mobileOpen">☰</button>
    </nav>
    <!-- Mobile Menu -->
    <div class="mobile-menu" v-if="mobileOpen">
      <a href="#features" @click="mobileOpen=false">Funktionen</a>
      <a href="#how" @click="mobileOpen=false">So funktioniert's</a>
      <router-link v-if="auth.isLoggedIn" to="/dashboard" @click="mobileOpen=false">Dashboard</router-link>
      <template v-else>
        <router-link to="/login" @click="mobileOpen=false">Anmelden</router-link>
        <router-link to="/register" @click="mobileOpen=false">Registrieren</router-link>
      </template>
    </div>

    <!-- HERO -->
    <section class="hero">
      <div class="hero-badge">🎓 KI-gestütztes Lernen für Informatik-Studierende</div>
      <h1>Programmieraufgaben<br/><span class="grad">verstehen. Schritt für Schritt.</span></h1>
      <p class="hero-sub">IntelliLab analysiert deine Programmieraufgaben mit KI, teilt sie in verständliche Schritte auf und begleitet dich beim Lösen — ohne die Antwort zu verraten.</p>
      <div class="hero-btns">
        <router-link to="/register" class="btn-hero-primary">Jetzt kostenlos starten →</router-link>
        <a href="#how" class="btn-hero-ghost">So funktioniert's</a>
      </div>
      <!-- Demo Screenshot -->
      <div class="hero-preview">
        <div class="preview-bar">
          <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
          <span class="preview-title">IntelliLab — Editor</span>
        </div>
        <div class="preview-body">
          <div class="preview-left">
            <div class="plabel">SCHRITTE</div>
            <div class="pstep done"><span class="pnum">✓</span>Klasse Animal erstellen</div>
            <div class="pstep done"><span class="pnum">✓</span>Methode makeSound() hinzufügen</div>
            <div class="pstep active"><span class="pnum">3</span>Unterklasse Dog erstellen</div>
            <div class="pstep"><span class="pnum">4</span>Vererbung implementieren</div>
          </div>
          <div class="preview-code">
            <span class="ck">public class</span> <span class="cc">Dog</span> <span class="ck">extends</span> <span class="cc">Animal</span> {<br/>
            &nbsp;&nbsp;<span class="ca">@Override</span><br/>
            &nbsp;&nbsp;<span class="ck">public void</span> <span class="cf">makeSound</span>() {<br/>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="cc">System</span>.<span class="cf">out</span>.<span class="cf">println</span>(<span class="cs">"Woof!"</span>);<br/>
            &nbsp;&nbsp;}<br/>
            }
          </div>
          <div class="preview-right">
            <div class="plabel">FEEDBACK</div>
            <div class="pfb">✓ Schritt 3 korrekt umgesetzt! Die Klasse Dog erbt korrekt von Animal.</div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="features" id="features">
      <div class="section-head">
        <h2>Alles was du zum Lernen brauchst</h2>
        <p>IntelliLab begleitet dich von der Aufgabe bis zur Lösung</p>
      </div>
      <div class="feat-grid">
        <div class="feat-card">
          <div class="feat-icon">📄</div>
          <h3>Aufgaben hochladen</h3>
          <p>Lade PDFs, Word-Dokumente oder füge Text ein. Die KI erkennt automatisch Programmieraufgaben und ignoriert irrelevante Inhalte.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon">🤖</div>
          <h3>KI-Analyse</h3>
          <p>Die KI teilt jede Aufgabe in sinnvolle Schritte auf, passend zur Programmiersprache — ohne den Code für dich zu schreiben.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon">💻</div>
          <h3>Code-Editor</h3>
          <p>Integrierter Editor mit Syntax-Highlighting für Java, Python, JavaScript und mehr. Erstelle Klassen, Interfaces und Pakete selbst.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon">✅</div>
          <h3>Intelligentes Prüfen</h3>
          <p>Nach dem Abschließen prüft die KI deinen Code, gibt konstruktives Feedback und zeigt was noch fehlt.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon">📈</div>
          <h3>Lernfortschritt</h3>
          <p>Verfolge deine erledigten Projekte und sieh wie sich deine Kenntnisse in verschiedenen Sprachen entwickeln.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon">🔍</div>
          <h3>Analyse Ergebnis</h3>
          <p>Erfahre was du bereits beherrschst, wo du dich verbessern kannst und was du als nächstes lernen solltest.</p>
        </div>
      </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how" id="how">
      <div class="section-head light">
        <h2>So funktioniert IntelliLab</h2>
        <p>In 4 einfachen Schritten zum Lernerfolg</p>
      </div>
      <div class="steps-grid">
        <div class="step-card">
          <div class="step-num">1</div>
          <h3>Aufgabe einreichen</h3>
          <p>Lade deine Programmieraufgabe als PDF hoch oder gib den Text direkt ein. Wähle Sprache und Projektname.</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step-card">
          <div class="step-num">2</div>
          <h3>KI analysiert</h3>
          <p>IntelliLab erkennt die Teilaufgaben und teilt sie in klare, lernförderliche Schritte auf — ohne die Lösung zu verraten.</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step-card">
          <div class="step-num">3</div>
          <h3>Selbst programmieren</h3>
          <p>Du schreibst den Code selbst im eingebauten Editor. Bei Bedarf gibt es Hinweise — aber keine fertigen Antworten.</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step-card">
          <div class="step-num">4</div>
          <h3>Feedback erhalten</h3>
          <p>Nach dem Abschließen bewertet die KI deinen Code und zeigt was gut war und was du noch verbessern kannst.</p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
      <h2>Bereit zum Lernen?</h2>
      <p>Starte jetzt kostenlos und verstehe Programmierung Schritt für Schritt.</p>
      <router-link to="/register" class="btn-hero-primary">Jetzt starten →</router-link>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <div class="footer-logo">
        <svg width="22" height="22" viewBox="0 0 100 100" fill="none">
          <circle cx="50" cy="50" r="50" fill="#7c3aed"/>
          <path d="M20 50 C20 28 35 18 50 18 C65 18 80 28 80 50 C80 72 65 82 50 82 C35 82 20 72 20 50Z" stroke="#a78bfa" stroke-width="3" fill="none"/>
          <circle cx="50" cy="50" r="6" fill="#c4b5fd"/>
        </svg>
        IntelliLab
      </div>
      <p>© 2026 IntelliLab · Hochschule Bochum · Webtechnologien II</p>
    </footer>
  </div>
</template>


<style scoped>
* { box-sizing: border-box; }
.home { min-height:100vh; background:#0f0f1a; color:white; font-family:'Segoe UI',system-ui,sans-serif; }

/* NAV */
.nav { display:flex; align-items:center; justify-content:space-between; padding:16px 60px; background:rgba(15,15,26,.9); backdrop-filter:blur(10px); position:sticky; top:0; z-index:100; border-bottom:1px solid rgba(255,255,255,.07); }
.nav-logo { display:flex; align-items:center; gap:10px; text-decoration:none; font-size:1.1rem; font-weight:700; color:#c4b5fd; }
.nav-links { display:flex; align-items:center; gap:24px; }
.nav-links a { color:#a6adc8; text-decoration:none; font-size:.9rem; transition:color .15s; }
.nav-links a:hover { color:white; }
.btn-login { color:#c4b5fd !important; font-weight:600; }
.btn-register { background:#7c3aed; color:white !important; padding:8px 18px; border-radius:8px; font-weight:600; }
.btn-register:hover { background:#6d28d9 !important; }
.hamburger { display:none; background:none; border:none; color:white; font-size:1.4rem; cursor:pointer; }
.mobile-menu { display:none; flex-direction:column; background:#1a1a2e; padding:16px 24px; gap:14px; }
.mobile-menu a { color:#a6adc8; text-decoration:none; font-size:1rem; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.07); }

/* HERO */
.hero { text-align:center; padding:80px 24px 40px; }
.hero-badge { display:inline-block; background:rgba(124,58,237,.2); border:1px solid rgba(124,58,237,.4); color:#c4b5fd; padding:6px 16px; border-radius:999px; font-size:.82rem; font-weight:600; margin-bottom:24px; }
.hero h1 { font-size:clamp(2rem,5vw,3.5rem); font-weight:800; line-height:1.15; margin:0 0 20px; }
.grad { background:linear-gradient(135deg,#a78bfa,#60a5fa); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.hero-sub { font-size:clamp(.95rem,2vw,1.15rem); color:#a6adc8; max-width:620px; margin:0 auto 32px; line-height:1.7; }
.hero-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; margin-bottom:48px; }
.btn-hero-primary { background:#7c3aed; color:white; padding:13px 28px; border-radius:12px; text-decoration:none; font-weight:700; font-size:.95rem; transition:background .15s; }
.btn-hero-primary:hover { background:#6d28d9; }
.btn-hero-ghost { background:rgba(255,255,255,.08); color:white; padding:13px 28px; border-radius:12px; text-decoration:none; font-weight:600; font-size:.95rem; border:1px solid rgba(255,255,255,.15); transition:background .15s; }
.btn-hero-ghost:hover { background:rgba(255,255,255,.14); }

/* Preview */
.hero-preview { background:#1e1e2e; border-radius:16px; border:1px solid rgba(255,255,255,.1); overflow:hidden; max-width:860px; margin:0 auto; box-shadow:0 30px 80px rgba(0,0,0,.5); }
.preview-bar { background:#181825; padding:12px 16px; display:flex; align-items:center; gap:7px; }
.dot { width:12px; height:12px; border-radius:50%; }
.red{background:#ff5f57;}.yellow{background:#febc2e;}.green{background:#28c840;}
.preview-title { margin-left:auto; margin-right:auto; font-size:.78rem; color:#6c7086; }
.preview-body { display:grid; grid-template-columns:200px 1fr 200px; min-height:200px; }
.preview-left,.preview-right { background:#181825; padding:14px; border-right:1px solid rgba(255,255,255,.07); }
.preview-right { border-right:none; border-left:1px solid rgba(255,255,255,.07); }
.plabel { font-size:.62rem; font-weight:700; letter-spacing:.07em; color:#6c7086; margin-bottom:10px; text-transform:uppercase; }
.pstep { display:flex; gap:8px; align-items:flex-start; font-size:.73rem; color:#6c7086; margin-bottom:8px; line-height:1.4; }
.pstep.done { color:#a6e3a1; }
.pstep.active { color:#cdd6f4; }
.pnum { flex:0 0 18px; height:18px; border-radius:50%; background:#313244; color:#cba6f7; font-size:.65rem; font-weight:700; display:grid; place-items:center; }
.pstep.done .pnum { background:#a6e3a1; color:#1e3a1e; }
.pstep.active .pnum { background:#7c3aed; color:white; }
.preview-code { padding:14px 16px; font-family:ui-monospace,monospace; font-size:.78rem; line-height:1.8; color:#cdd6f4; text-align:left; }
.ck{color:#cba6f7;}.cc{color:#89dceb;}.cf{color:#89b4fa;}.ca{color:#a6e3a1;}.cs{color:#a6e3a1;}
.pfb { font-size:.75rem; color:#a6e3a1; line-height:1.6; margin-top:4px; }

/* FEATURES */
.features { padding:80px 60px; background:#0f0f1a; }
.section-head { text-align:center; margin-bottom:48px; }
.section-head h2 { font-size:clamp(1.5rem,3vw,2.2rem); margin:0 0 12px; }
.section-head p { color:#a6adc8; font-size:1rem; margin:0; }
.section-head.light h2 { color:white; }
.section-head.light p { color:#a6adc8; }
.feat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; max-width:1100px; margin:0 auto; }
.feat-card { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:28px; transition:border-color .2s; }
.feat-card:hover { border-color:rgba(124,58,237,.4); }
.feat-icon { font-size:1.8rem; margin-bottom:14px; }
.feat-card h3 { font-size:1rem; margin:0 0 10px; color:white; }
.feat-card p { font-size:.88rem; color:#a6adc8; line-height:1.6; margin:0; }

/* HOW */
.how { padding:80px 60px; background:#0a0a16; }
.steps-grid { display:flex; align-items:flex-start; gap:12px; max-width:1100px; margin:0 auto; flex-wrap:wrap; justify-content:center; }
.step-card { background:rgba(124,58,237,.1); border:1px solid rgba(124,58,237,.25); border-radius:16px; padding:28px 22px; flex:1; min-width:180px; max-width:220px; text-align:center; }
.step-num { width:40px; height:40px; border-radius:50%; background:#7c3aed; color:white; font-size:1.1rem; font-weight:700; display:grid; place-items:center; margin:0 auto 14px; }
.step-card h3 { font-size:.95rem; margin:0 0 10px; color:white; }
.step-card p { font-size:.82rem; color:#a6adc8; line-height:1.6; margin:0; }
.step-arrow { font-size:1.6rem; color:#7c3aed; align-self:center; padding-top:0; }

/* CTA */
.cta-section { text-align:center; padding:80px 24px; background:linear-gradient(135deg,rgba(124,58,237,.2),rgba(96,165,250,.1)); border-top:1px solid rgba(255,255,255,.07); }
.cta-section h2 { font-size:clamp(1.6rem,3vw,2.2rem); margin:0 0 14px; }
.cta-section p { color:#a6adc8; margin:0 0 28px; }

/* FOOTER */
.footer { padding:24px 60px; background:#0a0a16; border-top:1px solid rgba(255,255,255,.07); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.footer-logo { display:flex; align-items:center; gap:8px; font-weight:700; color:#c4b5fd; font-size:.9rem; }
.footer p { color:#45475a; font-size:.8rem; margin:0; }

/* RESPONSIVE */
@media (max-width:768px) {
  .nav { padding:14px 20px; }
  .nav-links { display:none; }
  .hamburger { display:block; }
  .mobile-menu { display:flex; }
  .features,.how { padding:60px 20px; }
  .hero { padding:60px 20px 32px; }
  .preview-body { grid-template-columns:1fr; }
  .preview-left,.preview-right { display:none; }
  .steps-grid { flex-direction:column; align-items:center; }
  .step-arrow { transform:rotate(90deg); }
  .footer { padding:20px; flex-direction:column; text-align:center; }
}
</style>

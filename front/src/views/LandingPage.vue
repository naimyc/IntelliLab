<template>
  <div class="landing-page">
    <div class="page-shell">
      <header class="topbar">
        <div class="brand">
          <img src="/favicon.svg" alt="IntelliLab" class="logo" />
          <span class="brand-text">IntelliLab</span>
        </div>

        <nav class="main-nav">
          <router-link :to="{ path: '/', hash: '#features' }">Funktionen</router-link>
          <router-link :to="{ path: '/', hash: '#benefits' }">Vorteile</router-link>
          <router-link :to="{ path: '/', hash: '#how-it-works' }">So funktioniert's</router-link>
          <router-link :to="{ path: '/', hash: '#about' }">Über uns</router-link>
        </nav>

        <div class="nav-actions">
          <router-link to="/login" class="nav-link">Anmelden</router-link>
          <router-link to="/register" class="nav-button">Registrieren</router-link>
        </div>
      </header>

      <main class="hero-section">
        <section class="hero-copy">
          <span class="eyebrow">IntelliLab</span>
          <h1>Programmieraufgaben verstehen. Schritt für Schritt.</h1>
          <p>
            IntelliLab hilft dir, Aufgaben zu analysieren, Konzepte zu verstehen und deine Lösungen in klaren
            Schritten aufzubauen – ohne fertige Antworten, aber mit smarter Unterstützung.
          </p>

          <div class="hero-actions">
            <router-link to="/dashboard" class="btn btn-primary">Aufgabe analysieren</router-link>
            <router-link :to="{ path: '/', hash: '#features' }" class="btn btn-secondary">Mehr erfahren</router-link>
          </div>
        </section>

        <section class="hero-visual">
          <div class="visual-card">
            <img :src="heroImg" alt="Illustration" class="hero-image" />
            <div class="assistant-card">
              <div class="assistant-title">IntelliLab Assistant</div>
              <ul class="assistant-list">
                <li>Aufgabe analysieren</li>
                <li>Wichtige Konzepte erkennen</li>
                <li>Schritt-für-Schritt erklären</li>
              </ul>
            </div>
          </div>
        </section>

      </main>

      <section class="features-bar" id="features" aria-label="Features">
        <div class="features-inner">
          <div class="feature-item">
            <div class="icon-circle">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3v10m0 0l-4-4m4 4l4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M5 17h14a1 1 0 0 1 1 1v2H4v-2a1 1 0 0 1 1-1Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="feature-text">
              <div class="feature-title">Aufgaben hochladen</div>
              <div class="feature-sub">PDF oder Text einfügen</div>
            </div>
          </div>

          <div class="feature-item">
            <div class="icon-circle">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="7" r="3" fill="none" stroke="currentColor" stroke-width="2" />
                <path d="M12 10v4m0 0l-3 3m3-3l3 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6 17.5h12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              </svg>
            </div>
            <div class="feature-text">
              <div class="feature-title">KI analysiert</div>
              <div class="feature-sub">Konzepte und Anforderungen erkennen</div>
            </div>
          </div>

          <div class="feature-item">
            <div class="icon-circle">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <use href="/icons.svg#documentation-icon" />
              </svg>
            </div>
            <div class="feature-text">
              <div class="feature-title">Schritt-für-Schritt Hilfe</div>
              <div class="feature-sub">Verständnis statt nur Lösungen</div>
            </div>
          </div>

          <div class="feature-item">
            <div class="icon-circle">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 14l3 3 9-9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 5h6a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="feature-text">
              <div class="feature-title">Besser lernen</div>
              <div class="feature-sub">Nachhaltig verstehen und verbessern</div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import heroImg from "../assets/hero.png";
</script>

<style scoped>
.landing-page {
  min-height: 100vh;
  padding: 2rem 1.5rem 3rem;
  background: radial-gradient(circle at top left, rgba(124, 58, 237, 0.18), transparent 26%),
    radial-gradient(circle at bottom right, rgba(107, 33, 168, 0.14), transparent 22%),
    #07060d;
  color: #f4f4ff;
}

.page-shell {
  width: min(1320px, 100%);
  margin: 0 auto;
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 0 2rem;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.03em;
}

.logo-mark {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 0.85rem;
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  box-shadow: 0 18px 35px rgba(124, 58, 237, 0.25);
}

.logo {
  width: 2.4rem;
  height: 2.4rem;
  display: block;
  filter: brightness(1.3) drop-shadow(0 0 0.35rem rgba(255,255,255,0.2));
}

.brand-text {
  color: #f4f4ff;
  font-weight: 700;
}

.main-nav {
  display: flex;
  align-items: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.main-nav a {
  color: #d8d5f1;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.main-nav a:hover {
  color: #ffffff;
}

.nav-actions {
  display: flex;
  gap: 0.85rem;
  flex-wrap: wrap;
}

.nav-link,
.nav-button,
.btn {
  border: none;
  border-radius: 999px;
  padding: 0.85rem 1.3rem;
  font-weight: 700;
}

.nav-link {
  background: rgba(255, 255, 255, 0.06);
  color: #f4f4ff;
}

.nav-button {
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  color: white;
}

.hero-section {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
  gap: 3rem;
  align-items: center;
}

.hero-copy {
  max-width: 640px;
}

.eyebrow {
  display: inline-flex;
  padding: 0.55rem 1rem;
  border-radius: 999px;
  background: rgba(124, 58, 237, 0.16);
  color: #d8b4fe;
  font-size: 0.9rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 1.4rem;
}

h1 {
  font-size: clamp(3rem, 4vw, 4.75rem);
  line-height: 1.03;
  margin: 0;
  letter-spacing: -0.06em;
  max-width: 12ch;
}

.hero-copy p {
  margin-top: 1.6rem;
  color: #d8d5f1;
  font-size: 1.05rem;
  line-height: 1.85;
  max-width: 40rem;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 2.6rem;
}

.btn-primary {
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  color: white;
  box-shadow: 0 18px 40px rgba(124, 58, 237, 0.22);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.08);
  color: #f4f4ff;
  border: 1px solid rgba(255, 255, 255, 0.12);
}

.hero-visual {
  display: flex;
  justify-content: center;
}

.visual-card {
  width: 100%;
  min-height: 420px;
  border-radius: 2rem;
  padding: 2rem;
  background: rgba(15, 11, 28, 0.96);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 35px 90px rgba(0, 0, 0, 0.35);
  overflow: hidden;
  position: relative;
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  border-radius: 1.2rem;
}

.assistant-card {
  position: absolute;
  right: 2rem;
  top: 2rem;
  max-width: 280px;
  min-width: 240px;
  background: rgba(12, 9, 24, 0.92);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 1.5rem;
  padding: 1.25rem 1.3rem;
  color: #efeaff;
  box-shadow: 0 28px 70px rgba(0, 0, 0, 0.32);
  backdrop-filter: blur(18px);
}
.assistant-card::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  background: linear-gradient(180deg, rgba(124, 58, 237, 0.12), transparent 40%);
  pointer-events: none;
}

.assistant-title {
  font-weight: 800;
  margin-bottom: 0.75rem;
  font-size: 0.95rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: #d8b4fe;
}
.assistant-list {
  margin: 0;
  padding: 0;
  display: grid;
  gap: 0.75rem;
}
.assistant-list li {
  list-style: none;
  padding-left: 2rem;
  position: relative;
  color: #e7e3f8;
  font-size: 0.94rem;
  line-height: 1.5;
}
.assistant-list li::before {
  content: "\2713";
  position: absolute;
  left: 0;
  top: 0.1rem;
  width: 1.2rem;
  height: 1.2rem;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #7c3aed, #8b5cf6);
  color: white;
  font-size: 0.72rem;
  box-shadow: 0 8px 24px rgba(124, 58, 237, 0.18);
}

.feature-svg { width: 28px; height: 28px; display: block; }

.visual-card::before {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top, rgba(124, 58, 237, 0.18), transparent 30%),
    radial-gradient(circle at bottom right, rgba(139, 92, 246, 0.12), transparent 20%);
  pointer-events: none;
}

/* Features bar */
.features-bar {
  margin-top: 2.4rem;
  background: rgba(9, 7, 17, 0.96);
  border-radius: 1.75rem;
  padding: 1.3rem 1.5rem;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.22);
}
.features-inner {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: stretch;
  justify-content: space-between;
}
.feature-item {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex: 1 1 210px;
  min-width: 210px;
}
.icon-circle {
  width: 58px;
  height: 58px;
  border-radius: 18px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(180deg, rgba(124, 58, 237, 0.16), rgba(79, 70, 229, 0.14));
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 18px 48px rgba(124, 58, 237, 0.12);
}
.icon-circle svg {
  width: 26px;
  height: 26px;
  fill: currentColor;
  color: #ffffff;
}
.feature-text {
  min-width: 0;
}
.feature-title {
  font-weight: 700;
  color: #ffffff;
  font-size: 1rem;
}
.feature-sub {
  font-size: 0.9rem;
  color: #cfc6e9;
  line-height: 1.55;
}

@media (max-width: 900px) {
  .features-inner { gap: 1rem; }
  .icon-circle { width: 48px; height: 48px; }
}

.visual-header {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 2rem;
  z-index: 1;
}

.card-label {
  padding: 0.6rem 1rem;
  border-radius: 999px;
  background: rgba(124, 58, 237, 0.18);
  color: #d8b4fe;
  font-weight: 700;
}

.status-dot {
  width: 0.9rem;
  height: 0.9rem;
  border-radius: 999px;
  background: #34d399;
  box-shadow: 0 0 18px rgba(52, 211, 153, 0.3);
}

.visual-screen {
  position: relative;
  z-index: 1;
  display: grid;
  gap: 1rem;
}

.screen-row {
  display: flex;
  gap: 1rem;
}

.screen-block {
  flex: 1;
  min-height: 1.4rem;
  border-radius: 1rem;
  background: rgba(255, 255, 255, 0.08);
}

.screen-block.short {
  width: 55%;
}

.screen-block.layer {
  min-height: 4rem;
  background: rgba(255, 255, 255, 0.06);
}


/* Desktop */
@media (min-width: 1400px) {
  .page-shell { width: 1280px; }
  .hero-copy { max-width: 720px; }
}

/* Laptop (1366px) */
@media (max-width: 1366px) {
  .hero-section { gap: 2.25rem; }
  .visual-card { min-height: 380px; }
}

/* Tablet */
@media (max-width: 1024px) {
  .page-shell { padding: 0 1rem; }
  .hero-section { grid-template-columns: 1fr; gap: 2rem; }
  .visual-card { min-height: 320px; }
  .main-nav { gap: 1rem; }
  .features-inner { justify-content: center; }
}

/* Small tablet / large phone */
@media (max-width: 768px) {
  .topbar { flex-direction: column; align-items: center; gap: 0.6rem; }
  .main-nav { order: 2; display: flex; justify-content: center; flex-wrap: wrap; gap: 0.9rem; }
  .nav-actions { order: 3; }
  .hero-copy { text-align: center; }
  h1 { font-size: clamp(1.8rem, 5vw, 2.6rem); }
  .hero-actions { justify-content: center; }
  .visual-card { min-height: 260px; }
  .features-inner { gap: 0.9rem; }
  .feature-item { min-width: 180px; }
}

/* Phones */
@media (max-width: 420px) {
  .page-shell { padding: 0 0.75rem; }
  .logo-mark { width: 1.8rem; height: 1.8rem; }
  .brand-text { font-size: 0.95rem; }
  .main-nav { display: flex; gap: 0.8rem; font-size: 0.92rem; }
  .hero-copy { padding: 0 0.25rem; }
  h1 { font-size: 1.75rem; line-height: 1.05; }
  .hero-actions { flex-direction: column; gap: 0.75rem; }
  .btn { width: 100%; padding: 0.7rem 1rem; }
  .visual-card { min-height: 220px; padding: 1rem; }
  .feature-item { min-width: 100%; }
  .feature-title { font-size: 0.98rem; }
}

@media (max-width: 360px) {
  h1 { font-size: 1.5rem; }
  .brand-text { font-size: 0.92rem; }
  .nav-button { padding: 0.55rem 0.85rem; }
}
</style>
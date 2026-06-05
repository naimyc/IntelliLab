<template>
  <div class="landing-wrapper">
    <header class="landing-navbar">
      <div class="brand">🧠 IntelliLab</div>

      <nav>
        <a>Funktionen</a>
        <a>Vorteile</a>
        <a>So funktioniert's</a>
        <a>Über uns</a>
      </nav>

      <div class="nav-actions">
        <RouterLink class="outline-btn" to="/login">
          Anmelden
        </RouterLink>

        <RouterLink class="purple-btn" to="/register">
          Registrieren
        </RouterLink>
      </div>
    </header>

    <main class="landing-hero">
      <section class="hero-left">
        <h1>
          Programmieraufgaben verstehen.
          <span>Schritt für Schritt.</span>
        </h1>

        <p>
          IntelliLab nutzt künstliche Intelligenz, um Studierende bei
          Programmieraufgaben zu unterstützen – nicht mit fertigen Lösungen,
          sondern mit Verständnis.
        </p>

        <div class="hero-buttons">
          <RouterLink class="purple-btn" to="/register">
            Jetzt starten
          </RouterLink>

          <RouterLink class="outline-btn" to="/login">
            Mehr erfahren
          </RouterLink>
        </div>
      </section>

      <section class="hero-visual">
        <div class="code-window">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>

        <div class="assistant-card">
          <h3>IntelliLab Assistant</h3>

          <p>
            Ich helfe dir, die Aufgabe zu verstehen.
          </p>

          <ul>
            <li>Aufgabe analysieren</li>
            <li>Wichtige Konzepte erkennen</li>
            <li>Schritt-für-Schritt erklären</li>
            <li>Tipps geben</li>
          </ul>
        </div>
      </section>
    </main>

    <section class="landing-features">
      <div>
        <strong>📤 Aufgaben hochladen</strong>
        <p>PDF oder Text einfügen</p>
      </div>

      <div>
        <strong>🤖 KI analysiert</strong>
        <p>Konzepte und Anforderungen erkennen</p>
      </div>

      <div>
        <strong>🧩 Schritt-für-Schritt Hilfe</strong>
        <p>Verständnis statt fertiger Lösungen</p>
      </div>

      <div>
        <strong>📈 Besser lernen</strong>
        <p>Nachhaltig verstehen und verbessern</p>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from "vue-router";
</script>
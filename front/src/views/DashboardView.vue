<template>
  <div class="dashboard-page">
    <Sidebar />

    <main class="dashboard-main">
      <div class="topbar">
        <div>
          <h1>Willkommen zurück! 👋</h1>
          <p>Bereit für neue Herausforderungen?</p>
        </div>

        <button class="new-task-btn">+ Neue Aufgabe</button>
      </div>

      <section class="stats-grid">
        <StatsCard value="12" title="Aufgaben hochgeladen" />
        <StatsCard value="8" title="Analysen durchgeführt" />
        <StatsCard value="10" title="Stunden gelernt" />
        <StatsCard value="78%" title="Fortschritt" />
      </section>

      <section class="dashboard-content">
        <div class="tasks-section">
          <h2>Meine letzten Aufgaben</h2>

          <TaskCard
            title="Java – Vererbung und Klassen"
            subtitle="hochgeladen am 24.05.2024 · Java"
            status="Analysiert"
          />

          <TaskCard
            title="Python – Listen und Schleifen"
            subtitle="hochgeladen am 22.05.2024 · Python"
            status="Analysiert"
          />

          <TaskCard
            title="Datenbanken – SQL Abfragen"
            subtitle="hochgeladen am 20.05.2024 · SQL"
            status="In Bearbeitung"
          />

          <TaskCard
            title="Java – Interfaces"
            subtitle="hochgeladen am 18.05.2024 · Java"
            status="Analysiert"
          />
        </div>

        <aside class="right-panel">
          <div class="side-card">
            <h3>Lernfortschritt</h3>
            <strong>78%</strong>
            <div class="progress-bar">
              <div class="progress-fill"></div>
            </div>
          </div>

          <div class="side-card">
            <h3>Empfohlene Themen</h3>
            <ul>
              <li>Rekursion verstehen</li>
              <li>Datenbanken Normalisierung</li>
              <li>Dynamische Programmierung</li>
            </ul>
          </div>

          <div class="side-card">
            <h3>Tipp des Tages</h3>
            <p>
              Versuche, die Aufgabe zuerst selbst zu lösen, bevor du dir Hinweise geben lässt. 💡
            </p>
          </div>
        </aside>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import Sidebar from "../components/Sidebar.vue";
import StatsCard from "../components/StatsCard.vue";
import TaskCard from "../components/TaskCard.vue";
</script>
<template>
  <div class="dashboard-page">
    <Sidebar />

    <main class="dashboard-main">
      <div class="topbar">
        <div>
          <h1>Hallo, Nutzer! 👋</h1>
          <p>Bereit für neue Herausforderungen?</p>
        </div>

        <RouterLink class="new-task-btn" to="/new-task">
          + Neue Aufgabe
        </RouterLink>
      </div>

      <section class="stats-grid">
        <StatsCard value="0" title="Aufgaben hochgeladen" />
        <StatsCard value="0" title="Analysen durchgeführt" />
        <StatsCard value="0" title="Stunden gelernt" />
        <StatsCard value="0%" title="Fortschritt" />
      </section>

      <section class="dashboard-content">
        <div class="tasks-section">
          <h2>Meine letzten Aufgaben</h2>

          <TaskCard
            title="Noch keine Aufgaben vorhanden"
            subtitle="Lade deine erste Aufgabe hoch"
            status="Neu"
          />
        </div>

        <aside class="right-panel">
          <div class="side-card">
            <h3>Lernfortschritt</h3>
            <strong>0%</strong>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 0%"></div>
            </div>
          </div>

          <div class="side-card">
            <h3>Empfohlene Themen</h3>
            <ul>
              <li>Java Grundlagen</li>
              <li>SQL Grundlagen</li>
              <li>HTML & CSS</li>
            </ul>
          </div>

          <div class="side-card">
            <h3>Tipp des Tages</h3>
            <p>Lade deine erste Programmieraufgabe hoch und lasse sie analysieren. 💡</p>
          </div>
        </aside>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from "vue-router";
import Sidebar from "../components/Sidebar.vue";
import StatsCard from "../components/StatsCard.vue";
import TaskCard from "../components/TaskCard.vue";
</script>
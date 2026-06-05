<template>
  <aside class="sidebar">
    <div class="logo">🧠 IntelliLab</div>

    <nav class="sidebar-nav">
      <RouterLink class="active" to="/dashboard">🏠 Dashboard</RouterLink>
      <RouterLink to="/tasks">📄 Meine Aufgaben</RouterLink>
      <RouterLink to="/new-task">➕ Neue Aufgabe</RouterLink>
      <RouterLink to="/analytics">📊 Analysen</RouterLink>
      <RouterLink to="/progress">📈 Lernfortschritt</RouterLink>
      <RouterLink to="/profile">👤 Profil</RouterLink>
    </nav>

    <div class="bottom-links">
      <a>⚙️ Einstellungen</a>
      <RouterLink to="/">🚪 Abmelden</RouterLink>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { RouterLink } from "vue-router";
</script>
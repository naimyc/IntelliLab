<template>
  <div class="auth-page">
    <div class="auth-left">
      <div class="auth-brand">🧠 IntelliLab</div>

      <div class="auth-left-content">
        <h1>Willkommen zurück! 👋</h1>
        <p>Melde dich an, um weiterzulernen und deine Aufgaben zu verwalten.</p>

        <div class="auth-illustration">👨‍💻</div>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form">
        <h2>Anmelden</h2>

        <label>E-Mail-Adresse</label>
        <input type="email" placeholder="deine@email.de" />

        <label>Passwort</label>

<input type="password" placeholder="••••••••" />

<div class="forgot-password">
  <RouterLink to="/forgot-password">
    Passwort vergessen?
  </RouterLink>
</div>
        <RouterLink class="auth-button" to="/dashboard">
          Anmelden
        </RouterLink>

        <div class="divider">oder weiter mit</div>

        <button class="google-btn">G Google</button>

        <p>
          Noch kein Konto?
          <RouterLink to="/register">Registrieren</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from "vue-router";
</script>
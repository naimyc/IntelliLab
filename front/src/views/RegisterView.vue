<template>
  <div class="auth-page">
    <div class="auth-left">
      <div class="auth-brand">🧠 IntelliLab</div>

      <div class="auth-left-content">
        <h1>Erstelle dein Konto 🚀</h1>
        <p>Starte jetzt mit IntelliLab und verbessere dein Programmierverständnis.</p>

        <div class="auth-illustration">👨‍💻</div>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form">
        <h2>Registrieren</h2>

        <label>Name</label>
        <input type="text" placeholder="Dein Name" />

        <label>E-Mail-Adresse</label>
        <input type="email" placeholder="deine@email.de" />

        <label>Passwort</label>
        <input type="password" placeholder="Mindestens 8 Zeichen" />

        <label>Passwort bestätigen</label>
        <input type="password" placeholder="Passwort wiederholen" />

        <RouterLink class="auth-button" to="/dashboard">
          Registrieren
        </RouterLink>

        <div class="divider">oder weiter mit</div>

        <button class="google-btn">G Google</button>

        <p>
          Bereits ein Konto?
          <RouterLink to="/login">Anmelden</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { RouterLink } from "vue-router";
</script>
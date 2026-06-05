<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function login() {
  router.push('/dashboard')
}
</script>

<template>

  <div class="login-page">

    <div class="login-card">
<div class="logo">
  🧠 IntelliLab
</div>
      <h1>Anmelden</h1>

      <label>E-Mail-Adresse</label>

      <input
        type="email"
        placeholder="deine@email.de"
      />

      <label>Passwort</label>

      <input
        type="password"
        placeholder="••••••••"
      />

      <div class="forgot-password">
        Passwort vergessen?
      </div>

      <button @click="login">
        Anmelden
      </button>

      <p class="divider">
        oder weiter mit
      </p>

      <button class="google-btn">
        G Google
      </button>

      <p class="register-link">
        Noch kein Konto?
        <router-link to="/register">
          Registrieren
        </router-link>
      </p>

    </div>

  </div>

</template>

<style scoped>

.login-page{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:#f8fafc;
}

.login-card{
  width:420px;
  background:white;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.login-card h1{
  margin-bottom:30px;
}

label{
  display:block;
  margin-bottom:8px;
  font-weight:600;
}

input{
  width:100%;
  padding:14px;
  border:1px solid #ddd;
  border-radius:10px;
  margin-bottom:15px;
}

.forgot-password{
  text-align:right;
  color:#7c3aed;
  font-size:0.9rem;
  margin-bottom:20px;
  cursor:pointer;
}

button{
  width:100%;
  padding:14px;
  border:none;
  border-radius:10px;
  background:#7c3aed;
  color:white;
  cursor:pointer;
  font-size:1rem;
}

button:hover{
  background:#6d28d9;
}

.divider{
  text-align:center;
  margin:20px 0;
  color:#888;
}

.google-btn{
  background:white;
  color:#444;
  border:1px solid #ddd;
}

.google-btn:hover{
  background:#f3f4f6;
}

.register-link{
  text-align:center;
  margin-top:20px;
}

.register-link a{
  color:#7c3aed;
  text-decoration:none;
  font-weight:bold;
}

.logo{
  text-align:center;
  font-size:1.8rem;
  font-weight:bold;
  margin-bottom:25px;
  color:#7c3aed;
}

</style>
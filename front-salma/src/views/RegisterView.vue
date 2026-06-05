<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function register() {
  router.push('/dashboard')
}
</script>

<template>

  <div class="register-page">

    <div class="register-card">
    <div class="logo">
  🧠 IntelliLab
    </div>
      <h1>Registrieren</h1>

      <label>Name</label>
      <input
        type="text"
        placeholder="Dein Name"
      />

      <label>E-Mail-Adresse</label>
      <input
        type="email"
        placeholder="deine@email.de"
      />

      <label>Passwort</label>
      <input
        type="password"
        placeholder="Mindestens 8 Zeichen"
      />

      <label>Passwort bestätigen</label>
      <input
        type="password"
        placeholder="Passwort wiederholen"
      />

      <button @click="register">
        Registrieren
      </button>

      <p class="divider">
        oder weiter mit
      </p>

      <button class="google-btn">
        G Google
      </button>

      <p class="login-link">
        Bereits ein Konto?
        <router-link to="/login">
          Anmelden
        </router-link>
      </p>

    </div>

  </div>

</template>

<style scoped>

.register-page{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:#f8fafc;
}

.register-card{
  width:420px;
  background:white;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.register-card h1{
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

.login-link{
  text-align:center;
  margin-top:20px;
}

.login-link a{
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
<template>
  <nav class="navbar">

    <h2>🧠 IntelliLab</h2>

    <div class="nav-links">

  <router-link to="/">
    Home
  </router-link>

  <router-link to="/login">
    Anmelden
  </router-link>

  <router-link to="/register">
    Registrieren
  </router-link>

</div>

  </nav>
</template>

<style scoped>

.navbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 40px;
  background:#0f172a;
  color:white;
}

.nav-links{
  display:flex;
  gap:20px;
}

.nav-links a{
  color:white;
  text-decoration:none;
}

.nav-links a:hover{
  color:#a855f7;
}

</style>
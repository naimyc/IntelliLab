<template>

  <div class="sidebar">

    <h2>🧠 IntelliLab</h2>

    <ul>

      <li>
        <router-link to="/dashboard">
          Dashboard
        </router-link>
      </li>

      <li>
        <router-link to="/tasks">
          Meine Aufgaben
        </router-link>
      </li>

      <li>
        <router-link to="/upload">
          Neue Analyse
        </router-link>
      </li>

      <li>
        <router-link to="/profile">
          Profil
        </router-link>
      </li>

      <li>
        <router-link to="/analysis">
        Analyse Ergebnis
        </router-link>
      </li>

    </ul>

  </div>

</template>

<style scoped>

.sidebar{
  width:250px;
  min-height:100vh;
  background:#0f172a;
  color:white;
  padding:30px;
}

.sidebar h2{
  margin-bottom:40px;
}

.sidebar ul{
  list-style:none;
}

.sidebar li{
  margin-bottom:20px;
}

.sidebar a{
  color:white;
  text-decoration:none;
}

.sidebar a:hover{
  color:#a855f7;
}

.router-link-active{
  color:#a855f7 !important;
  font-weight:bold;
}

</style>
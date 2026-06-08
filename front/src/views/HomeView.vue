<script setup>
import Navbar from '../components/Navbar.vue'
</script>
<template>
  <div class="home">
    <Navbar />

    <section class="hero">
      <h1>Programmieraufgaben verstehen. Schritt für Schritt.</h1>

      <p>
        IntelliLab unterstützt Informatik-Studierende mit KI beim
        Verstehen und Lösen von Programmieraufgaben.
      </p>

      <button class="cta">
        Aufgabe analysieren
      </button>
    </section>
    <section class="features">

  <div class="card">
    <h3>📄 Aufgaben hochladen</h3>
    <p>PDFs oder Aufgabentexte direkt analysieren.</p>
  </div>

  <div class="card">
    <h3>🤖 KI Analyse</h3>
    <p>Wichtige Konzepte und Anforderungen erkennen.</p>
  </div>

  <div class="card">
    <h3>🧠 Schritt-für-Schritt Hilfe</h3>
    <p>Verständliche Erklärungen statt fertiger Lösungen.</p>
  </div>

  <div class="card">
    <h3>📈 Lernfortschritt</h3>
    <p>Eigene Fortschritte verfolgen und verbessern.</p>
  </div>

</section>
  </div>
</template>

<style scoped>

.home{
  min-height:100vh;
  background: linear-gradient(135deg,#0f172a,#1e1b4b);
  color:white;
}

.navbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 60px;
}

.nav-buttons{
  display:flex;
  gap:15px;
}

.nav-buttons button{
  padding:10px 20px;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.register{
  background:#7c3aed;
  color:white;
}

.hero{
  text-align:center;
  margin-top:120px;
  padding:0 20px;
}

.hero h1{
  font-size:3rem;
  max-width:900px;
  margin:auto;
}

.hero p{
  margin-top:20px;
  font-size:1.2rem;
  color:#d1d5db;
}

.cta{
  margin-top:30px;
  padding:15px 30px;
  background:#7c3aed;
  border:none;
  border-radius:10px;
  color:white;
  font-size:1rem;
  cursor:pointer;
}
.features{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
  gap:25px;
  padding:60px;
}

.card{
  background:rgba(255,255,255,0.08);
  padding:25px;
  border-radius:16px;
  backdrop-filter:blur(10px);
}

.card h3{
  margin-bottom:10px;
}

.card p{
  color:#d1d5db;
}

</style>
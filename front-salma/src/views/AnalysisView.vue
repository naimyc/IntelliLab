<script setup>
import Sidebar from '../components/Sidebar.vue'
</script>

<template>

  <div class="analysis-page">

    <Sidebar />

    <div class="content">

      <div class="analysis-card">

        <h1>Analyse Ergebnis</h1>

        <div class="section">
          <h2>📚 Erkannte Themen</h2>
          <ul>
            <li>Java Klassen</li>
            <li>Vererbung</li>
            <li>Konstruktoren</li>
          </ul>
        </div>

        <div class="section">
          <h2>🧠 Benötigte Konzepte</h2>
          <ul>
            <li>Objektorientierte Programmierung</li>
            <li>Methoden überschreiben</li>
            <li>Attribute und Objekte</li>
          </ul>
        </div>

        <div class="section">
          <h2>🚀 Empfohlene Schritte</h2>

          <ol>
            <li>Basisklasse erstellen</li>
            <li>Unterklasse anlegen</li>
            <li>Konstruktor implementieren</li>
            <li>Methoden überschreiben</li>
          </ol>
        </div>

      </div>

    </div>

  </div>

</template>

<style scoped>

.analysis-page{
  min-height:100vh;
  display:flex;
  background:#f8fafc;
}

.content{
  flex:1;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:40px;
}

.analysis-card{
  width:900px;
  background:white;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.section{
  margin-top:30px;
}

.section h2{
  margin-bottom:15px;
}

.section ul,
.section ol{
  margin-left:20px;
}

.section li{
  margin-bottom:10px;
}

</style>
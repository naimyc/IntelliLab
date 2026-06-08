<script setup>
import StatCard from '../components/StatCard.vue'
import Sidebar from '../components/Sidebar.vue'
</script>

<template>
  <div class="dashboard">

    <Sidebar />

    <div class="content">

      <h1>Willkommen zurück!</h1>

      <p>
        Hier findest du deine aktuellen Aufgaben und Analysen.
      </p>

      <div class="cards">

        <StatCard
          title="📄 Aufgaben"
          value="0"
        />

        <StatCard
          title="🤖 Analysen"
          value="0"
        />

        <StatCard
          title="🏆 Fortschritt"
          value="0%"
        />

      </div>

      <div class="tasks-section">

        <h2>Meine letzten Aufgaben</h2>

        <div class="task-item">
          <span>Java Vererbung</span>
          <span class="status done">Analysiert</span>
        </div>

        <div class="task-item">
          <span>Python Listen</span>
          <span class="status progress">In Bearbeitung</span>
        </div>

        <div class="task-item">
          <span>SQL Joins</span>
          <span class="status done">Analysiert</span>
        </div>

      </div>

    </div>

  </div>
</template>

<style scoped>

.dashboard{
  min-height:100vh;
  display:flex;
}

.content{
  flex:1;
  padding:40px;
  background:#f8fafc;
}

.cards{
  margin-top:30px;
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:20px;
}

.tasks-section{
  margin-top:40px;
}

.tasks-section h2{
  margin-bottom:20px;
}

.task-item{
  background:white;
  padding:18px 25px;
  margin-bottom:15px;
  border-radius:12px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 5px 15px rgba(0,0,0,.05);
}

.status{
  padding:6px 12px;
  border-radius:20px;
  font-size:0.9rem;
}

.done{
  background:#dcfce7;
  color:#166534;
}

.progress{
  background:#fef3c7;
  color:#92400e;
}

</style>
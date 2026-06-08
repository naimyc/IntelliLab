<script setup>
import Sidebar from '../components/Sidebar.vue'
</script>

<template>

  <div class="tasks-page">

    <Sidebar />

    <div class="content">

      <div class="tasks-card">

        <h1>📚 Meine Aufgaben</h1>

        <p>
          Hier findest du alle deine bisherigen Analysen.
        </p>

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

        <div class="task-item">
          <span>C++ Klassen</span>
          <span class="status progress">In Bearbeitung</span>
        </div>

      </div>

    </div>

  </div>

</template>

<style scoped>

.tasks-page{
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

.tasks-card{
  width:900px;
  background:white;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.tasks-card h1{
  margin-bottom:10px;
}

.tasks-card p{
  margin-bottom:30px;
}

.task-item{
  background:#f8fafc;
  padding:20px;
  border-radius:12px;
  margin-bottom:15px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.status{
  padding:8px 15px;
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
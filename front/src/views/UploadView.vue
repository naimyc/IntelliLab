<script setup>
import Sidebar from '../components/Sidebar.vue'
import { useRouter } from 'vue-router'

const router = useRouter()

function analyze() {
  router.push('/analysis')
}
</script>

<template>

  <div class="upload-page">

    <Sidebar />

    <div class="content">

      <div class="upload-card">

        <h1>Neue Analyse</h1>

        <p>
          Lade eine Aufgabe hoch oder füge den Aufgabentext direkt ein.
        </p>

        <div class="upload-box">
          📄 PDF auswählen
        </div>

        <p class="oder">ODER</p>

        <textarea
          placeholder="Füge hier deine Programmieraufgabe ein..."
        ></textarea>

        <button @click="analyze">
          Analysieren
        </button>

      </div>

    </div>

  </div>

</template>

<style scoped>

.upload-page{
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

.upload-card{
  width:700px;
  background:white;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.1);
}

.upload-card h1{
  margin-bottom:10px;
}

.upload-box{
  margin-top:25px;
  border:2px dashed #7c3aed;
  border-radius:16px;
  padding:40px;
  text-align:center;
  cursor:pointer;
}

.oder{
  text-align:center;
  margin:20px 0;
}

textarea{
  width:100%;
  height:180px;
  padding:15px;
  border-radius:12px;
  border:1px solid #ddd;
  resize:none;
}

button{
  width:100%;
  margin-top:20px;
  padding:15px;
  border:none;
  border-radius:12px;
  background:#7c3aed;
  color:white;
  font-size:1rem;
  cursor:pointer;
}

button:hover{
  background:#6d28d9;
}

</style>
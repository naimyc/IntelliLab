<script setup>
import Sidebar from '../components/Sidebar.vue'
</script>

<template>

  <div class="profile-page">

    <Sidebar />

    <div class="content">

      <div class="profile-card">

        <div class="avatar">
          👩‍🎓
        </div>

        <h1>Mein Profil</h1>

        <div class="info">
          <label>Name</label>
          <p>Max Mustermann</p>
        </div>

        <div class="info">
          <label>E-Mail</label>
          <p>max.mustermann@intellilab.de</p>
        </div>

        <div class="info">
          <label>Studiengang</label>
          <p>Informatik</p>
        </div>

        <div class="info">
          <label>Analysen durchgeführt</label>
          <p>0</p>
        </div>

        <div class="info">
          <label>Mitglied seit</label>
          <p>Juni 2026</p>
        </div>

        <div class="info">
          <label>Rolle</label>
          <p>Student</p>
        </div>

        <button
          @click="alert('Profil bearbeiten wird später mit dem Backend umgesetzt.')"
        >
          Profil bearbeiten
        </button>

      </div>

    </div>

  </div>

</template>

<style scoped>

.profile-page{
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

.profile-card{
  width:600px;
  background:white;
  padding:40px;
  border-radius:20px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.avatar{
  font-size:70px;
  text-align:center;
  margin-bottom:20px;
}

.profile-card h1{
  text-align:center;
  margin-bottom:30px;
}

.info{
  margin-bottom:20px;
}

.info label{
  display:block;
  font-weight:bold;
  color:#666;
  margin-bottom:5px;
}

.info p{
  font-size:1.1rem;
}

button{
  margin-top:20px;
  padding:12px 20px;
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

</style>
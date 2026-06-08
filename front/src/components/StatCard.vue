<template>
  <div class="card">

    <h3>{{ title }}</h3>

    <p>{{ value }}</p>

  </div>
</template>

<script setup>

defineProps({
  title: String,
  value: String
})

</script>

<style scoped>

.card{
  background:white;
  padding:25px;
  border-radius:16px;
  box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.card p{
  font-size:2rem;
  margin-top:10px;
}

</style>
<script setup>
import Sidebar from './Sidebar.vue'
</script>

<template>
  <div class="app-layout">
    <Sidebar />
    <div class="app-main">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background:var(--il-bg);
}

/* Desktop: sidebar a sinistra */
@media (min-width: 768px) {
  .app-layout {
    flex-direction: row;
  }
}

.app-main {
  flex: 1;
  min-width: 0;
  overflow-x: hidden;
}
</style>

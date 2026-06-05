<template>
  <div class="task-card">
    <div class="task-icon">📄</div>

    <div class="task-info">
      <h3>{{ title }}</h3>
      <p>{{ subtitle }}</p>
    </div>

    <span class="status">{{ status }}</span>
    <span class="arrow">›</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  subtitle: string
  status: string
}>()
</script>
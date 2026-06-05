<template>
  <div class="stats-card">
    <strong>{{ value }}</strong>
    <span>{{ title }}</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  value: string;
  title: string;
}>();
</script>
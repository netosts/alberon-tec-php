<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <HomeLayoutHeader />
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import HomeLayoutHeader from './HomeLayoutHeader.vue'
</script>

<style scoped></style>

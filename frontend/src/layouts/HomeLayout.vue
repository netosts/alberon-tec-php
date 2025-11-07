<template>
  <div class="min-h-screen bg-linear-to-br from-slate-900 via-slate-800 to-slate-900">
    <HomeLayoutHeader />
    <main class="mx-auto max-w-6xl px-6 py-12">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import HomeLayoutHeader from './HomeLayoutHeader.vue'
</script>

<style scoped></style>

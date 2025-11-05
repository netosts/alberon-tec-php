<template>
  <header class="border-b border-slate-700 bg-slate-900/50 backdrop-blur-sm">
    <div class="mx-auto max-w-6xl px-6 py-6">
      <div class="flex items-center gap-3">
        <img src="/logo.png" class="rounded-lg w-10 h-10 object-fit" />
        <h1 class="text-2xl font-bold text-white">{{ appName }}</h1>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import packageJson from '../../package.json'

const appName = packageJson.displayName || packageJson.name
</script>

<style scoped></style>

<template>
  <header class="border-b border-slate-700 bg-slate-900/50 backdrop-blur-sm">
    <div class="mx-auto max-w-6xl px-6 py-6">
      <div class="flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600"
        >
          <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8m0 8l-4-2m4 2l4-2"
            />
          </svg>
        </div>
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

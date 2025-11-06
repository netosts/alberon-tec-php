<template>
  <div class="rounded-xl border border-slate-700 bg-slate-800/50 p-8 backdrop-blur-sm">
    <h2 class="mb-6 text-xl font-bold text-white">
      <span
        class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-600 text-sm font-semibold text-white mr-3"
        >✓</span
      >
      Import Summary
    </h2>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-lg bg-slate-700/50 p-6">
        <p class="text-sm text-slate-400 mb-2">Total Rows</p>
        <p class="text-3xl font-bold text-white">{{ props.summary.total_rows }}</p>
      </div>
      <div class="rounded-lg bg-green-900/20 border border-green-700/50 p-6">
        <p class="text-sm text-green-300 mb-2">Successfully Imported</p>
        <p class="text-3xl font-bold text-green-400">{{ props.summary.imported }}</p>
      </div>
      <div class="rounded-lg bg-orange-900/20 border border-orange-700/50 p-6">
        <p class="text-sm text-orange-300 mb-2">Duplicates</p>
        <p class="text-3xl font-bold text-orange-400">{{ props.summary.duplicates }}</p>
      </div>
      <div class="rounded-lg bg-red-900/20 border border-red-700/50 p-6">
        <p class="text-sm text-red-300 mb-2">Errors</p>
        <p class="text-3xl font-bold text-red-400">{{ props.summary.errors }}</p>
      </div>
    </div>

    <!-- <button
        @click="reset"
        class="mt-6 rounded-lg bg-slate-700 px-6 py-2 font-semibold text-white hover:bg-slate-600 transition-colors"
      >
        Import Another File
      </button> -->
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  summary: {
    total_rows: number
    imported: number
    duplicates: number
    errors: number
  }
}>()
</script>

<style scoped></style>

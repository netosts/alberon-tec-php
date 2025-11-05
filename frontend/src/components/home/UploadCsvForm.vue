<template>
  <div class="rounded-xl border border-slate-700 bg-slate-800/50 p-8 backdrop-blur-sm">
    <h2 class="mb-2 text-xl font-bold text-white">
      <span
        class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white mr-3"
        >1</span
      >
      Upload CSV File
    </h2>
    <p class="mb-6 text-slate-400">Import contacts from your CSV file</p>

    <div class="space-y-4">
      <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="handleDrop"
        :class="[
          'rounded-lg border-2 border-dashed p-8 text-center transition-colors',
          dragging
            ? 'border-blue-500 bg-blue-500/10'
            : 'border-slate-600 bg-slate-700/30 hover:border-slate-500',
        ]"
      >
        <svg
          class="mx-auto mb-4 h-12 w-12 text-slate-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
          />
        </svg>
        <p class="mb-2 text-white font-semibold">Drag and drop your CSV file here</p>
        <p class="mb-4 text-sm text-slate-400">or click to select</p>
        <input
          ref="fileInput"
          type="file"
          accept=".csv"
          @change="handleFileSelect"
          class="hidden"
        />
        <button
          @click="$refs.fileInput.click()"
          class="inline-block rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 transition-colors"
        >
          Select File
        </button>
      </div>

      <div
        v-if="selectedFile"
        class="flex items-center justify-between rounded-lg bg-slate-700/30 p-4"
      >
        <div class="flex items-center gap-3">
          <Icon name="file-type-csv" class="text-green-400" />
          <span class="text-white font-medium">{{ selectedFile.name }}</span>
          <span class="text-sm text-slate-400"
            >({{ (selectedFile.size / 1024).toFixed(2) }} KB)</span
          >
        </div>
        <button @click="resetForm" class="text-slate-400 hover:text-red-400 transition-colors">
          ✕
        </button>
      </div>

      <button
        @click="processFile"
        :disabled="!selectedFile || processing"
        :class="[
          'w-full rounded-lg py-3 font-semibold transition-all duration-200',
          selectedFile && !processing
            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 cursor-pointer'
            : 'bg-slate-700 text-slate-500 cursor-not-allowed',
        ]"
      >
        <span v-if="!processing">Process File</span>
        <span v-else class="flex items-center justify-center gap-2">
          <svg class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          Processing...
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Icon from '../Icon.vue'

const selectedFile = ref<File | null>(null)
const dragging = ref(false)
const processing = ref(false)

const fileInput = ref(null)

const handleFileSelect = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file && file.type === 'text/csv') {
    selectedFile.value = file
  }
}

const handleDrop = (event: DragEvent) => {
  dragging.value = false
  const file = event.dataTransfer?.files?.[0]
  if (file && file.type === 'text/csv') {
    selectedFile.value = file
  }
}

const resetForm = () => {
  selectedFile.value = null
  processing.value = false
}
</script>

<style scoped></style>

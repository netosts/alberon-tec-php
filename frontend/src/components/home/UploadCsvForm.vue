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
        <Icon name="cloud-up" class="text-slate-400 mb-4" size="w-16 h-16" />
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
          class="inline-block cursor-pointer rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700 transition-colors"
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
          <Icon name="loader-2" class="animate-spin" />
          Processing...
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Icon from '../Icon.vue'
import { uploadCsvContactService } from '@/services/home/homeApi'

const emit = defineEmits<{
  onUpload: [data: any]
}>()

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

const processFile = () => {
  if (!selectedFile.value) return

  processing.value = true

  const formData = new FormData()
  formData.append('file', selectedFile.value)
  formData.append('_method', 'PUT')

  uploadCsvContactService(formData)
    .then(({ data: { data } }) => {
      alert('File processed successfully!')
      emit('onUpload', data)
    })
    .catch(() => {
      alert('Error processing file. Please try again.')
    })
    .finally(() => {
      processing.value = false
    })
}
</script>

<style scoped></style>

<template>
  <div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload CSV File</h2>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2"> Select CSV File </label>
        <input
          ref="fileInput"
          type="file"
          accept=".csv"
          @change="handleFileChange"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
        />
      </div>

      <button
        type="submit"
        :disabled="!file || loading"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
      >
        {{ loading ? 'Uploading...' : 'Upload and Process' }}
      </button>
    </form>

    <!-- Summary -->
    <div v-if="summary" class="mt-6 p-4 bg-green-50 rounded-md">
      <h3 class="text-lg font-semibold text-green-900 mb-3">Import Summary</h3>
      <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
          <span class="text-gray-600">Total Rows:</span>
          <span class="ml-2 font-semibold">{{ summary.total_rows }}</span>
        </div>
        <div>
          <span class="text-gray-600">Successfully Imported:</span>
          <span class="ml-2 font-semibold text-green-600">{{ summary.imported }}</span>
        </div>
        <div>
          <span class="text-gray-600">Duplicates:</span>
          <span class="ml-2 font-semibold text-yellow-600">{{ summary.duplicates }}</span>
        </div>
        <div>
          <span class="text-gray-600">Validation Errors:</span>
          <span class="ml-2 font-semibold text-red-600">{{ summary.errors }}</span>
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-if="error" class="mt-4 p-4 bg-red-50 rounded-md">
      <p class="text-sm text-red-800">{{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface ImportStats {
  total_rows: number
  imported: number
  duplicates: number
  errors: number
}

const emit = defineEmits<{
  (e: 'upload-success'): void
}>()

const file = ref<File | null>(null)
const loading = ref(false)
const summary = ref<ImportStats | null>(null)
const error = ref<string | null>(null)
const fileInput = ref<HTMLInputElement>()

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  file.value = target.files?.[0] || null
  summary.value = null
  error.value = null
}

const handleSubmit = async () => {
  if (!file.value) return

  loading.value = true
  error.value = null
  summary.value = null

  try {
    const formData = new FormData()
    formData.append('file', file.value)

    const response = await fetch('http://localhost:8000/api/contacts/upload', {
      method: 'POST',
      body: formData,
    })

    if (!response.ok) {
      throw new Error('Upload failed')
    }

    const data = await response.json()
    summary.value = data.stats

    // Reset file input
    if (fileInput.value) {
      fileInput.value.value = ''
    }
    file.value = null

    // Notify parent component
    emit('upload-success')
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'An error occurred during upload'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="rounded-xl border border-slate-700 bg-slate-800/50 p-8 backdrop-blur-sm">
    <ContactImportSummaryTitle :status="props.status" />

    <template v-if="isProcessing">
      <div class="space-y-2 mb-4">
        <div class="flex justify-between items-center">
          <p class="text-sm text-slate-400">File Processing Progress</p>
          <p class="text-sm font-semibold text-blue-400">{{ props.progress.toFixed(2) }}%</p>
        </div>
        <div class="h-2 rounded-full bg-slate-700/50">
          <div
            class="h-full rounded-full bg-linear-to-r from-blue-500 to-indigo-600 transition-all duration-300"
            :style="{ width: props.progress + '%' }"
          ></div>
        </div>
      </div>
      <!-- Chunks Progress -->
      <div class="mb-4 rounded-lg bg-blue-900/20 border border-blue-700/50 p-4">
        <p class="text-sm text-blue-300">
          Processing chunk {{ props.processedChunks }} of {{ props.totalChunks }}
        </p>
      </div>
    </template>

    <div
      v-else-if="isError && !isProcessing"
      class="rounded-lg bg-red-900/20 border border-red-700/50 p-4 mb-6"
    >
      <p class="text-sm text-red-300 font-semibold mb-2">Error Details</p>
      <p class="text-sm text-red-200">
        The import process encountered errors. Please review the statistics below and try again.
      </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
      <div class="rounded-lg bg-slate-700/50 p-6">
        <p class="text-sm text-slate-400 mb-2">Total Rows</p>
        <p class="text-3xl font-bold text-white">{{ props.stats.total_rows }}</p>
      </div>
      <div class="rounded-lg bg-green-900/20 border border-green-700/50 p-6">
        <p class="text-sm text-green-300 mb-2">Successfully Imported</p>
        <p class="text-3xl font-bold text-green-400">{{ props.stats.imported }}</p>
      </div>
      <div class="rounded-lg bg-orange-900/20 border border-orange-700/50 p-6">
        <p class="text-sm text-orange-300 mb-2">Duplicates</p>
        <p class="text-3xl font-bold text-orange-400">{{ props.stats.duplicates }}</p>
      </div>
      <div class="rounded-lg bg-red-900/20 border border-red-700/50 p-6">
        <p class="text-sm text-red-300 mb-2">Errors</p>
        <p class="text-3xl font-bold text-red-400">{{ props.stats.errors }}</p>
      </div>
    </div>

    <div v-if="isError && !isProcessing" class="space-y-3 mb-6">
      <p class="text-sm text-slate-400 font-semibold">Common Issues:</p>
      <div class="space-y-2">
        <ContactImportIssueItem
          v-for="(item, itemId) in issueItems"
          :key="itemId"
          :text="item.text"
          :iconName="item.iconName"
          :iconClass="item.iconClass"
        />
      </div>
    </div>

    <button
      v-if="!isProcessing"
      @click="emit('reset')"
      class="mt-6 cursor-pointer rounded-lg bg-slate-700 px-6 py-2 font-semibold text-white hover:bg-slate-600 transition-colors"
    >
      Import Another File
    </button>
  </div>
</template>

<script setup lang="ts">
import type { CsvImportDataStats } from '@/composables/useCsvImport'
import { CsvImportStatus } from '@/enums/csv-import.enum'
import { computed, ref } from 'vue'
import ContactImportSummaryTitle from './ContactImportSummaryTitle.vue'
import ContactImportIssueItem from './ContactImportIssueItem.vue'

const props = defineProps<{
  status: CsvImportStatus
  progress: number
  stats: CsvImportDataStats
  processedChunks: number
  totalChunks: number
}>()

const emit = defineEmits<{
  reset: []
}>()

const issueItems = ref([
  {
    text: 'Check that all rows have valid email addresses (e.g., user@example.com)',
    iconName: 'exclamation-circle',
    iconClass: 'text-red-400',
  },
  {
    text: 'Verify that the name field is present and not empty (max 255 characters)',
    iconName: 'exclamation-circle',
    iconClass: 'text-red-400',
  },
  {
    text: 'Ensure phone numbers are in international format (e.g., +1-555-123-4567, (555) 123-4567)',
    iconName: 'exclamation-circle',
    iconClass: 'text-red-400',
  },
  {
    text: 'Ensure birthdates are valid dates in the past (e.g., 1990-01-15)',
    iconName: 'exclamation-circle',
    iconClass: 'text-red-400',
  },
  {
    text: 'Duplicate email addresses will be automatically skipped',
    iconName: 'alert-triangle',
    iconClass: 'text-orange-400',
  },
])

const isError = computed(() => {
  return props.stats.errors && Number(props.stats.errors) > 0
})

const isProcessing = computed(() => props.status === CsvImportStatus.PROCESSING)
</script>

<style scoped></style>

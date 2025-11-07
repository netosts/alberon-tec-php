<template>
  <div class="space-y-8">
    <UploadCsvForm v-if="status === CsvImportStatus.IDLE" @onUpload="showResults" />

    <ContactImportSummary
      v-else
      :status="status"
      :progress="progress"
      :stats="stats"
      :processedChunks="processedChunks"
      :totalChunks="totalChunks"
      @reset="reset"
    />

    <ContactPagination :status="status" />
  </div>
</template>

<script setup lang="ts">
import ContactImportSummary from '@/components/contact/ContactImportSummary.vue'
import ContactPagination from '@/components/contact/ContactPagination.vue'
import UploadCsvForm from '@/components/contact/UploadCsvForm.vue'
import { useCsvImport } from '@/composables/useCsvImport'
import { CsvImportStatus } from '@/enums/csv-import.enum'

const { progress, stats, status, subscribeToImport, processedChunks, totalChunks, reset } =
  useCsvImport()

const showResults = (data: any) => {
  status.value = CsvImportStatus.PROCESSING
  progress.value = 0

  if (data.import_id) {
    subscribeToImport(data.import_id)
  }
}
</script>

<style scoped></style>

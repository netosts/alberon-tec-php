<template>
  <div class="space-y-8">
    <UploadCsvForm @onUpload="showResults" />

    <ContactImportSummary v-if="results.summary.total_rows > 0" :summary="results.summary" />

    <ContactPagination />
  </div>
</template>

<script setup lang="ts">
import ContactImportSummary from '@/components/home/ContactImportSummary.vue'
import ContactPagination from '@/components/home/ContactPagination.vue'
import UploadCsvForm from '@/components/home/UploadCsvForm.vue'
import { ref } from 'vue'

const results = ref<any>({
  summary: {
    total_rows: 0,
    imported: 0,
    duplicates: 0,
    errors: 0,
  },
})

const showResults = (newResults: any) => {
  results.value.summary = newResults
}
</script>

<style scoped></style>

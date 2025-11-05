<template>
  <div>
    <UploadCsvForm @onUpload="showResults" />
  </div>
</template>

<script setup lang="ts">
import UploadCsvForm from '@/components/home/UploadCsvForm.vue'

const showResults = (results: any) => {
  console.log('CSV Upload Results:', results)
}
</script>

<style scoped></style>

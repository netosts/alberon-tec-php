<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">CSV Contact Importer</h1>
        <p class="text-gray-600">Upload a CSV file to import contacts</p>
      </div>

      <UploadForm @upload-success="handleUploadSuccess" />

      <ContactList :key="contactsKey" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import UploadForm from '@/components/UploadForm.vue'
import ContactList from '@/components/ContactList.vue'

const contactsKey = ref(0)

const handleUploadSuccess = () => {
  contactsKey.value++
}
</script>

<style scoped></style>

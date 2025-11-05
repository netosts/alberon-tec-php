<template>
  <div>
    <UploadCsvForm />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import UploadForm from '@/components/UploadForm.vue'
import ContactList from '@/components/ContactList.vue'
import UploadCsvForm from '@/components/home/UploadCsvForm.vue'

const contactsKey = ref(0)

const handleUploadSuccess = () => {
  contactsKey.value++
}
</script>

<style scoped></style>

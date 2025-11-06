<template>
  <h2 class="mb-6 text-xl font-bold text-white">
    <span
      class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold mr-3"
      :class="componentData.class"
    >
      <Icon :key="componentData.icon" :name="componentData.icon" size="sm" />
    </span>
    {{ componentData.title }}
  </h2>
</template>

<script setup lang="ts">
import { CsvImportStatus } from '@/enums/csv-import.enum'
import { computed } from 'vue'
import Icon from '../Icon.vue'

const props = defineProps<{
  status: CsvImportStatus
}>()

const componentData = computed(() => {
  if (props.status === CsvImportStatus.FAILED) {
    return {
      class: 'bg-red-600',
      textColor: 'text-white',
      title: 'Import Failed',
      icon: 'x',
    }
  }

  if (props.status === CsvImportStatus.PROCESSING) {
    return {
      class: 'bg-blue-600 animate-spin',
      textColor: 'text-white',
      title: 'Processing Import',
      icon: 'loader-2',
    }
  }

  return {
    class: 'bg-green-600',
    textColor: 'text-white',
    title: 'Import Summary',
    icon: 'check',
  }
})
</script>

<style scoped></style>

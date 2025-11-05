<template>
  <component :is="layout">
    <RouterView />
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import EmptyLayout from './layouts/EmptyLayout.vue'
import HomeLayout from './layouts/HomeLayout.vue'

const route = useRoute()

const layouts: Record<string, any> = {
  HomeLayout,
  EmptyLayout,
}

const layout = computed(() => {
  const layoutName = (route.meta.layout as string) || 'EmptyLayout'
  return layouts[layoutName] || EmptyLayout
})
</script>

<style scoped></style>

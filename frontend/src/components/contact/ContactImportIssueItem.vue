<template>
  <div class="flex items-center gap-3 rounded-lg bg-slate-700/30 p-3">
    <Icon :name="props.iconName ?? ''" class="h-5 w-5 shrink-0 mt-0.5" :class="props.iconClass" />
    <span class="text-sm text-slate-300">
      {{ props.text }}
    </span>
  </div>
</template>

<script setup lang="ts">
import Icon from '../Icon.vue'

const props = defineProps<{
  text: string
  iconName?: string
  iconClass?: string
}>()
</script>

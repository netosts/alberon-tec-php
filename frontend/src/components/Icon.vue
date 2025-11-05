<template>
  <span :class="[sizeClass, 'inline-block']">
    <component :is="iconComponent" />
  </span>
</template>

<script setup lang="ts">
import { computed, defineAsyncComponent } from 'vue'

type IconSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl'

interface Props {
  name: string
  size?: IconSize | string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
})

const sizeMap: Record<IconSize, string> = {
  xs: 'w-3 h-3',
  sm: 'w-4 h-4',
  md: 'w-6 h-6',
  lg: 'w-8 h-8',
  xl: 'w-12 h-12',
}

// Convert name to kebab-case if not already
const iconName = computed(() => {
  return props.name
    .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
    .replace(/([A-Z])([A-Z])(?=[a-z])/g, '$1-$2')
    .toLowerCase()
})

const iconComponent = computed(() => {
  return defineAsyncComponent(() =>
    import(`../assets/icons/${iconName.value}.svg`).catch(() => {
      console.warn(`Icon "${iconName.value}" not found`)
      return { template: '<span></span>' }
    }),
  )
})

const sizeClass = computed(() => {
  // Check if it's a preset size
  if (props.size in sizeMap) {
    return sizeMap[props.size as IconSize]
  }
  // Otherwise use as custom Tailwind classes
  return props.size
})
</script>

<style scoped>
:deep(svg) {
  width: 100% !important;
  height: 100% !important;
  display: block;
}
</style>

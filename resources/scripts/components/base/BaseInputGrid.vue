<script setup>
import { computed } from 'vue'

const props = defineProps({
  layout: {
    type: String,
    default: 'two-column',
  },
})

const formLayout = computed(() => {
  if (props.layout === 'two-column') {
    return 'grid gap-y-6 gap-x-4 grid-cols-1 md:grid-cols-2'
  }

  return 'grid gap-y-6 gap-x-4 grid-cols-1'
})
</script>

<template>
  <div :class="formLayout">
    <slot />
  </div>
</template>

<template>
  <h6 :class="typeClass">
    <slot />
  </h6>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  type: {
    type: String,
    default: 'section-title',
    validator: function (value) {
      return ['section-title', 'heading-title'].indexOf(value) !== -1
    },
  },
})

const typeClass = computed(() => {
  return {
    'text-gray-900 text-lg font-medium': props.type === 'heading-title',
    'text-gray-500 uppercase text-base': props.type === 'section-title',
  }
})
</script>

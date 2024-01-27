<template>
  <h6 :class="typeClass">
    <slot />
  </h6>
  
  <p
    v-if="subtitle"
    class="
      mt-2
      text-sm
      leading-snug
      text-gray-500
      dark:text-gray-400
      max-w-[680px]
    "
  >
    {{ subtitle }}
  </p>

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
  subtitle: {
    type: String,
    default: '',
  },
})

const typeClass = computed(() => {
  return {
    'text-gray-900 text-lg font-medium dark:text-white': props.type === 'heading-title',
    'text-gray-500 uppercase text-base dark:text-gray-300': props.type === 'section-title',
  }
})
</script>

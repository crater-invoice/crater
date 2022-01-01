<template>
  <div class="base-content-placeholders-text">
    <div
      v-for="n in lines"
      :key="n"
      :class="lineClass"
      class="w-full h-full base-content-placeholders-text__line"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  lines: {
    type: Number,
    default: 4,
  },
  rounded: {
    type: Boolean,
    default: false,
  },
})

const lineClass = computed(() => {
  return {
    'base-content-placeholders-is-rounded': props.rounded,
  }
})
</script>

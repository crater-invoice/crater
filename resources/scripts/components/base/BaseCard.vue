<template>
  <div class="bg-white rounded-lg shadow">
    <div
      v-if="hasHeaderSlot"
      class="px-5 py-4 text-black border-b border-gray-100 border-solid"
    >
      <slot name="header" />
    </div>
    <div :class="containerClass">
      <slot />
    </div>
    <div
      v-if="hasFooterSlot"
      class="px-5 py-4 border-t border-gray-100 border-solid sm:px-6"
    >
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
  containerClass: {
    type: String,
    default: 'px-4 py-5 sm:px-8 sm:py-8',
  },
})

const slots = useSlots()

const hasHeaderSlot = computed(() => {
  return !!slots.header
})
const hasFooterSlot = computed(() => {
  return !!slots.footer
})
</script>

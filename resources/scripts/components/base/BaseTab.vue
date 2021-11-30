<template>
  <TabPanel :class="[tabPanelContainer, 'focus:outline-none']">
    <!-- focus:ring-1 focus:ring-jet focus:ring-opacity-60 -->
    <slot />
  </TabPanel>
</template>

<script setup>
import { TabPanel } from '@headlessui/vue'

const props = defineProps({
  title: {
    type: [String, Number],
    default: 'Tab',
  },
  count: {
    type: [String, Number],
    default: '',
  },
  countVariant: {
    type: [String, Number],
    default: '',
  },
  tabPanelContainer: {
    type: String,
    default: 'py-4 mt-px',
  },
})
</script>

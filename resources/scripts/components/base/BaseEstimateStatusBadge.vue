<template>
  <span :class="badgeColorClasses">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: false,
    default: '',
  },
})

const badgeColorClasses = computed(() => {
  switch (props.status) {
    case 'DRAFT':
      return 'bg-yellow-300 bg-opacity-25 px-2  py-1 text-sm  text-yellow-800 uppercase font-normal text-center '
    case 'SENT':
      return ' bg-yellow-500 bg-opacity-25 px-2 py-1 text-sm  text-yellow-900 uppercase font-normal text-center '
    case 'VIEWED':
      return 'bg-blue-400 bg-opacity-25 px-2  py-1 text-sm  text-blue-900 uppercase font-normal text-center'
    case 'EXPIRED':
      return 'bg-red-300 bg-opacity-25 px-2 py-1 text-sm  text-red-800 uppercase font-normal text-center'
    case 'ACCEPTED':
      return 'bg-green-400 bg-opacity-25 px-2 py-1 text-sm  text-green-800 uppercase font-normal text-center'
    case 'REJECTED':
      return 'bg-purple-300 bg-opacity-25 px-2 py-1 text-sm  text-purple-800 uppercase font-normal text-center'
    default:
      return 'bg-gray-500 bg-opacity-25 px-2 py-1 text-sm  text-gray-900 uppercase font-normal text-center'
  }
})
</script>

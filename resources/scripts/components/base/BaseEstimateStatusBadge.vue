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
      return 'bg-yellow-300 bg-opacity-25 px-2  py-1 text-sm  text-yellow-800 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-yellow-500 dark:text-yellow-500'
    case 'SENT':
      return ' bg-yellow-500 bg-opacity-25 px-2 py-1 text-sm  text-yellow-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-yellow-200 dark:text-yellow-200'
    case 'VIEWED':
      return 'bg-blue-400 bg-opacity-25 px-2  py-1 text-sm  text-blue-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-blue-300 dark:text-blue-300'
    case 'EXPIRED':
      return 'bg-red-300 bg-opacity-25 px-2 py-1 text-sm  text-red-800 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-red-500 dark:text-red-500'
    case 'ACCEPTED':
      return 'bg-green-400 bg-opacity-25 px-2 py-1 text-sm  text-green-800 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-green-400 dark:text-green-400'
    case 'REJECTED':
      return 'bg-purple-300 bg-opacity-25 px-2 py-1 text-sm  text-purple-800 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-purple-400 dark:text-purple-400'
    default:
      return 'bg-gray-500 bg-opacity-25 px-2 py-1 text-sm  text-gray-900 uppercase font-normal text-center'
  }
})
</script>

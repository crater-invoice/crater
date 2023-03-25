<template>
  <span :class="badgeColorClasses">
    <slot />
  </span>
</template>

<script>
import { computed } from 'vue'

export default {
  props: {
    status: {
      type: String,
      required: false,
      default: '',
    },
  },

  setup(props) {
    const badgeColorClasses = computed(() => {
      switch (props.status) {
        case 'DRAFT':
          return 'bg-yellow-300 bg-opacity-25 px-2  py-1 text-sm  text-yellow-800 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-yellow-500 dark:text-yellow-500'
        case 'SENT':
          return ' bg-yellow-500 bg-opacity-25 px-2 py-1 text-sm  text-yellow-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-lime-200 dark:text-yellow-200'
        case 'VIEWED':
          return 'bg-blue-400 bg-opacity-25 px-2  py-1 text-sm  text-blue-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-blue-300 dark:text-blue-300'
        case 'COMPLETED':
          return 'bg-green-500 bg-opacity-25 px-2  py-1 text-sm  text-green-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-green-400 dark:text-green-400'
        case 'DUE':
          return 'bg-yellow-500 bg-opacity-25 px-2  py-1 text-sm  text-yellow-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-yellow-500 dark:text-yellow-500'
        case 'OVERDUE':
          return 'bg-red-300 bg-opacity-50 px-2  py-1 text-sm  text-red-900 uppercase font-normal text-center  dark:bg-transparent dark:border dark:border-red-500 dark:text-red-500'
        case 'UNPAID':
          return 'bg-yellow-500 bg-opacity-25 px-2 py-1 text-sm  text-yellow-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-yellow-500 dark:text-yellow-500'
        case 'PARTIALLY_PAID':
          return 'bg-blue-400 bg-opacity-25 px-2  py-1 text-sm  text-blue-900 uppercase font-normal text-center  dark:bg-transparent dark:border dark:border-green-400 dark:text-green-400'
        case 'PAID':
          return 'bg-green-500 bg-opacity-25 px-2 py-1 text-sm  text-green-900 uppercase font-normal text-center text-center dark:bg-transparent dark:border dark:border-green-400 dark:text-green-400'
        default:
          return 'bg-gray-500 bg-opacity-25 px-2 py-1 text-sm  text-gray-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-gray-502 dark:text-gray-200'
      }
    })
    return { badgeColorClasses }
  },
}
</script>

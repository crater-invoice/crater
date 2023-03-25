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
        case 'COMPLETED':
          return 'bg-green-500 bg-opacity-25 px-2  py-1 text-sm  text-green-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-green-500 dark:text-green-500'
        case 'ON_HOLD':
          return 'bg-yellow-500 bg-opacity-25 px-2  py-1 text-sm  text-yellow-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-yellow-500 dark:text-yellow-500'
        case 'ACTIVE':
          return 'bg-blue-400 bg-opacity-25 px-2  py-1 text-sm  text-blue-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-blue-400 dark:text-blue-400'
        default:
          return 'bg-gray-500 bg-opacity-25 px-2 py-1 text-sm  text-gray-900 uppercase font-normal text-center dark:bg-transparent dark:border dark:border-gray-500 dark:text-gray-500'
      }
    })
    return { badgeColorClasses }
  },
}
</script>

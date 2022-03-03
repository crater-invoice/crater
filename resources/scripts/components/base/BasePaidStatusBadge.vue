<template>
  <span :class="[badgeColorClasses, defaultClass]" class="">
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
    defaultClass: {
      type: String,
      default: 'px-1 py-0.5 text-xs',
    },
  },

  setup(props) {
    const badgeColorClasses = computed(() => {
      switch (props.status) {
        case 'PAID':
          return 'bg-primary-300 bg-opacity-25 text-primary-800 uppercase font-normal text-center'
        case 'UNPAID':
          return ' bg-yellow-500 bg-opacity-25 text-yellow-900 uppercase font-normal text-center '
        case 'PARTIALLY_PAID':
          return 'bg-blue-400 bg-opacity-25 text-blue-900 uppercase font-normal text-center'
        case 'OVERDUE':
          return 'bg-red-300 bg-opacity-50 px-2  py-1 text-sm  text-red-900 uppercase font-normal text-center'
        default:
          return 'bg-gray-500 bg-opacity-25 text-gray-900 uppercase font-normal text-center'
      }
    })
    return { badgeColorClasses }
  },
}
</script>

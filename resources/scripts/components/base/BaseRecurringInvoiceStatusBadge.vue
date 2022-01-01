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
          return 'bg-green-500 bg-opacity-25 px-2  py-1 text-sm  text-green-900 uppercase font-normal text-center'
        case 'ON_HOLD':
          return 'bg-yellow-500 bg-opacity-25 px-2  py-1 text-sm  text-yellow-900 uppercase font-normal text-center'
        case 'ACTIVE':
          return 'bg-blue-400 bg-opacity-25 px-2  py-1 text-sm  text-blue-900 uppercase font-normal text-center'
        default:
          return 'bg-gray-500 bg-opacity-25 px-2 py-1 text-sm  text-gray-900 uppercase font-normal text-center'
      }
    })
    return { badgeColorClasses }
  },
}
</script>

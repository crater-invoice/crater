<template>
  <router-link v-bind="$attrs" :class="containerClass">
    <span v-if="hasIconSlot" class="mr-3">
      <slot name="icon" />
    </span>
    <span>{{ title }}</span>
  </router-link>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'ListItem',
  props: {
    title: {
      type: String,
      required: false,
      default: '',
    },
    active: {
      type: Boolean,
      required: true,
    },
    index: {
      type: Number,
      default: null,
    },
  },
  setup(props, { slots }) {
    const defaultClass = `cursor-pointer pb-2 pr-0 text-sm font-medium leading-5  flex items-center`
    let hasIconSlot = computed(() => {
      return !!slots.icon
    })
    let containerClass = computed(() => {
      if (props.active) return `${defaultClass} text-primary-500`
      else return `${defaultClass} text-gray-500`
    })
    return {
      hasIconSlot,
      containerClass,
    }
  },
}
</script>

<template>
  <BaseCustomTag :tag="tag" :title="text">
    {{ displayText }}
  </BaseCustomTag>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  tag: {
    type: String,
    default: 'div',
  },

  text: {
    type: String,
    default: '',
  },

  length: {
    type: Number,
    default: 0,
  }
})

const displayText = computed(() => {

  return props.text.length < props.length ?  props.text : `${props.text.substring(0 , props.length)}...`
})
</script>

<template>
  <BaseSwitch v-model="inputValue" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean],
    default: null,
  },
})

const emit = defineEmits(['update:modelValue'])

const inputValue = computed({
  get: () => props.modelValue === 1,
  set: (value) => {
    const intVal = value ? 1 : 0

    emit('update:modelValue', intVal)
  },
})
</script>

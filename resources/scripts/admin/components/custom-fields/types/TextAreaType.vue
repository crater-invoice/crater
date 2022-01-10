<template>
  <BaseTextarea v-model="inputValue" :rows="rows" :name="inputName" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: null,
  },
  rows: {
    type: String,
    default: '2',
  },
  inputName: {
    type: String,
    default: 'description',
  },
})

const emit = defineEmits(['update:modelValue'])

const inputValue = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>

<template>
  <BaseDatePicker v-model="date" enable-time />
</template>

<script setup>
import moment from 'moment'
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: moment().format('YYYY-MM-DD hh:MM'),
  },
})

const emit = defineEmits(['update:modelValue'])

const date = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>

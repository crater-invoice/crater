<template>
  <NumberCustomizer
    type="estimate"
    :type-store="estimateStore"
    default-series="EST"
  />
</template>

<script setup>
import { useEstimateStore } from '@/scripts/stores/estimate'
import NumberCustomizer from '../NumberCustomizer.vue'

const estimateStore = useEstimateStore()
</script>


<template>
  <NumberCustomizer
    type="payment"
    :type-store="paymentStore"
    default-series="PAY"
  />
</template>

<script setup>
import { usePaymentStore } from '@/scripts/admin/stores/payment'
import NumberCustomizer from '../NumberCustomizer.vue'

const paymentStore = usePaymentStore()
</script>

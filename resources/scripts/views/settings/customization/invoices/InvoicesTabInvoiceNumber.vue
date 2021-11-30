<template>
  <NumberCustomizer
    type="invoice"
    :type-store="invoiceStore"
    default-series="INV"
  />
</template>

<script setup>
import { useInvoiceStore } from '@/scripts/stores/invoice'
import NumberCustomizer from '../NumberCustomizer.vue'

const invoiceStore = useInvoiceStore()
</script>


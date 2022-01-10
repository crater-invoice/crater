<template>
  <NumberCustomizer
    type="invoice"
    :type-store="invoiceStore"
    default-series="INV"
  />
</template>

<script setup>
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import NumberCustomizer from '../NumberCustomizer.vue'

const invoiceStore = useInvoiceStore()
</script>

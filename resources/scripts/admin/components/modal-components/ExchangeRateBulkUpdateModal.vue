<template>
  <BaseModal :show="modalActive">
    <ExchangeRateBulkUpdate @update="closeModal()" />
  </BaseModal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import ExchangeRateBulkUpdate from '@/scripts/admin/components/currency-exchange-rate/ExchangeRateBulkUpdate.vue'
import { useModalStore } from '@/scripts/stores/modal'

const modalStore = useModalStore()

const modalActive = computed(() => {
  return (
    modalStore.active &&
    modalStore.componentName === 'ExchangeRateBulkUpdateModal'
  )
})

function closeModal() {
  modalStore.closeModal()
}
</script>

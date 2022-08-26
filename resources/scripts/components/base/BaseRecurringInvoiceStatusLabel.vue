<template>
  {{ labelStatus }}
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  status: {
    type: String,
    required: false,
    default: '',
  },
})

const labelStatus = computed(() => {
  switch (props.status) {
    case 'COMPLETED':
      return t('recurring_invoices.complete')
    case 'ON_HOLD':
      return t('recurring_invoices.on_hold')
    case 'ACTIVE':
      return t('recurring_invoices.working')
    default:
      return props.status
  }
})
</script>

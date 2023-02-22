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
    case 'DRAFT':
      return t('estimates.draft')
    case 'SENT':
      return t('estimates.sent')
    case 'VIEWED':
      return t('estimates.viewed')
    case 'EXPIRED':
      return t('estimates.expired')
    case 'ACCEPTED':
      return t('estimates.accepted')
    case 'REJECTED':
      return t('estimates.rejected')
    case 'DECLINED':
      return t('estimates.declined')
    default:
      return props.status
  }
})
</script>

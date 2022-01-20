<template>
  <PaymentModeModal />

  <BaseSettingCard
    :title="$t('settings.payment_modes.title')"
    :description="$t('settings.payment_modes.description')"
  >
    <template #action>
      <BaseButton
        type="submit"
        variant="primary-outline"
        @click="addPaymentMode"
      >
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />
        </template>
        {{ $t('settings.payment_modes.add_payment_mode') }}
      </BaseButton>
    </template>

    <BaseTable
      ref="table"
      :data="fetchData"
      :columns="paymentColumns"
      class="mt-16"
    >
      <template #cell-actions="{ row }">
        <PaymentModeDropdown
          :row="row.data"
          :table="table"
          :load-data="refreshTable"
        />
      </template>
    </BaseTable>
  </BaseSettingCard>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import { usePaymentStore } from '@/scripts/admin/stores/payment'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useModalStore } from '@/scripts/stores/modal'
import PaymentModeModal from '@/scripts/admin/components/modal-components/PaymentModeModal.vue'
import PaymentModeDropdown from '@/scripts/admin/components/dropdowns/PaymentModeIndexDropdown.vue'

const modalStore = useModalStore()
const dialogStore = useDialogStore()
const paymentStore = usePaymentStore()
const { t } = useI18n()

const table = ref(null)

const paymentColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.payment_modes.mode_name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'actions',
      label: '',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

async function refreshTable() {
  table.value && table.value.refresh()
}

async function fetchData({ page, filter, sort }) {
  let data = {
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  let response = await paymentStore.fetchPaymentModes(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: 5,
    },
  }
}

function addPaymentMode() {
  modalStore.openModal({
    title: t('settings.payment_modes.add_payment_mode'),
    componentName: 'PaymentModeModal',
    refreshData: table.value && table.value.refresh,
    size: 'sm',
  })
}
</script>

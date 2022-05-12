<template>
  <SendInvoiceModal @update="updateSentInvoiceStatus" />
  <div class="relative table-container">
    <BaseTable
      ref="table"
      :data="recurringInvoiceStore.newRecurringInvoice.invoices"
      :columns="invoiceColumns"
      :loading="recurringInvoiceStore.isFetchingViewData"
      :placeholder-count="5"
      class="mt-5"
    >
      <!-- Invoice Number  -->
      <template #cell-invoice_number="{ row }">
        <router-link
          :to="{ path: `/admin/invoices/${row.data.id}/view` }"
          class="font-medium text-primary-500"
        >
          {{ row.data.invoice_number }}
        </router-link>
      </template>

      <!-- Invoice Due amount  -->
      <template #cell-total="{ row }">
        <BaseFormatMoney
          :amount="row.data.due_amount"
          :currency="row.data.currency"
        />
      </template>

      <!-- Invoice status  -->
      <template #cell-status="{ row }">
        <BaseInvoiceStatusBadge :status="row.data.status" class="px-3 py-1">
          {{ row.data.status }}
        </BaseInvoiceStatusBadge>
      </template>

      <!-- Actions -->
      <template v-if="hasAtleastOneAbility()" #cell-actions="{ row }">
        <InvoiceDropdown :row="row.data" :table="table" />
      </template>
    </BaseTable>
  </div>
</template>

<script setup>
import { computed, ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'
import abilities from '@/scripts/admin/stub/abilities'
import InvoiceDropdown from '@/scripts/admin/components/dropdowns/InvoiceIndexDropdown.vue'
import SendInvoiceModal from '@/scripts/admin/components/modal-components/SendInvoiceModal.vue'

const recurringInvoiceStore = useRecurringInvoiceStore()

const table = ref(null)
const baseSelect = ref(null)
const utils = inject('$utils')
const { t } = useI18n()
const currency = ref(null)
const router = useRouter()
const userStore = useUserStore()

const invoiceColumns = computed(() => {
  return [
    {
      key: 'invoice_date',
      label: t('invoices.date'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    { key: 'invoice_number', label: t('invoices.invoice') },
    { key: 'customer.name', label: t('invoices.customer') },
    { key: 'status', label: t('invoices.status') },
    { key: 'total', label: t('invoices.total') },

    {
      key: 'actions',
      label: t('invoices.action'),
      tdClass: 'text-right text-sm font-medium',
      thClass: 'text-right',
      sortable: false,
    },
  ]
})

function hasAtleastOneAbility() {
  return userStore.hasAbilities([
    abilities.DELETE_INVOICE,
    abilities.EDIT_INVOICE,
    abilities.VIEW_INVOICE,
    abilities.SEND_INVOICE,
  ])
}

function refreshTable() {
  table.value && table.value.refresh()
}

function updateSentInvoiceStatus(id) {
  let pos = recurringInvoiceStore.newRecurringInvoice.invoices.findIndex(
    (invoice) => invoice.id === id
  )

  if (recurringInvoiceStore.newRecurringInvoice.invoices[pos]) {
    recurringInvoiceStore.newRecurringInvoice.invoices[pos].status = 'SENT'
  }
}
</script>

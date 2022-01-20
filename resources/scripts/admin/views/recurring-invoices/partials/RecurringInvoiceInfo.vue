<template>
  <BaseCard class="mt-10">
    <BaseHeading>
      {{ $t('customers.basic_info') }}
    </BaseHeading>

    <BaseDescriptionList class="mt-5">
      <BaseDescriptionListItem
        :label="$t('recurring_invoices.starts_at')"
        :content-loading="isLoading"
        :value="recurringInvoiceStore.newRecurringInvoice?.formatted_starts_at"
      />

      <BaseDescriptionListItem
        :label="$t('recurring_invoices.next_invoice_date')"
        :content-loading="isLoading"
        :value="
          recurringInvoiceStore.newRecurringInvoice?.formatted_next_invoice_at
        "
      />

      <BaseDescriptionListItem
        v-if="
          recurringInvoiceStore.newRecurringInvoice?.limit_date &&
          recurringInvoiceStore.newRecurringInvoice?.limit_by !== 'NONE'
        "
        :label="$t('recurring_invoices.limit_date')"
        :content-loading="isLoading"
        :value="recurringInvoiceStore.newRecurringInvoice?.limit_date"
      />

      <BaseDescriptionListItem
        v-if="
          recurringInvoiceStore.newRecurringInvoice?.limit_date &&
          recurringInvoiceStore.newRecurringInvoice?.limit_by !== 'NONE'
        "
        :label="$t('recurring_invoices.limit_by')"
        :content-loading="isLoading"
        :value="recurringInvoiceStore.newRecurringInvoice?.limit_by"
      />

      <BaseDescriptionListItem
        v-if="recurringInvoiceStore.newRecurringInvoice?.limit_count"
        :label="$t('recurring_invoices.limit_count')"
        :value="recurringInvoiceStore.newRecurringInvoice?.limit_count"
        :content-loading="isLoading"
      />

      <BaseDescriptionListItem
        v-if="recurringInvoiceStore.newRecurringInvoice?.selectedFrequency"
        :label="$t('recurring_invoices.frequency.title')"
        :value="
          recurringInvoiceStore.newRecurringInvoice?.selectedFrequency?.label
        "
        :content-loading="isLoading"
      />
    </BaseDescriptionList>

    <BaseHeading class="mt-8">
      {{ $t('invoices.title', 2) }}
    </BaseHeading>

    <Invoices />
  </BaseCard>
</template>

<script setup>
import { ref, computed, watch, reactive, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'
import Invoices from './Invoices.vue'

const recurringInvoiceStore = useRecurringInvoiceStore()

const route = useRoute()

let isLoading = computed(() => {
  return recurringInvoiceStore.isFetchingViewData
})

watch(
  route,
  () => {
    if (route.params.id && route.name === 'recurring-invoices.view') {
      loadRecurringInvoice()
    }
  },
  { immediate: true }
)

async function loadRecurringInvoice() {
  await recurringInvoiceStore.fetchRecurringInvoice(route.params.id)
}
</script>

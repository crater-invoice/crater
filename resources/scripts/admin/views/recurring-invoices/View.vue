<template>
  <BasePage class="xl:pl-96">
    <BasePageHeader :title="pageTitle">
      <template #actions>
        <RecurringInvoiceIndexDropdown
          v-if="hasAtleastOneAbility()"
          :row="recurringInvoiceStore.newRecurringInvoice"
        />
      </template>
    </BasePageHeader>

    <RecurringInvoiceViewSidebar />

    <RecurringInvoiceInfo />
  </BasePage>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'
import abilities from '@/scripts/admin/stub/abilities'

import RecurringInvoiceViewSidebar from '@/scripts/admin/views/recurring-invoices/partials/RecurringInvoiceViewSidebar.vue'
import RecurringInvoiceInfo from '@/scripts/admin/views/recurring-invoices/partials/RecurringInvoiceInfo.vue'
import RecurringInvoiceIndexDropdown from '@/scripts/admin/components/dropdowns/RecurringInvoiceIndexDropdown.vue'

const dialogStore = useDialogStore()
const recurringInvoiceStore = useRecurringInvoiceStore()
const userStore = useUserStore()
const { t } = useI18n()

const router = useRouter()

const pageTitle = computed(() => {
  return recurringInvoiceStore.newRecurringInvoice
    ? recurringInvoiceStore.newRecurringInvoice?.customer?.name
    : ''
})

function hasAtleastOneAbility() {
  return userStore.hasAbilities([
    abilities.DELETE_RECURRING_INVOICE,
    abilities.EDIT_RECURRING_INVOICE,
  ])
}

function removeRecurringInvoice(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('recurring_invoices.confirm_delete', 1),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then((res) => {
      if (res) {
        let data = { ids: [id] }
        let response = recurringInvoiceStore
          .deleteRecurringInvoice(data)
          .then((res) => {
            if (response) {
              router.push('/admin/recurring-invoices')
              return true
            }
          })
      }
    })
}
</script>

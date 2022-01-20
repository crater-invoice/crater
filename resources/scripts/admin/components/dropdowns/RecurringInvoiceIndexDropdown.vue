<template>
  <BaseDropdown :content-loading="recurringInvoiceStore.isFetchingViewData">
    <template #activator>
      <BaseButton
        v-if="route.name === 'recurring-invoices.view'"
        variant="primary"
      >
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- Edit Recurring Invoice  -->
    <router-link
      v-if="userStore.hasAbilities(abilities.EDIT_RECURRING_INVOICE)"
      :to="`/admin/recurring-invoices/${row.id}/edit`"
    >
      <BaseDropdownItem>
        <BaseIcon
          name="PencilIcon"
          class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
        />
        {{ $t('general.edit') }}
      </BaseDropdownItem>
    </router-link>

    <!-- View Recurring Invoice -->
    <router-link
      v-if="
        route.name !== 'recurring-invoices.view' &&
        userStore.hasAbilities(abilities.VIEW_RECURRING_INVOICE)
      "
      :to="`recurring-invoices/${row.id}/view`"
    >
      <BaseDropdownItem>
        <BaseIcon
          name="EyeIcon"
          class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
        />
        {{ $t('general.view') }}
      </BaseDropdownItem>
    </router-link>

    <!-- Delete Recurring Invoice  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.DELETE_RECURRING_INVOICE)"
      @click="removeMultipleRecurringInvoices(row.id)"
    >
      <BaseIcon
        name="TrashIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.delete') }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useModalStore } from '@/scripts/stores/modal'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/scripts/admin/stores/user'
import { inject } from 'vue'
import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'
import abilities from '@/scripts/admin/stub/abilities'

const props = defineProps({
  row: {
    type: Object,
    default: null,
  },
  table: {
    type: Object,
    default: null,
  },
  loadData: {
    type: Function,
    default: () => {},
  },
})

const recurringInvoiceStore = useRecurringInvoiceStore()
const notificationStore = useNotificationStore()
const dialogStore = useDialogStore()
const userStore = useUserStore()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const utils = inject('utils')

async function removeMultipleRecurringInvoices(id = null) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('invoices.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await recurringInvoiceStore
          .deleteMultipleRecurringInvoices(id)
          .then((res) => {
            if (res.data.success) {
              props.table && props.table.refresh()
              recurringInvoiceStore.$patch((state) => {
                state.selectedRecurringInvoices = []
                state.selectAllField = false
              })
              notificationStore.showNotification({
                type: 'success',
                message: t('recurring_invoices.deleted_message', 2),
              })
            } else if (res.data.error) {
              notificationStore.showNotification({
                type: 'error',
                message: res.data.message,
              })
            }
          })
      }
    })
}
</script>

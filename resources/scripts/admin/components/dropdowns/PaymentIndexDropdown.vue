<template>
  <BaseDropdown :content-loading="contentLoading">
    <template #activator>
      <BaseButton v-if="route.name === 'payments.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- Copy pdf url  -->
    <BaseDropdownItem
      v-if="
        route.name === 'payments.view' &&
        userStore.hasAbilities(abilities.VIEW_PAYMENT)
      "
      v-slot="slotProps"
      class="rounded-md"
      @click="copyPdfUrl"
    >
      <BaseIcon
        name="LinkIcon"
        :class="slotProps.class"
      />
      {{ $t('general.copy_pdf_url') }}
    </BaseDropdownItem>

    <!-- edit payment  -->
    <router-link
      v-if="userStore.hasAbilities(abilities.EDIT_PAYMENT)"
      :to="`/admin/payments/${row.id}/edit`"
    >
      <BaseDropdownItem v-slot="slotProps">
        <BaseIcon
          name="PencilIcon"
          :class="slotProps.class"
        />
        {{ $t('general.edit') }}
      </BaseDropdownItem>
    </router-link>

    <!-- view payment  -->
    <router-link
      v-if="
        route.name !== 'payments.view' &&
        userStore.hasAbilities(abilities.VIEW_PAYMENT)
      "
      :to="`/admin/payments/${row.id}/view`"
    >
      <BaseDropdownItem v-slot="slotProps">
        <BaseIcon
          name="EyeIcon"
          :class="slotProps.class"
        />
        {{ $t('general.view') }}
      </BaseDropdownItem>
    </router-link>

    <!-- Send Estimate  -->
    <BaseDropdownItem
      v-if="
        row.status !== 'SENT' &&
        route.name !== 'payments.view' &&
        userStore.hasAbilities(abilities.SEND_PAYMENT)
      "
      v-slot="slotProps"
      @click="sendPayment(row)"
    >
      <BaseIcon
        name="PaperAirplaneIcon"
        :class="slotProps.class"
      />
      {{ $t('payments.send_payment') }}
    </BaseDropdownItem>

    <!-- delete payment  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.DELETE_PAYMENT)"
      v-slot="slotProps"
      @click="removePayment(row.id)"
    >
      <BaseIcon
        name="TrashIcon"
        :class="slotProps.class"
      />
      {{ $t('general.delete') }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { useDialogStore } from '@/scripts/stores/dialog'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useModalStore } from '@/scripts/stores/modal'
import { useI18n } from 'vue-i18n'
import { usePaymentStore } from '@/scripts/admin/stores/payment'
import { useRoute, useRouter } from 'vue-router'
import { inject } from 'vue'
import { useUserStore } from '@/scripts/admin/stores/user'
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
  contentLoading: {
    type: Boolean,
    default: false,
  },
})

const dialogStore = useDialogStore()
const notificationStore = useNotificationStore()
const { t } = useI18n()
const paymentStore = usePaymentStore()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const modalStore = useModalStore()

const $utils = inject('utils')

function removePayment(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('payments.confirm_delete', 1),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then(async (res) => {
      if (res) {
        await paymentStore.deletePayment({ ids: [id] })
        router.push(`/admin/payments`)
        props.table && props.table.refresh()

        return true
      }
    })
}

function copyPdfUrl() {
  let pdfUrl = `${window.location.origin}/payments/pdf/${props.row?.unique_hash}`

  $utils.copyTextToClipboard(pdfUrl)

  notificationStore.showNotification({
    type: 'success',
    message: t('general.copied_pdf_url_clipboard'),
  })
}

async function sendPayment(payment) {
  modalStore.openModal({
    title: t('payments.send_payment'),
    componentName: 'SendPaymentModal',
    id: payment.id,
    data: payment,
    variant: 'lg',
  })
}
</script>

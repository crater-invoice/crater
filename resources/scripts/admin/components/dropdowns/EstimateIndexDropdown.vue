<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton v-if="route.name === 'estimates.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="text-white" />
      </BaseButton>
      <BaseIcon v-else class="text-gray-500" name="DotsHorizontalIcon" />
    </template>

    <!-- Copy PDF url  -->
    <BaseDropdownItem
      v-if="route.name === 'estimates.view'"
      v-slot="slotProps"
      @click="copyPdfUrl"
    >
      <BaseIcon
        name="LinkIcon"
        :class="slotProps.class"
      />
      {{ $t('general.copy_pdf_url') }}
    </BaseDropdownItem>

    <!-- Edit Estimate -->
    <router-link
      v-if="userStore.hasAbilities(abilities.EDIT_ESTIMATE)"
      :to="`/admin/estimates/${row.id}/edit`"
    >
      <BaseDropdownItem v-slot="slotProps">
        <BaseIcon
          name="PencilIcon"
          :class="slotProps.class"
        />
        {{ $t('general.edit') }}
      </BaseDropdownItem>
    </router-link>

    <!-- Delete Estimate  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.DELETE_ESTIMATE)"
      v-slot="slotProps"
      @click="removeEstimate(row.id)"
    >
      <BaseIcon
        name="TrashIcon"
        :class="slotProps.class"
      />
      {{ $t('general.delete') }}
    </BaseDropdownItem>

    <!-- View Estimate -->
    <router-link
      v-if="
        route.name !== 'estimates.view' &&
        userStore.hasAbilities(abilities.VIEW_ESTIMATE)
      "
      :to="`estimates/${row.id}/view`"
    >
      <BaseDropdownItem v-slot="slotProps">
        <BaseIcon
          name="EyeIcon"
          :class="slotProps.class"
        />
        {{ $t('general.view') }}
      </BaseDropdownItem>
    </router-link>

    <!-- Convert into Invoice  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.CREATE_INVOICE)"
      v-slot="slotProps"
      @click="convertInToinvoice(row.id)"
    >
      <BaseIcon
        name="DocumentTextIcon"
        :class="slotProps.class"
      />
      {{ $t('estimates.convert_to_invoice') }}
    </BaseDropdownItem>

    <!-- Mark as sent  -->
    <BaseDropdownItem
      v-if="
        row.status !== 'SENT' &&
        route.name !== 'estimates.view' &&
        userStore.hasAbilities(abilities.SEND_ESTIMATE)
      "
      v-slot="slotProps"
      @click="onMarkAsSent(row.id)"
    >
      <BaseIcon
        name="CheckCircleIcon"
        :class="slotProps.class"
      />
      {{ $t('estimates.mark_as_sent') }}
    </BaseDropdownItem>

    <!-- Send Estimate  -->
    <BaseDropdownItem
      v-if="
        row.status !== 'SENT' &&
        route.name !== 'estimates.view' &&
        userStore.hasAbilities(abilities.SEND_ESTIMATE)
      "
      v-slot="slotProps"
      @click="sendEstimate(row)"
    >
      <BaseIcon
        name="PaperAirplaneIcon"
        :class="slotProps.class"
      />
      {{ $t('estimates.send_estimate') }}
    </BaseDropdownItem>

    <!-- Resend Estimate -->
    <BaseDropdownItem v-if="canResendEstimate(row)" v-slot="slotProps"  @click="sendEstimate(row)">
      <BaseIcon
        name="PaperAirplaneIcon"
        :class="slotProps.class"
      />
      {{ $t('estimates.resend_estimate') }}
    </BaseDropdownItem>

    <!-- Mark as Accepted -->
    <BaseDropdownItem
      v-if="
        row.status !== 'ACCEPTED' &&
        userStore.hasAbilities(abilities.EDIT_ESTIMATE)
      "
      v-slot="slotProps"
      @click="onMarkAsAccepted(row.id)"
    >
      <BaseIcon
        name="CheckCircleIcon"
        :class="slotProps.class"
      />
      {{ $t('estimates.mark_as_accepted') }}
    </BaseDropdownItem>

    <!-- Mark as Rejected  -->
    <BaseDropdownItem
      v-if="
        row.status !== 'REJECTED' &&
        userStore.hasAbilities(abilities.EDIT_ESTIMATE)
      "
      v-slot="slotProps"
      @click="onMarkAsRejected(row.id)"
    >
      <BaseIcon
        name="XCircleIcon"
        :class="slotProps.class"
      />
      {{ $t('estimates.mark_as_rejected') }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { useEstimateStore } from '@/scripts/admin/stores/estimate'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useModalStore } from '@/scripts/stores/modal'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useDialogStore } from '@/scripts/stores/dialog'
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
})

const utils = inject('utils')

const estimateStore = useEstimateStore()
const modalStore = useModalStore()
const notificationStore = useNotificationStore()
const dialogStore = useDialogStore()
const userStore = useUserStore()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

async function removeEstimate(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('estimates.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then((res) => {
      id = id
      if (res) {
        estimateStore.deleteEstimate({ ids: [id] }).then((res) => {
          if (res) {
            props.table && props.table.refresh()

            if (res.data) {
              router.push('/admin/estimates')
            }
            estimateStore.$patch((state) => {
              state.selectedEstimates = []
              state.selectAllField = false
            })
          }
        })
      }
    })
}

function convertInToinvoice(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('estimates.confirm_conversion'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'primary',
      hideNoButton: false,
      size: 'lg',
    })
    .then((res) => {
      if (res) {
        estimateStore.convertToInvoice(id).then((res) => {
          if (res.data) {
            router.push(`/admin/invoices/${res.data.data.id}/edit`)
          }
        })
      }
    })
}

async function onMarkAsSent(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('estimates.confirm_mark_as_sent'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'primary',
      hideNoButton: false,
      size: 'lg',
    })
    .then((response) => {
      const data = {
        id: id,
        status: 'SENT',
      }
      if (response) {
        estimateStore.markAsSent(data).then((response) => {
          props.table && props.table.refresh()
        })
      }
    })
}

function canResendEstimate(row) {
  return (
    (row.status == 'SENT' || row.status == 'VIEWED') &&
    route.name !== 'estimates.view' &&
    userStore.hasAbilities(abilities.SEND_ESTIMATE)
  )
}

async function sendEstimate(estimate) {
  modalStore.openModal({
    title: t('estimates.send_estimate'),
    componentName: 'SendEstimateModal',
    id: estimate.id,
    data: estimate,
    variant: 'lg',
  })
}

async function onMarkAsAccepted(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('estimates.confirm_mark_as_accepted'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'primary',
      hideNoButton: false,
      size: 'lg',
    })
    .then((response) => {
      const data = {
        id: id,
        status: 'ACCEPTED',
      }
      if (response) {
        estimateStore.markAsAccepted(data).then((response) => {
          props.table && props.table.refresh()
        })
      }
    })
}

async function onMarkAsRejected(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('estimates.confirm_mark_as_rejected'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'primary',
      hideNoButton: false,
      size: 'lg',
    })
    .then((response) => {
      const data = {
        id: id,
        status: 'REJECTED',
      }
      if (response) {
        estimateStore.markAsRejected(data).then((response) => {
          props.table && props.table.refresh()
        })
      }
    })
}

function copyPdfUrl() {
  let pdfUrl = `${window.location.origin}/estimates/pdf/${props.row.unique_hash}`

  let response = utils.copyTextToClipboard(pdfUrl)
  notificationStore.showNotification({
    type: 'success',
    message: t('general.copied_pdf_url_clipboard'),
  })
}
</script>

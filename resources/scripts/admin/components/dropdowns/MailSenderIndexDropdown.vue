<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton v-if="route.name === 'mailsender.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit mail-sender  -->
    <BaseDropdownItem @click="editMailSender(row.id)">
      <BaseIcon
        name="PencilIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.edit') }}
    </BaseDropdownItem>

    <!-- delete mail-sender  -->
    <BaseDropdownItem v-if="!row.is_default" @click="removeMailSender(row.id)">
      <BaseIcon
        name="TrashIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.delete') }}
    </BaseDropdownItem>

    <!-- send test mail-sender  -->
    <BaseDropdownItem @click="openMailSenderTestModal(row.id)">
      <BaseIcon
        name="PaperAirplaneIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.send_test_mail') }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { useDialogStore } from '@/scripts/stores/dialog'
import { useI18n } from 'vue-i18n'
import { useMailSenderStore } from '@/scripts/admin/stores/mail-sender'
import { useRoute, useRouter } from 'vue-router'
import { inject } from 'vue'
import { useModalStore } from '@/scripts/stores/modal'

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
    default: null,
  },
})

const pre_t = 'settings.mail_sender'
const dialogStore = useDialogStore()
const { t } = useI18n()
const mailSenderStore = useMailSenderStore()
const route = useRoute()
const modalStore = useModalStore()

async function editMailSender(id) {
  await mailSenderStore.fetchMailSender(id)
  modalStore.openModal({
    title: t(`${pre_t}.edit_mail_sender`),
    componentName: 'MailSenderModal',
    size: 'md',
    refreshData: props.loadData && props.loadData,
  })
}

function removeMailSender(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t(`${pre_t}.confirm_delete`),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        let response = await mailSenderStore.deleteMailSender(id)
        if (response.data.success) {
          props.loadData && props.loadData()
          return true
        }
        props.loadData && props.loadData()
      }
    })
}

async function openMailSenderTestModal(id) {
  modalStore.openModal({
    title: t(`general.send_test_mail`),
    componentName: 'MailSenderTestModal',
    size: 'md',
    id: id,
    refreshData: props.loadData && props.loadData,
  })
}
</script>

<template>
  <MailTestModal />

  <BaseSettingCard
    :title="$t('settings.mail.mail_config')"
    :description="$t('settings.mail.mail_config_desc')"
  >
    <div v-if="mailDriverStore && mailDriverStore.mailConfigData" class="mt-14">
      <component
        :is="mailDriver"
        :config-data="mailDriverStore.mailConfigData"
        :is-saving="isSaving"
        :mail-drivers="mailDriverStore.mail_drivers"
        :is-fetching-initial-data="isFetchingInitialData"
        @on-change-driver="(val) => changeDriver(val)"
        @submit-data="saveEmailConfig"
      >
        <BaseButton
          variant="primary-outline"
          type="button"
          class="ml-2"
          :content-loading="isFetchingInitialData"
          @click="openMailTestModal"
        >
          {{ $t('general.test_mail_conf') }}
        </BaseButton>
      </component>
    </div>
  </BaseSettingCard>
</template>

<script setup>
import Smtp from '@/scripts/admin/views/settings/mail-driver/SmtpMailDriver.vue'
import Mailgun from '@/scripts/admin/views/settings/mail-driver/MailgunMailDriver.vue'
import Ses from '@/scripts/admin/views/settings/mail-driver/SesMailDriver.vue'
import Basic from '@/scripts/admin/views/settings/mail-driver/BasicMailDriver.vue'
import { ref, computed } from 'vue'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'
import { useModalStore } from '@/scripts/stores/modal'
import MailTestModal from '@/scripts/admin/components/modal-components/MailTestModal.vue'
import { useI18n } from 'vue-i18n'

let isSaving = ref(false)
let isFetchingInitialData = ref(false)

const mailDriverStore = useMailDriverStore()
const modalStore = useModalStore()
const { t } = useI18n()

loadData()
function changeDriver(value) {
  mailDriverStore.mail_driver = value
  mailDriverStore.mailConfigData.mail_driver = value
}

async function loadData() {
  isFetchingInitialData.value = true
  Promise.all([
    await mailDriverStore.fetchMailDrivers(),
    await mailDriverStore.fetchMailConfig(),
  ]).then(([res1]) => {
    isFetchingInitialData.value = false
  })
}

const mailDriver = computed(() => {
  if (mailDriverStore.mail_driver == 'smtp') return Smtp
  if (mailDriverStore.mail_driver == 'mailgun') return Mailgun
  if (mailDriverStore.mail_driver == 'sendmail') return Basic
  if (mailDriverStore.mail_driver == 'ses') return Ses
  if (mailDriverStore.mail_driver == 'mail') return Basic
  return Smtp
})

async function saveEmailConfig(value) {
  try {
    isSaving.value = true
    await mailDriverStore.updateMailConfig(value)
    isSaving.value = false
    return true
  } catch (e) {
    console.error(e)
  }
}

function openMailTestModal() {
  modalStore.openModal({
    title: t('general.test_mail_conf'),
    componentName: 'MailTestModal',
    size: 'sm',
  })
}
</script>

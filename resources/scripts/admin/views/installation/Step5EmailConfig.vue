<template>
  <BaseWizardStep
    :title="$t('wizard.mail.mail_config')"
    :description="$t('wizard.mail.mail_config_desc')"
  >
    <form action="" @submit.prevent="next">
      <component
        :is="mailDriverStore.mail_driver"
        :config-data="mailDriverStore.mailConfigData"
        :is-saving="isSaving"
        :is-fetching-initial-data="isFetchingInitialData"
        @on-change-driver="(val) => changeDriver(val)"
        @submit-data="next"
      />
    </form>
  </BaseWizardStep>
</template>

<script>
import Smtp from './mail-driver/SmtpMailDriver.vue'
import Mailgun from './mail-driver/MailgunMailDriver.vue'
import Ses from './mail-driver/SesMailDriver.vue'
import Basic from './mail-driver/BasicMailDriver.vue'
import { useMailDriverStore } from '@/scripts/admin/stores/mail-driver'
import { ref } from 'vue'

export default {
  components: {
    Smtp,
    Mailgun,
    Ses,
    sendmail: Basic,
    Mail: Basic,
  },

  emits: ['next'],

  setup(props, { emit }) {
    const isSaving = ref(false)
    const isFetchingInitialData = ref(false)

    const mailDriverStore = useMailDriverStore()

    mailDriverStore.mail_driver = 'mail'

    loadData()

    function changeDriver(value) {
      mailDriverStore.mail_driver = value
    }

    async function loadData() {
      isFetchingInitialData.value = true
      await mailDriverStore.fetchMailDrivers()
      isFetchingInitialData.value = false
    }

    async function next(mailConfigData) {
      isSaving.value = true
      let res = await mailDriverStore.updateMailConfig(mailConfigData)
      isSaving.value = false

      if (res.data.success) {
        await emit('next', 5)
      }
    }

    return {
      mailDriverStore,
      isSaving,
      isFetchingInitialData,
      changeDriver,
      next,
    }
  },
}
</script>

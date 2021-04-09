<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.mail.mail_config') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.mail.mail_config_desc') }}
        </p>
      </template>
      <div v-if="mailConfigData">
        <component
          :is="mail_driver"
          :config-data="mailConfigData"
          :loading="isLoading"
          :mail-drivers="mail_drivers"
          @on-change-driver="
            (val) => (mail_driver = mailConfigData.mail_driver = val)
          "
          @submit-data="saveEmailConfig"
        >
          <sw-button
            variant="primary-outline"
            type="button"
            class="ml-2"
            @click="openMailTestModal"
          >
            {{ $t('general.test_mail_conf') }}
          </sw-button>
        </component>
      </div>
    </sw-card>
  </div>
</template>

<script>
import Smtp from './mail-driver/SmtpMailDriver'
import Mailgun from './mail-driver/MailgunMailDriver'
import Ses from './mail-driver/SesMailDriver'
import Basic from './mail-driver/BasicMailDriver'
import { mapActions } from 'vuex'

export default {
  components: {
    Smtp,
    Mailgun,
    Ses,
    sendmail: Basic,
    mail: Basic,
  },

  data() {
    return {
      mailConfigData: null,
      mail_driver: 'smtp',
      isLoading: false,
      isRequestOnGoing: false,
      mail_drivers: [],
    }
  },

  mounted() {
    this.loadData()
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('company', [
      'fetchMailDrivers',
      'fetchMailConfig',
      'updateMailConfig',
    ]),
    ...mapActions('notification', ['showNotification']),

    async loadData() {
      this.isRequestOnGoing = true
      let mailDrivers = await this.fetchMailDrivers()

      let mailData = await this.fetchMailConfig()

      if (mailDrivers.data) {
        this.mail_drivers = mailDrivers.data
      }

      if (mailData.data) {
        this.mailConfigData = mailData.data
        this.mail_driver = mailData.data.mail_driver
      }
      this.isRequestOnGoing = false
    },

    async saveEmailConfig(mailConfigData) {
      try {
        this.isLoading = true
        let response = await this.updateMailConfig(mailConfigData)
        if (response.data.success) {
          this.isLoading = false
          this.showNotification({
            type: 'success',
            message: this.$t('wizard.success.' + response.data.success),
          })
        } else {
          this.showNotification({
            type: 'error',
            message: this.$t('wizard.errors.' + response.data.error),
          })
        }
        return true
      } catch (e) {
        this.showNotification({
          type: 'error',
          message: 'Something went wrong',
        })
      }
    },

    openMailTestModal() {
      this.openModal({
        title: 'Test Mail Configuration',
        componentName: 'MailTestModal',
      })
    },
  },
}
</script>

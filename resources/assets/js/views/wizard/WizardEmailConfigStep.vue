<template>
  <sw-wizard-step
    :title="$t('wizard.mail.mail_config')"
    :description="$t('wizard.mail.mail_config_desc')"
  >
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <form action="" @submit.prevent="next()">
      <component
        :is="mail_driver"
        :config-data="mailConfigData"
        :loading="isLoading"
        :mail-drivers="mail_drivers"
        @on-change-driver="
          (val) => (mail_driver = mailConfigData.mail_driver = val)
        "
        @submit-data="next"
      />
    </form>
  </sw-wizard-step>
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
      mailConfigData: {
        mail_driver: 'mail',
      },
      mail_driver: 'mail',
      isLoading: false,
      isFetching: false,
      mail_drivers: [],
    }
  },
  created() {
    this.getMailDrivers()
  },
  methods: {
    ...mapActions('notification', ['showNotification']),
    async getMailDrivers() {
      this.isLoading = this.isFetching = true

      let response = await window.axios.get('/api/v1/mail/drivers')

      if (response.data) {
        this.mail_drivers = response.data
        this.isLoading = this.isFetching = false
      }
    },
    async next(mailConfigData) {
      this.isLoading = this.isFetching = true
      try {
        let response = await window.axios.post(
          '/api/v1/mail/config',
          mailConfigData
        )
        if (response.data.success) {
          this.$emit('next', 5)
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
        this.isLoading = this.isFetching = false
        return true
      } catch (e) {
        this.isLoading = this.isFetching = false
        this.showNotification({
          type: 'error',
          message: 'Something went wrong',
        })
      }
    },
  },
}
</script>

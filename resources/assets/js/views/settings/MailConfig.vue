<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.mail.mail_config') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.mail.mail_config_desc') }}
        </p>
      </div>
      <div v-if="mailConfigData">
        <component
          :is="mail_driver"
          :config-data="mailConfigData"
          :loading="loading"
          :mail-drivers="mail_drivers"
          @on-change-driver="(val) => mail_driver = mailConfigData.mail_driver = val"
          @submit-data="saveEmailConfig"
        />
      </div>
    </div>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
import Smtp from './mailDriver/Smtp'
import Mailgun from './mailDriver/Mailgun'
import Ses from './mailDriver/Ses'
import Basic from './mailDriver/Basic'

export default {
  components: {
    MultiSelect,
    Smtp,
    Mailgun,
    Ses,
    sendmail: Basic,
    mail: Basic
  },
  mixins: [validationMixin],
  data () {
    return {
      mailConfigData: null,
      mail_driver: 'smtp',
      loading: false,
      mail_drivers: []
    }
  },
  mounted () {
    this.loadData()
  },
  methods: {
    async loadData () {
      this.loading = true

      let mailDrivers = await window.axios.get('/api/settings/environment/mail')
      let mailData = await window.axios.get('/api/settings/environment/mail-env')

      if (mailDrivers.data) {
        this.mail_drivers = mailDrivers.data
      }
      if (mailData.data) {
        this.mailConfigData = mailData.data
        this.mail_driver = mailData.data.mail_driver
      }
      this.loading = false
    },
    async saveEmailConfig (mailConfigData) {
      this.loading = true
      try {
        let response = await window.axios.post('/api/settings/environment/mail', mailConfigData)
        if (response.data.success) {
          window.toastr['success'](this.$t('wizard.success.' + response.data.success))
        } else {
          window.toastr['error'](this.$t('wizard.errors.' + response.data.error))
        }
        this.loading = false
        return true
      } catch (e) {
        window.toastr['error']('Something went wrong')
      }
    }
  }
}
</script>

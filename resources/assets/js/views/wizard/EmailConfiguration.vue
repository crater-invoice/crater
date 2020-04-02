<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.mail.mail_config') }}</p>
      <p class="form-desc">{{ $t('wizard.mail.mail_config_desc') }}</p>
      <component
        :is="mail_driver"
        :config-data="mailConfigData"
        :loading="loading"
        :mail-drivers="mail_drivers"
        @on-change-driver="(val) => mail_driver = mailConfigData.mail_driver = val"
        @submit-data="next"
      />
    </form>
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
      mailConfigData: {
        mail_driver: 'mail'
      },
      mail_driver: 'mail',
      loading: false,
      mail_drivers: []
    }
  },
  created () {
    this.getMailDrivers()
  },
  methods: {
    async getMailDrivers () {
      this.loading = true

      let response = await window.axios.get('/api/admin/onboarding/environment/mail')

      if (response.data) {
        this.mail_drivers = response.data
        this.loading = false
      }
    },
    async next (mailConfigData) {
      this.loading = true
      try {
        let response = await window.axios.post('/api/admin/onboarding/environment/mail', mailConfigData)
        if (response.data.success) {
          this.$emit('next')
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

<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.mail.mail_config') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.mail.mail_config_desc') }}
        </p>
      </div>
      <form action="" @submit.prevent="saveEmailConfig()">
        <div class="row my-2 mt-5">
          <div class="col-md-6 my-2">
            <label class="form-label">{{ $t('settings.mail.driver') }}</label>
            <span class="text-danger"> *</span>
            <base-select
              v-model="mailConfigData.mail_driver"
              :invalid="$v.mailConfigData.mail_driver.$error"
              :options="mail_drivers"
              :searchable="true"
              :show-labels="false"
              @change="$v.mailConfigData.mail_driver.$touch()"
            />
            <div v-if="$v.mailConfigData.mail_driver.$error">
              <span v-if="!$v.mailConfigData.mail_driver.required" class="text-danger">
                {{ $tc('validation.required') }}
              </span>
            </div>
          </div>
          <div class="col-md-6 my-2">
            <label class="form-label">{{ $t('settings.mail.host') }}</label>
            <span class="text-danger"> *</span>
            <base-input
              :invalid="$v.mailConfigData.mail_host.$error"
              v-model.trim="mailConfigData.mail_host"
              type="text"
              name="mail_host"
              @input="$v.mailConfigData.mail_host.$touch()"
            />
            <div v-if="$v.mailConfigData.mail_host.$error">
              <span v-if="!$v.mailConfigData.mail_host.required" class="text-danger">
                {{ $tc('validation.required') }}
              </span>
            </div>
          </div>
        </div>
        <div class="row my-2">
          <div class="col-md-6 my-2">
            <label class="form-label">{{ $t('settings.mail.username') }}</label>
            <span class="text-danger"> *</span>
            <base-input
              :invalid="$v.mailConfigData.mail_username.$error"
              v-model.trim="mailConfigData.mail_username"
              type="text"
              name="db_name"
              @input="$v.mailConfigData.mail_username.$touch()"
            />
            <div v-if="$v.mailConfigData.mail_username.$error">
              <span v-if="!$v.mailConfigData.mail_username.required" class="text-danger">
                {{ $tc('validation.required') }}
              </span>
            </div>
          </div>
          <div class="col-md-6 my-2">
            <label class="form-label">{{ $t('settings.mail.password') }}</label>
            <span class="text-danger"> *</span>
            <base-input
              :invalid="$v.mailConfigData.mail_password.$error"
              v-model.trim="mailConfigData.mail_password"
              type="password"
              name="name"
              show-password
              @input="$v.mailConfigData.mail_password.$touch()"
            />
            <div v-if="$v.mailConfigData.mail_password.$error">
              <span v-if="!$v.mailConfigData.mail_password.required" class="text-danger">
                {{ $tc('validation.required') }}
              </span>
            </div>
          </div>
        </div>
        <div class="row my-2">
          <div class="col-md-6 my-2">
            <label class="form-label">{{ $t('settings.mail.port') }}</label>
            <span class="text-danger"> *</span>
            <base-input
              :invalid="$v.mailConfigData.mail_port.$error"
              v-model.trim="mailConfigData.mail_port"
              type="text"
              name="mail_port"
              @input="$v.mailConfigData.mail_port.$touch()"
            />
            <div v-if="$v.mailConfigData.mail_port.$error">
              <span v-if="!$v.mailConfigData.mail_port.required" class="text-danger">
                {{ $tc('validation.required') }}
              </span>
              <span v-if="!$v.mailConfigData.mail_port.numeric" class="text-danger">
                {{ $tc('validation.numbers_only') }}
              </span>
            </div>
          </div>
          <div class="col-md-6 my-2">
            <label class="form-label">{{ $t('settings.mail.encryption') }}</label>
            <span class="text-danger"> *</span>
            <base-input
              :invalid="$v.mailConfigData.mail_encryption.$error"
              v-model.trim="mailConfigData.mail_encryption"
              type="text"
              name="name"
              @input="$v.mailConfigData.mail_encryption.$touch()"
            />
            <div v-if="$v.mailConfigData.mail_encryption.$error">
              <span v-if="!$v.mailConfigData.mail_encryption.required" class="text-danger">
                {{ $tc('validation.required') }}
              </span>
            </div>
          </div>
        </div>
        <base-button
          :loading="loading"
          class="pull-right mt-5"
          icon="save"
          color="theme"
          type="submit"
        >
          {{ $t('general.save') }}
        </base-button>
      </form>
    </div>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
import Ls from '../../services/ls'
const { required, email, numeric } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  data () {
    return {
      mailConfigData: {
        mail_driver: '',
        mail_host: '',
        mail_port: null,
        mail_username: '',
        mail_password: '',
        mail_encryption: ''
      },
      loading: false,
      mail_drivers: []
    }
  },
  validations: {
    mailConfigData: {
      mail_driver: {
        required
      },
      mail_host: {
        required
      },
      mail_port: {
        required,
        numeric
      },
      mail_username: {
        required
      },
      mail_password: {
        required
      },
      mail_encryption: {
        required
      }
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
      }
      this.loading = false
    },
    async saveEmailConfig () {
      this.$v.mailConfigData.$touch()
      if (this.$v.mailConfigData.$invalid) {
        return true
      }
      this.loading = true
      try {
        let response = await window.axios.post('/api/settings/environment/mail', this.mailConfigData)
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

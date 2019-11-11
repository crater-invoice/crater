<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.mail.mail_config') }}</p>
      <p class="form-desc">{{ $t('wizard.mail.mail_config_desc') }}</p>
      <div class="row my-2 mt-5">
        <div class="col-md-6 my-2">
          <label class="form-label">{{ $t('wizard.mail.driver') }}</label>
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
          <label class="form-label">{{ $t('wizard.mail.host') }}</label>
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
          <label class="form-label">{{ $t('wizard.mail.username') }}</label>
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
          <label class="form-label">{{ $t('wizard.mail.password') }}</label>
          <span class="text-danger"> *</span>
          <base-input
            :invalid="$v.mailConfigData.mail_password.$error"
            v-model.trim="mailConfigData.mail_password"
            type="mail_password"
            name="name"
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
          <label class="form-label">{{ $t('wizard.mail.port') }}</label>
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
          <label class="form-label">{{ $t('wizard.mail.encryption') }}</label>
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
        {{ $t('wizard.save_cont') }}
      </base-button>
    </form>
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
        mail_driver: 'smtp',
        mail_host: 'mailtrap.io',
        mail_port: 2525,
        mail_username: 'cc3c64516febd4',
        mail_password: 'e6a0176301f587',
        mail_encryption: 'tls'
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
    async next () {
      this.$v.mailConfigData.$touch()
      if (this.$v.mailConfigData.$invalid) {
        return true
      }
      this.loading = true
      try {
        let response = await window.axios.post('/api/admin/onboarding/environment/mail', this.mailConfigData)
        if (response.data.success) {
          this.$emit('next')
          window.toastr['success'](this.$t('wizard.success.' + response.data.success))
        } else {
          window.toastr['error'](this.$t('wizard.errors.' + response.data.error))
        }
        this.loading = false
        return true
      } catch (e) {
        window.toastr['error']('Somethig went wrong')
      }
    }
  }
}
</script>

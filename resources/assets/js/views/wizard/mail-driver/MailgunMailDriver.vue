<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:mb-6 md:mb-6">
      <sw-input-group
        :label="$t('wizard.mail.driver')"
        :error="driverError"
        required
      >
        <sw-select
          v-model="mailConfigData.mail_driver"
          :invalid="$v.mailConfigData.mail_driver.$error"
          :options="mailDrivers"
          :searchable="true"
          :allow-empty="false"
          :show-labels="false"
          @input="onChangeDriver"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('wizard.mail.mailgun_domain')"
        :error="domainError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.mail_mailgun_domain"
          :invalid="$v.mailConfigData.mail_mailgun_domain.$error"
          type="text"
          name="mailgun_domain"
          @input="$v.mailConfigData.mail_mailgun_domain.$touch()"
        />
      </sw-input-group>
    </div>
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:mb-6 md:mb-6">
      <sw-input-group
        :label="$t('wizard.mail.mailgun_secret')"
        :error="secretError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.mail_mailgun_secret"
          :invalid="$v.mailConfigData.mail_mailgun_secret.$error"
          :type="getInputType"
          name="mailgun_secret"
          autocomplete="off"
          data-lpignore="true"
          @input="$v.mailConfigData.mail_mailgun_secret.$touch()"
        >
          <template v-slot:rightIcon>
            <eye-off-icon
              v-if="isShowPassword"
              class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
              @click="isShowPassword = !isShowPassword"
            />
            <eye-icon
              v-else
              class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
              @click="isShowPassword = !isShowPassword"
            />
          </template>
        </sw-input>
      </sw-input-group>

      <sw-input-group
        :label="$t('wizard.mail.mailgun_endpoint')"
        :error="endpointError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.mail_mailgun_endpoint"
          :invalid="$v.mailConfigData.mail_mailgun_endpoint.$error"
          type="text"
          name="mailgun_endpoint"
          @input="$v.mailConfigData.mail_mailgun_endpoint.$touch()"
        />
      </sw-input-group>
    </div>
    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
      <sw-input-group
        :label="$t('wizard.mail.from_mail')"
        :error="fromMailError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.from_mail"
          :invalid="$v.mailConfigData.from_mail.$error"
          type="text"
          name="from_mail"
          @input="$v.mailConfigData.from_mail.$touch()"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('wizard.mail.from_name')"
        :error="fromNameError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.from_name"
          :invalid="$v.mailConfigData.from_name.$error"
          type="text"
          name="from_name"
          @input="$v.mailConfigData.from_name.$touch()"
        />
      </sw-input-group>
    </div>
    <sw-button
      :loading="loading"
      :disabled="loading"
      variant="primary"
      type="submit"
      class="mt-4"
    >
      <save-icon v-if="!loading" class="mr-2" />
      {{ $t('general.save') }}
    </sw-button>
  </form>
</template>
<script>
const { required, email } = require('vuelidate/lib/validators')
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    EyeIcon,
    EyeOffIcon,
  },
  props: {
    configData: {
      type: Object,
      require: true,
      default: Object,
    },
    loading: {
      type: Boolean,
      require: true,
      default: false,
    },
    mailDrivers: {
      type: Array,
      require: true,
      default: Array,
    },
  },
  data() {
    return {
      isShowPassword: false,
      mailConfigData: {
        mail_driver: '',
        mail_mailgun_domain: '',
        mail_mailgun_secret: '',
        mail_mailgun_endpoint: '',
        from_mail: '',
        from_name: '',
      },
    }
  },
  validations: {
    mailConfigData: {
      mail_driver: {
        required,
      },
      mail_mailgun_domain: {
        required,
      },
      mail_mailgun_endpoint: {
        required,
      },
      mail_mailgun_secret: {
        required,
      },
      from_mail: {
        required,
        email,
      },
      from_name: {
        required,
      },
    },
  },
  computed: {
    driverError() {
      if (!this.$v.mailConfigData.mail_driver.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_driver.required) {
        return tthis.$tc('validation.required')
      }
    },
    domainError() {
      if (!this.$v.mailConfigData.mail_mailgun_domain.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_mailgun_domain.required) {
        return this.$tc('validation.required')
      }
    },
    secretError() {
      if (!this.$v.mailConfigData.mail_mailgun_secret.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_mailgun_secret.required) {
        return this.$tc('validation.required')
      }
    },
    endpointError() {
      if (!this.$v.mailConfigData.mail_mailgun_endpoint.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_mailgun_endpoint.required) {
        return this.$tc('validation.required')
      }
    },
    fromMailError() {
      if (!this.$v.mailConfigData.from_mail.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.from_mail.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.mailConfigData.from_mail.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    fromNameError() {
      if (!this.$v.mailConfigData.from_name.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.from_name.required) {
        return this.$tc('validation.required')
      }
    },
    getInputType() {
      if (this.isShowPassword) {
        return 'text'
      }
      return 'password'
    },
  },
  mounted() {
    for (const key in this.mailConfigData) {
      if (this.configData.hasOwnProperty(key)) {
        this.mailConfigData[key] = this.configData[key]
      }
    }
  },
  methods: {
    async saveEmailConfig() {
      this.$v.mailConfigData.$touch()
      if (!this.$v.mailConfigData.$invalid) {
        this.$emit('submit-data', this.mailConfigData)
      }

      return false
    },
    onChangeDriver() {
      this.$v.mailConfigData.mail_driver.$touch()
      this.$emit('on-change-driver', this.mailConfigData.mail_driver)
    },
  },
}
</script>

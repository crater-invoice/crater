<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid gap-6 grid-col-1 md:grid-cols-2">
      <sw-input-group
        :label="$t('settings.mail.driver')"
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
          class="mt-2"
          @input="onChangeDriver"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.mail.host')"
        :error="hostError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.mail_host"
          :invalid="$v.mailConfigData.mail_host.$error"
          type="text"
          name="mail_host"
          class="mt-2"
          @input="$v.mailConfigData.mail_host.$touch()"
        />
      </sw-input-group>

      <sw-input-group :label="$t('settings.mail.username')">
        <sw-input
          v-model.trim="mailConfigData.mail_username"
          type="text"
          name="db_name"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group :label="$t('settings.mail.password')">
        <sw-input
          v-model.trim="mailConfigData.mail_password"
          :type="getInputType"
          name="password"
          class="mt-2"
          autocomplete="off"
          data-lpignore="true"
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
        :label="$t('settings.mail.port')"
        :error="portError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.mail_port"
          :invalid="$v.mailConfigData.mail_port.$error"
          type="text"
          name="mail_port"
          class="mt-2"
          @input="$v.mailConfigData.mail_port.$touch()"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.mail.encryption')"
        :error="encryptionError"
        required
      >
        <sw-select
          v-model.trim="mailConfigData.mail_encryption"
          :invalid="$v.mailConfigData.mail_encryption.$error"
          :options="encryptions"
          :searchable="true"
          :show-labels="false"
          class="mt-2"
          @input="$v.mailConfigData.mail_encryption.$touch()"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.mail.from_mail')"
        :error="fromEmailError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.from_mail"
          :invalid="$v.mailConfigData.from_mail.$error"
          type="text"
          name="from_mail"
          class="mt-2"
          autocomplete="off"
          data-lpignore="true"
          @input="$v.mailConfigData.from_mail.$touch()"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.mail.from_name')"
        :error="fromNameError"
        required
      >
        <sw-input
          v-model.trim="mailConfigData.from_name"
          :invalid="$v.mailConfigData.from_name.$error"
          type="text"
          name="from_name"
          class="mt-2"
          @input="$v.mailConfigData.from_name.$touch()"
        />
      </sw-input-group>
    </div>

    <div class="flex my-10">
      <sw-button
        :disabled="loading"
        :loading="loading"
        type="submit"
        variant="primary"
      >
        <save-icon class="mr-2" />
        {{ $t('general.save') }}
      </sw-button>
      <slot />
    </div>
  </form>
</template>

<script>
const { required, email, numeric } = require('vuelidate/lib/validators')
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
      mailConfigData: {
        mail_driver: '',
        mail_host: '',
        mail_port: null,
        mail_username: '',
        mail_password: '',
        mail_encryption: 'tls',
        from_mail: '',
        from_name: '',
      },
      isShowPassword: false,
      encryptions: ['tls', 'ssl', 'starttls'],
    }
  },
  validations: {
    mailConfigData: {
      mail_driver: {
        required,
      },
      mail_host: {
        required,
      },
      mail_port: {
        required,
        numeric,
      },
      mail_encryption: {
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
        return this.$tc('validation.required')
      }
    },
    hostError() {
      if (!this.$v.mailConfigData.mail_host.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_host.required) {
        return this.$tc('validation.required')
      }
    },
    portError() {
      if (!this.$v.mailConfigData.mail_port.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_port.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.mailConfigData.mail_port.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    encryptionError() {
      if (!this.$v.mailConfigData.mail_encryption.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_encryption.required) {
        return this.$tc('validation.required')
      }
    },
    fromEmailError() {
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

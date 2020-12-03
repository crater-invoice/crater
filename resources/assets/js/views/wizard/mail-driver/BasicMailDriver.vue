<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
      <sw-input-group
        :label="$t('wizard.mail.driver')"
        :error="driverError"
        required
      >
        <sw-select
          v-model="mailConfigData.mail_driver"
          :invalid="$v.mailConfigData.mail_driver.$error"
          :options="mailDrivers"
          :allow-empty="false"
          :searchable="true"
          :show-labels="false"
          @input="onChangeDriver"
        />
      </sw-input-group>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
      <sw-input-group
        :label="$t('wizard.mail.from_name')"
        :error="fromNameError"
        required
      >
        <sw-input
          :invalid="$v.mailConfigData.from_name.$error"
          v-model.trim="mailConfigData.from_name"
          type="text"
          name="name"
          @input="$v.mailConfigData.from_name.$touch()"
        />
      </sw-input-group>
      <sw-input-group
        :label="$t('wizard.mail.from_mail')"
        :error="fromMailError"
        required
      >
        <sw-input
          :invalid="$v.mailConfigData.from_mail.$error"
          v-model.trim="mailConfigData.from_mail"
          type="text"
          name="from_mail"
          @input="$v.mailConfigData.from_mail.$touch()"
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

export default {
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

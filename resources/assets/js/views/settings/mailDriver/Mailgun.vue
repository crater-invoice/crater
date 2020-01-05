<template>
  <form @submit.prevent="saveEmailConfig()">
    <div class="row">
      <div class="col-md-6 my-2">
        <label class="form-label">{{ $t('settings.mail.driver') }}</label>
        <span class="text-danger"> *</span>
        <base-select
          v-model="mailConfigData.mail_driver"
          :invalid="$v.mailConfigData.mail_driver.$error"
          :options="mailDrivers"
          :allow-empty="false"
          :searchable="true"
          :show-labels="false"
          @input="onChangeDriver"
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
        <base-select
          v-model.trim="mailConfigData.mail_encryption"
          :invalid="$v.mailConfigData.mail_encryption.$error"
          :options="encryptions"
          :searchable="true"
          :show-labels="false"
          @input="$v.mailConfigData.mail_encryption.$touch()"
        />
        <div v-if="$v.mailConfigData.mail_encryption.$error">
          <span v-if="!$v.mailConfigData.mail_encryption.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
        </div>
      </div>
    </div>
    <div class="row my-2">
      <div class="col-md-6 my-2">
        <label class="form-label">{{ $t('settings.mail.from_mail') }}</label>
        <span class="text-danger"> *</span>
        <base-input
          :invalid="$v.mailConfigData.from_mail.$error"
          v-model.trim="mailConfigData.from_mail"
          type="text"
          name="from_mail"
          @input="$v.mailConfigData.from_mail.$touch()"
        />
        <div v-if="$v.mailConfigData.from_mail.$error">
          <span v-if="!$v.mailConfigData.from_mail.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
          <span v-if="!$v.mailConfigData.from_mail.email" class="text-danger">
            {{ $tc('validation.email_incorrect') }}
          </span>
        </div>
      </div>
      <div class="col-md-6 my-2">
        <label class="form-label">{{ $t('settings.mail.from_name') }}</label>
        <span class="text-danger"> *</span>
        <base-input
          :invalid="$v.mailConfigData.from_name.$error"
          v-model.trim="mailConfigData.from_name"
          type="text"
          name="from_name"
          @input="$v.mailConfigData.from_name.$touch()"
        />
        <div v-if="$v.mailConfigData.from_name.$error">
          <span v-if="!$v.mailConfigData.from_name.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
        </div>
      </div>
    </div>
    <div class="row my-2">
      <div class="col-md-6 my-2">
        <label class="form-label">{{ $t('settings.mail.mailgun_domain') }}</label>
        <span class="text-danger"> *</span>
        <base-input
          :invalid="$v.mailConfigData.mail_mailgun_domain.$error"
          v-model.trim="mailConfigData.mail_mailgun_domain"
          type="text"
          name="mailgun_domain"
          @input="$v.mailConfigData.mail_mailgun_domain.$touch()"
        />
        <div v-if="$v.mailConfigData.mail_mailgun_domain.$error">
          <span v-if="!$v.mailConfigData.mail_mailgun_domain.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
        </div>
      </div>
      <div class="col-md-6 my-2">
        <label class="form-label">{{ $t('settings.mail.mailgun_secret') }}</label>
        <span class="text-danger"> *</span>
        <base-input
          :invalid="$v.mailConfigData.mail_mailgun_secret.$error"
          v-model.trim="mailConfigData.mail_mailgun_secret"
          type="password"
          name="mailgun_secret"
          show-password
          @input="$v.mailConfigData.mail_mailgun_secret.$touch()"
        />
        <div v-if="$v.mailConfigData.mail_mailgun_secret.$error">
          <span v-if="!$v.mailConfigData.mail_mailgun_secret.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
        </div>
      </div>
    </div>
    <div class="row my-2">
      <div class="col-md-6 my-2">
        <label class="form-label">{{ $t('settings.mail.mailgun_endpoint') }}</label>
        <span class="text-danger"> *</span>
        <base-input
          :invalid="$v.mailConfigData.mail_mailgun_endpoint.$error"
          v-model.trim="mailConfigData.mail_mailgun_endpoint"
          type="text"
          name="mailgun_endpoint"
          @input="$v.mailConfigData.mail_mailgun_endpoint.$touch()"
        />
        <div v-if="$v.mailConfigData.mail_mailgun_endpoint.$error">
          <span v-if="!$v.mailConfigData.mail_mailgun_endpoint.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
          <span v-if="!$v.mailConfigData.mail_mailgun_endpoint.numeric" class="text-danger">
            {{ $tc('validation.numbers_only') }}
          </span>
        </div>
      </div>
    </div>
    <div class="d-flex">
      <base-button
        :loading="loading"
        class="pull-right mt-4"
        icon="save"
        color="theme"
        type="submit"
      >
        {{ $t('general.save') }}
      </base-button>
      <slot/>
    </div>
  </form>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
const { required, email, numeric } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  props: {
    configData: {
      type: Object,
      require: true,
      default: Object
    },
    loading: {
      type: Boolean,
      require: true,
      default: false
    },
    mailDrivers: {
      type: Array,
      require: true,
      default: Array
    }
  },
  data () {
    return {
      mailConfigData: {
        mail_driver: '',
        mail_host: '',
        mail_port: null,
        mail_mailgun_domain: '',
        mail_mailgun_secret: '',
        mail_mailgun_endpoint: '',
        mail_encryption: 'tls',
        from_mail: '',
        from_name: ''
      },
      encryptions: ['tls', 'ssl', 'starttls']
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
      mail_mailgun_domain: {
        required
      },
      mail_mailgun_endpoint: {
        required
      },
      mail_mailgun_secret: {
        required
      },
      mail_encryption: {
        required
      },
      from_mail: {
        required,
        email
      },
      from_name: {
        required
      }
    }
  },
  mounted () {
    for (const key in this.mailConfigData) {
      if (this.configData.hasOwnProperty(key)) {
        this.mailConfigData[key] = this.configData[key]
      }
    }
  },
  methods: {
    async saveEmailConfig () {
      this.$v.mailConfigData.$touch()
      if (!this.$v.mailConfigData.$invalid) {
        this.$emit('submit-data', this.mailConfigData)
      }

      return false
    },
    onChangeDriver () {
      this.$v.mailConfigData.mail_driver.$touch()
      this.$emit('on-change-driver', this.mailConfigData.mail_driver)
    }
  }
}
</script>

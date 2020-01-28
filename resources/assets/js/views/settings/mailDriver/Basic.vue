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
          :searchable="true"
          :allow-empty="false"
          :show-labels="false"
          @input="onChangeDriver"
        />
        <div v-if="$v.mailConfigData.mail_driver.$error">
          <span v-if="!$v.mailConfigData.mail_driver.required" class="text-danger">
            {{ $tc('validation.required') }}
          </span>
        </div>
      </div>
      <!-- <div class="col-md-6 my-2">
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
      </div> -->
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
          name="name"
          @input="$v.mailConfigData.from_name.$touch()"
        />
        <div v-if="$v.mailConfigData.from_name.$error">
          <span v-if="!$v.mailConfigData.from_name.required" class="text-danger">
            {{ $tc('validation.required') }}
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
const { required, email } = require('vuelidate/lib/validators')

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
        from_mail: '',
        from_name: ''
      }
    }
  },
  validations: {
    mailConfigData: {
      mail_driver: {
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

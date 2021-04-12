<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateEstimateSetting">
      <sw-input-group
        :label="$t('settings.customization.estimates.estimate_prefix')"
        :error="estimatePrefixError"
      >
        <sw-input
          v-model="estimates.estimate_prefix"
          :invalid="$v.estimates.estimate_prefix.$error"
          style="max-width: 30%"
          @input="$v.estimates.estimate_prefix.$touch()"
          @keyup="changeToUppercase('ESTIMATES')"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.estimates.estimate_number_length')"
        :error="estimateNumberLengthError"
        class="mt-6 mb-4"
      >
        <sw-input
          v-model="estimates.estimate_number_length"
          :invalid="$v.estimates.estimate_number_length.$error"
          type="number"
          style="max-width: 60px"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.estimates.default_estimate_email_body')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="estimates.estimate_mail_body"
          :fields="mailFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.estimates.company_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="estimates.company_address_format"
          :fields="companyFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.estimates.shipping_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="estimates.shipping_address_format"
          :fields="shippingFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.estimates.billing_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="estimates.billing_address_format"
          :fields="billingFields"
        />
      </sw-input-group>

      <sw-button
        :disabled="isLoading"
        :loading="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <div class="flex mt-3 mb-4">
      <div class="relative w-12">
        <sw-switch
          v-model="estimateAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setEstimateSetting"
        />
      </div>
      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.estimates.autogenerate_estimate_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.estimates.estimate_setting_description')
          }}
        </p>
      </div>
    </div>
    <div class="flex mb-2">
      <div class="relative w-12">
        <sw-switch
          v-model="estimateAsAttachment"
          class="absolute"
          style="top: -20px"
          @change="setEstimateSetting"
        />
      </div>
      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{ $t('settings.customization.estimates.estimate_email_attachment') }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t(
              'settings.customization.estimates.estimate_email_attachment_setting_description'
            )
          }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
const {
  required,
  maxLength,
  minValue,
  alpha,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      estimateAutogenerate: false,
      estimateAsAttachment: false,

      estimates: {
        estimate_prefix: null,
        estimate_number_length: null,
        estimate_mail_body: null,
        estimate_terms_and_conditions: null,
        company_address_format: null,
        shipping_address_format: null,
        billing_address_format: null,
      },
      billingFields: [
        'billing',
        'customer',
        'customerCustom',
        'estimateCustom',
      ],
      shippingFields: [
        'shipping',
        'customer',
        'customerCustom',
        'estimateCustom',
      ],
      mailFields: [
        'customer',
        'estimate',
        'company',
        'customerCustom',
        'estimateCustom',
      ],
      companyFields: ['company', 'estimateCustom'],
      isLoading: false,
    }
  },

  computed: {
    estimatePrefixError() {
      if (!this.$v.estimates.estimate_prefix.$error) {
        return ''
      }

      if (!this.$v.estimates.estimate_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.estimates.estimate_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.estimates.estimate_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
    estimateNumberLengthError() {
      if (!this.$v.estimates.estimate_number_length.$error) {
        return ''
      }

      if (!this.$v.estimates.estimate_number_length.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.estimates.estimate_number_length.minValue) {
        return this.$t('validation.number_length_minvalue')
      }

      if (!this.$v.estimates.estimate_number_length.numeric) {
        return this.$t('validation.numbers_only')
      }
    },
  },

  validations: {
    estimates: {
      estimate_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
      estimate_number_length: {
        required,
        minValue: minValue(1),
        numeric,
      },
    },
  },

  watch: {
    settings(val) {
      this.estimates.estimate_prefix = val ? val.estimate_prefix : ''
      this.estimates.estimate_number_length = val
        ? val.estimate_number_length
        : ''

      this.estimates.estimate_mail_body = val ? val.estimate_mail_body : ''
      this.estimates.company_address_format = val
        ? val.estimate_company_address_format
        : ''
      this.estimates.shipping_address_format = val
        ? val.estimate_shipping_address_format
        : ''
      this.estimates.billing_address_format = val
        ? val.estimate_billing_address_format
        : ''

      this.estimates.estimate_terms_and_conditions = val
        ? val.estimate_terms_and_conditions
        : ''

      this.estimate_auto_generate = val ? val.estimate_auto_generate : ''

      if (this.estimate_auto_generate === 'YES') {
        this.estimateAutogenerate = true
      } else {
        this.estimateAutogenerate = false
      }

      this.estimate_email_attachment = val ? val.estimate_email_attachment : ''

      if (this.estimate_email_attachment === 'YES') {
        this.estimateAsAttachment = true
      } else {
        this.estimateAsAttachment = false
      }
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('notification', ['showNotification']),
    async setEstimateSetting() {
      let data = {
        settings: {
          estimate_auto_generate: this.estimateAutogenerate ? 'YES' : 'NO',
          estimate_email_attachment: this.estimateAsAttachment ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        this.showNotification({
          type: 'success',
          message: this.$t('general.setting_updated'),
        })
      }
    },

    changeToUppercase(currentTab) {
      if (currentTab === 'ESTIMATES') {
        this.estimates.estimate_prefix = this.estimates.estimate_prefix.toUpperCase()
        return true
      }
    },

    async updateEstimateSetting() {
      this.$v.estimates.$touch()

      if (this.$v.estimates.$invalid) {
        return false
      }

      let data = {
        settings: {
          estimate_prefix: this.estimates.estimate_prefix,
          estimate_number_length: this.estimates.estimate_number_length,
          estimate_mail_body: this.estimates.estimate_mail_body,
          estimate_company_address_format: this.estimates
            .company_address_format,
          estimate_shipping_address_format: this.estimates
            .shipping_address_format,
          estimate_billing_address_format: this.estimates
            .billing_address_format,
        },
      }

      if (this.updateSetting(data)) {
        this.showNotification({
          type: 'success',
          message: this.$t(
            'settings.customization.estimates.estimate_setting_updated'
          ),
        })
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },
  },
}
</script>

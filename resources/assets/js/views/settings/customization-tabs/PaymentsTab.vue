<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updatePaymentSetting">
      <sw-input-group
        :label="$t('settings.customization.payments.payment_prefix')"
        :error="paymentPrefixError"
      >
        <sw-input
          v-model="payments.payment_prefix"
          :invalid="$v.payments.payment_prefix.$error"
          class="mt-2"
          style="max-width: 30%"
          @input="$v.payments.payment_prefix.$touch()"
          @keyup="changeToUppercase('PAYMENTS')"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.payments.default_payment_email_body')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="payments.payment_mail_body"
          :fields="mailFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.payments.company_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="payments.company_address_format"
          :fields="companyFields"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.payments.from_customer_address_format')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="payments.from_customer_address_format"
          :fields="customerAddressFields"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="my-4"
      >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <sw-divider class="mt-6 mb-8" />

    <div class="flex">
      <div class="relative w-12">
        <sw-switch
          v-model="paymentAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setPaymentSetting"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.payments.autogenerate_payment_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.payments.payment_setting_description')
          }}
        </p>
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
  },

  data() {
    return {
      paymentAutogenerate: false,

      payments: {
        payment_prefix: null,
        payment_mail_body: null,
        from_customer_address_format: null,
        company_address_format: null,
      },

      mailFields: [
        'customer',
        'customerCustom',
        'company',
        'payment',
        'paymentCustom',
      ],
      customerAddressFields: [
        'billing',
        'customer',
        'customerCustom',
        'paymentCustom',
      ],
      companyFields: ['company', 'paymentCustom'],
      isLoading: false,
    }
  },
  computed: {
    paymentPrefixError() {
      if (!this.$v.payments.payment_prefix.$error) {
        return ''
      }

      if (!this.$v.payments.payment_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.payments.payment_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.payments.payment_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    payments: {
      payment_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) {
      this.payments.payment_prefix = val ? val.payment_prefix : ''

      this.payments.payment_mail_body = val ? val.payment_mail_body : ''

      this.payments.company_address_format = val
        ? val.payment_company_address_format
        : ''

      this.payments.from_customer_address_format = val
        ? val.payment_from_customer_address_format
        : ''

      this.payment_auto_generate = val ? val.payment_auto_generate : ''

      if (this.payment_auto_generate === 'YES') {
        this.paymentAutogenerate = true
      } else {
        this.paymentAutogenerate = false
      }
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('company', ['updateCompanySettings']),

    changeToUppercase(currentTab) {
      if (currentTab === 'PAYMENTS') {
        this.payments.payment_prefix = this.payments.payment_prefix.toUpperCase()
        return true
      }
    },

    async setPaymentSetting() {
      let data = {
        settings: {
          payment_auto_generate: this.paymentAutogenerate ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async updatePaymentSetting() {
      this.$v.payments.$touch()

      if (this.$v.payments.$invalid) {
        return false
      }

      let data = {
        settings: {
          payment_prefix: this.payments.payment_prefix,
          payment_mail_body: this.payments.payment_mail_body,
          payment_company_address_format: this.payments.company_address_format,
          payment_from_customer_address_format: this.payments
            .from_customer_address_format,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.payments.payment_setting_updated')
        )
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

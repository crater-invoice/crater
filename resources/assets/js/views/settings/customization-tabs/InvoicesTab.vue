<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateInvoiceSetting">
      <sw-input-group
        :label="$t('settings.customization.invoices.invoice_prefix')"
        :error="invoicePrefixError"
      >
        <sw-input
          v-model="invoices.invoice_prefix"
          :invalid="$v.invoices.invoice_prefix.$error"
          style="max-width: 30%"
          @input="$v.invoices.invoice_prefix.$touch()"
          @keyup="changeToUppercase('INVOICES')"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.invoice_number_length')"
        :error="invoicenumberLengthError"
        class="mt-6 mb-4"
      >
        <sw-input
          v-model="invoices.invoice_number_length"
          :invalid="$v.invoices.invoice_number_length.$error"
          type="number"
          style="max-width: 60px"
        />
      </sw-input-group>

      <sw-input-group
        :label="
          $t('settings.customization.invoices.default_invoice_email_body')
        "
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.invoice_mail_body"
          :fields="InvoiceMailFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.company_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.company_address_format"
          :fields="companyFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.shipping_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.shipping_address_format"
          :fields="shippingFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-input-group
        :label="$t('settings.customization.invoices.billing_address_format')"
        class="mt-6 mb-4"
      >
        <base-custom-input
          v-model="invoices.billing_address_format"
          :fields="billingFields"
          class="mt-2"
        />
      </sw-input-group>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
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
          v-model="invoiceAutogenerate"
          class="absolute"
          style="top: -20px"
          @change="setInvoiceSetting"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{
            $t('settings.customization.invoices.autogenerate_invoice_number')
          }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t('settings.customization.invoices.invoice_setting_description')
          }}
        </p>
      </div>
    </div>
    <div class="flex mb-2">
      <div class="relative w-12">
        <sw-switch
          v-model="invoiceAsAttachment"
          class="absolute"
          style="top: -20px"
          @change="setInvoiceSetting"
        />
      </div>

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black">
          {{ $t('settings.customization.invoices.invoice_email_attachment') }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-tight text-gray-500"
          style="max-width: 480px"
        >
          {{
            $t(
              'settings.customization.invoices.invoice_email_attachment_setting_description'
            )
          }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
const { required, maxLength, minValue, alpha, numeric } = require('vuelidate/lib/validators')
import { mapActions, mapGetters } from 'vuex'

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
      invoiceAutogenerate: false,
      invoiceAsAttachment: false,

      invoices: {
        invoice_prefix: null,
        invoice_number_length: null,
        invoice_mail_body: null,
        company_address_format: null,
        shipping_address_format: null,
        billing_address_format: null,
      },
      isLoading: false,
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'invoice',
        'invoiceCustom',
        'company',
      ],
      billingFields: ['billing', 'customer', 'customerCustom', 'invoiceCustom'],
      shippingFields: [
        'shipping',
        'customer',
        'customerCustom',
        'invoiceCustom',
      ],
      companyFields: ['company', 'invoiceCustom'],
    }
  },

  computed: {
    invoicePrefixError() {
      if (!this.$v.invoices.invoice_prefix.$error) {
        return ''
      }

      if (!this.$v.invoices.invoice_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.invoices.invoice_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.invoices.invoice_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
    invoicenumberLengthError() {
      if (!this.$v.invoices.invoice_number_length.$error) {
        return ''
      }

      if (!this.$v.invoices.invoice_number_length.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.invoices.invoice_number_length.minValue) {
        return this.$t('validation.number_length_minvalue')
      }

      if (!this.$v.invoices.invoice_number_length.numeric) {
        return this.$t('validation.numbers_only')
      }
    },
  },

  watch: {
    settings(val) {
      this.invoices.invoice_prefix = val ? val.invoice_prefix : ''
      this.invoices.invoice_number_length = val ? val.invoice_number_length : ''

      this.invoices.invoice_mail_body = val ? val.invoice_mail_body : null
      this.invoices.company_address_format = val
        ? val.invoice_company_address_format
        : null
      this.invoices.shipping_address_format = val
        ? val.invoice_shipping_address_format
        : null
      this.invoices.billing_address_format = val
        ? val.invoice_billing_address_format
        : null

      this.invoice_auto_generate = val ? val.invoice_auto_generate : ''

      if (this.invoice_auto_generate === 'YES') {
        this.invoiceAutogenerate = true
      } else {
        this.invoiceAutogenerate = false
      }

      this.invoice_email_attachment = val ? val.invoice_email_attachment : ''

      if (this.invoice_email_attachment === 'YES') {
        this.invoiceAsAttachment = true
      } else {
        this.invoiceAsAttachment = false
      }
    },
  },

  validations: {
    invoices: {
      invoice_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
      invoice_number_length: {
        required,
        minValue: minValue(1),
        numeric
      },
    },
  },

  methods: {
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('notification', ['showNotification']),

    async setInvoiceSetting() {
      let data = {
        settings: {
          invoice_auto_generate: this.invoiceAutogenerate ? 'YES' : 'NO',
          invoice_email_attachment: this.invoiceAsAttachment ? 'YES' : 'NO',
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
      if (currentTab === 'INVOICES') {
        this.invoices.invoice_prefix = this.invoices.invoice_prefix.toUpperCase()

        return true
      }
    },

    async updateInvoiceSetting() {
      this.$v.invoices.$touch()

      if (this.$v.invoices.$invalid) {
        return false
      }

      let data = {
        settings: {
          invoice_prefix: this.invoices.invoice_prefix,
          invoice_number_length: this.invoices.invoice_number_length,
          invoice_mail_body: this.invoices.invoice_mail_body,
          invoice_company_address_format: this.invoices.company_address_format,
          invoice_billing_address_format: this.invoices.billing_address_format,
          invoice_shipping_address_format: this.invoices
            .shipping_address_format,
        },
      }

      if (this.updateSetting(data)) {
        this.showNotification({
          type: 'success',
          message: this.$t(
            'settings.customization.invoices.invoice_setting_updated'
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

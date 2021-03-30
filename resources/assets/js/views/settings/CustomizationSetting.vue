<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card>
      <sw-tabs class="p-2">
        <!-- Invoices -->
        <sw-tab-item :title="$t('settings.customization.invoices.title')">
          <invoices-tab :settings="settings" />
        </sw-tab-item>

        <!-- Estimates -->
        <sw-tab-item :title="$t('settings.customization.estimates.title')">
          <estimates-tab :settings="settings" />
        </sw-tab-item>

        <!-- Payments -->
        <sw-tab-item :title="$t('settings.customization.payments.title')">
          <payments-tab :settings="settings" />
        </sw-tab-item>

        <!-- Items -->
        <sw-tab-item :title="$t('settings.customization.items.title')">
          <items-tab />
        </sw-tab-item>
      </sw-tabs>
    </sw-card>
  </div>
</template>

<script>
import InvoicesTab from './customization-tabs/InvoicesTab'
import EstimatesTab from './customization-tabs/EstimatesTab'
import PaymentsTab from './customization-tabs/PaymentsTab'
import ItemsTab from './customization-tabs/ItemsTab'
import { mapActions } from 'vuex'

export default {
  data() {
    return {
      settings: {},
      isRequestOnGoing: false,
    }
  },

  components: {
    InvoicesTab,
    EstimatesTab,
    PaymentsTab,
    ItemsTab,
  },

  created() {
    this.fetchSettings()
  },

  methods: {
    ...mapActions('company', ['fetchCompanySettings']),
    async fetchSettings() {
      this.isRequestOnGoing = true
      let res = await this.fetchCompanySettings([
        'payment_auto_generate',
        'payment_email_attachment',
        'payment_prefix',
        'payment_number_length',
        'payment_mail_body',
        'invoice_auto_generate',
        'invoice_email_attachment',
        'invoice_prefix',
        'invoice_number_length',
        'invoice_mail_body',
        'estimate_auto_generate',
        'estimate_email_attachment',
        'estimate_prefix',
        'estimate_number_length',
        'estimate_mail_body',
        'invoice_billing_address_format',
        'invoice_shipping_address_format',
        'invoice_company_address_format',
        'invoice_mail_body',
        'payment_mail_body',
        'payment_company_address_format',
        'payment_from_customer_address_format',
        'estimate_company_address_format',
        'estimate_billing_address_format',
        'estimate_shipping_address_format',
      ])

      this.settings = res.data
      this.isRequestOnGoing = false
    },
  },
}
</script>

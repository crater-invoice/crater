<template>
  <transition name="fade">
    <div class="address-tab">
      <form action="" class="px-4 py-2" @submit.prevent="updateAddressSetting">
        <div class="grid grid-cols-12 mt-6">
          <div class="col-span-12 mb-6">
            <label class="text-sm font-medium leading-5 text-dark non-italic">
              {{
                $t('settings.customization.addresses.customer_billing_address')
              }}
            </label>
            <base-custom-input
              v-model="addresses.billing_address_format"
              :types="billingAddressType"
              class="mt-2"
            />
          </div>
          <div class="col-span-12 mb-6">
            <label class="text-sm font-medium leading-5 text-dark non-italic">
              {{
                $t('settings.customization.addresses.customer_shipping_address')
              }}
            </label>
            <base-custom-input
              v-model="addresses.shipping_address_format"
              :types="shippingAddressType"
              class="mt-2"
            />
          </div>
          <div class="col-span-12 mb-6">
            <label class="text-sm font-medium leading-5 text-dark non-italic">
              {{ $t('settings.customization.addresses.company_address') }}
            </label>
            <base-custom-input
              v-model="addresses.company_address_format"
              :types="companyAddressType"
              class="mt-2"
            />
          </div>
        </div>
        <div class="grid grid-cols-12">
          <div class="col-span-12">
            <sw-button
              :disabled="isLoading"
              :loading="isLoading"
              variant="primary"
              type="submit"
            >
              <save-icon v-if="!isLoading" class="mr-2" />
              {{ $t('settings.customization.save') }}
            </sw-button>
          </div>
        </div>
      </form>
    </div>
  </transition>
</template>
<script>
import { mapActions } from 'vuex'
export default {
  data() {
    return {
      isLoading: false,
      addresses: {
        billing_address_format: '',
        shipping_address_format: '',
        company_address_format: '',
      },
      billingAddressType: [
        {
          label: 'Customer',
          fields: [
            { label: 'Display Name', value: 'CONTACT_DISPLAY_NAME' },
            { label: 'Contact Name', value: 'PRIMARY_CONTACT_NAME' },
            { label: 'Email', value: 'CONTACT_EMAIL' },
            { label: 'Phone', value: 'CONTACT_PHONE' },
            { label: 'Website', value: 'CONTACT_WEBSITE' },
          ],
        },
        {
          label: 'Billing Address',
          fields: [
            { label: 'Adddress name', value: 'BILLING_ADDRESS_NAME' },
            { label: 'Country', value: 'BILLING_COUNTRY' },
            { label: 'State', value: 'BILLING_STATE' },
            { label: 'City', value: 'BILLING_CITY' },
            { label: 'Address Street 1', value: 'BILLING_ADDRESS_STREET_1' },
            { label: 'Address Street 2', value: 'BILLING_ADDRESS_STREET_2' },
            { label: 'Phone', value: 'BILLING_PHONE' },
            { label: 'Zip Code', value: 'BILLING_ZIP_CODE' },
          ],
        },
      ],
      shippingAddressType: [
        {
          label: 'Customer',
          fields: [
            { label: 'Display Name', value: 'CONTACT_DISPLAY_NAME' },
            { label: 'Contact Name', value: 'PRIMARY_CONTACT_NAME' },
            { label: 'Email', value: 'CONTACT_EMAIL' },
            { label: 'Phone', value: 'CONTACT_PHONE' },
            { label: 'Website', value: 'CONTACT_WEBSITE' },
          ],
        },
        {
          label: 'Shipping Address',
          fields: [
            { label: 'Adddress name', value: 'SHIPPING_ADDRESS_NAME' },
            { label: 'Country', value: 'SHIPPING_COUNTRY' },
            { label: 'State', value: 'SHIPPING_STATE' },
            { label: 'City', value: 'SHIPPING_CITY' },
            { label: 'Address Street 1', value: 'SHIPPING_ADDRESS_STREET_1' },
            { label: 'Address Street 2', value: 'SHIPPING_ADDRESS_STREET_2' },
            { label: 'Phone', value: 'SHIPPING_PHONE' },
            { label: 'Zip Code', value: 'SHIPPING_ZIP_CODE' },
          ],
        },
      ],
      companyAddressType: [
        {
          label: 'Company Address',
          fields: [
            { label: 'Company Name', value: 'COMPANY_NAME' },
            { label: 'Address street 1', value: 'COMPANY_ADDRESS_STREET_1' },
            { label: 'Address Street 2', value: 'COMPANY_ADDRESS_STREET_2' },
            { label: 'Country', value: 'COMPANY_COUNTRY' },
            { label: 'State', value: 'COMPANY_STATE' },
            { label: 'City', value: 'COMPANY_CITY' },
            { label: 'Zip Code', value: 'COMPANY_ZIP_CODE' },
            { label: 'Phone', value: 'COMPANY_PHONE' },
          ],
        },
      ],
    }
  },

  methods: {
    ...mapActions('notification', ['showNotification']),
    async updateAddressSetting() {
      let data = { type: 'ADDRESSES', ...this.addresses, large: true }

      // if (this.updateSetting(data)) {
      this.showNotification({
        type: 'success',
        message: this.$t(
          'settings.customization.addresses.address_setting_updated'
        ),
      })
      // }
    },
  },
}
</script>

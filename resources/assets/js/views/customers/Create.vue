<template>
  <base-page class="customer-create">
    <form v-if="!initLoad" @submit.prevent="submitCustomerData">
      <sw-page-header :title="pageTitle" class="mb-5">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('customers.customer', 2)"
            to="/admin/customers"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'customers.edit'"
            :title="$t('customers.edit_customer')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('customers.new_customer')"
            to="#"
            active
          />
        </sw-breadcrumb>
        <template slot="actions">
          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
            size="lg"
            class="hidden md:relative md:flex"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />

            {{
              isEdit
                ? $t('customers.update_customer')
                : $t('customers.save_customer')
            }}
          </sw-button>
        </template>
      </sw-page-header>

      <sw-card variant="customer-card">
        <!-- Basic Info  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.basic_info') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group
              :label="$t('customers.display_name')"
              :error="displayNameError"
              class="md:col-span-3"
              required
            >
              <sw-input
                :invalid="$v.formData.name.$error"
                v-model="formData.name"
                focus
                type="text"
                name="name"
                tabindex="1"
                @input="$v.formData.name.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.primary_contact_name')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.contact_name"
                :label="$t('customers.contact_name')"
                type="text"
                tabindex="2"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.email')"
              :error="emailError"
              class="md:col-span-3"
            >
              <sw-input
                :invalid="$v.formData.email.$error"
                v-model.trim="formData.email"
                type="text"
                name="email"
                tabindex="3"
                @input="$v.formData.email.$touch()"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.phone')"
              class="md:col-span-3"
            >
              <sw-input
                v-model.trim="formData.phone"
                type="text"
                name="phone"
                tabindex="4"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.primary_currency')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="currency"
                :options="currencies"
                :custom-label="currencyNameWithCode"
                :allow-empty="false"
                :searchable="true"
                :show-labels="false"
                :tabindex="5"
                :placeholder="$t('customers.select_currency')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.website')"
              :error="urlError"
              class="md:col-span-3"
            >
              <sw-input
                v-model="formData.website"
                :invalid="$v.formData.website.$error"
                type="url"
                tabindex="6"
                @input="$v.formData.website.$touch()"
              />
            </sw-input-group>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Billing Address  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.billing_address') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group :label="$t('customers.name')" class="md:col-span-3">
              <sw-input
                v-model.trim="billing.name"
                type="text"
                name="address_name"
                tabindex="7"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.country')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="billing_country"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :allow-empty="true"
                :tabindex="8"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.state')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="billing.state"
                name="billing.state"
                type="text"
                tabindex="9"
              />
            </sw-input-group>

            <sw-input-group :label="$t('customers.city')" class="md:col-span-3">
              <sw-input
                v-model="billing.city"
                name="billing.city"
                type="text"
                tabindex="10"
              />
            </sw-input-group>

            <div class="md:col-span-3">
              <sw-input-group
                :label="$t('customers.address')"
                :error="billAddress1Error"
              >
                <sw-textarea
                  v-model.trim="billing.address_street_1"
                  :placeholder="$t('general.street_1')"
                  type="text"
                  name="billing_street1"
                  rows="3"
                  tabindex="11"
                  @input="$v.billing.address_street_1.$touch()"
                />
              </sw-input-group>

              <sw-input-group :error="billAddress2Error">
                <sw-textarea
                  v-model.trim="billing.address_street_2"
                  :placeholder="$t('general.street_2')"
                  type="text"
                  name="billing_street2"
                  rows="3"
                  tabindex="12"
                  @input="$v.billing.address_street_2.$touch()"
                />
              </sw-input-group>
            </div>

            <div class="md:col-span-3">
              <sw-input-group :label="$t('customers.phone')" class="mb-6">
                <sw-input
                  v-model.trim="billing.phone"
                  type="text"
                  name="phone"
                  tabindex="13"
                />
              </sw-input-group>

              <sw-input-group :label="$t('customers.zip_code')">
                <sw-input
                  v-model.trim="billing.zip"
                  tabindex="14"
                  type="text"
                  name="zip"
                />
              </sw-input-group>
            </div>
          </div>
        </div>

        <sw-divider class="mb-5 md:mb-8" />

        <!-- Billing Address Copy Button  -->
        <div
          class="flex items-center justify-start mb-6 md:justify-end md:mb-0"
        >
          <div class="p-1">
            <sw-button
              ref="sameAddress"
              variant="primary"
              type="button"
              class="h-8 px-3 py-1 mb-4"
              @click="copyAddress(true)"
            >
              <document-duplicate-icon class="h-4 mr-1 -ml-2" />
              <span class="text-xs">
                {{ $t('customers.copy_billing_address') }}
              </span>
            </sw-button>
          </div>
        </div>

        <!-- Shipping Address  -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('customers.shipping_address') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group :label="$t('customers.name')" class="md:col-span-3">
              <sw-input
                v-model.trim="shipping.name"
                type="text"
                name="address_name"
                tabindex="15"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.country')"
              class="md:col-span-3"
            >
              <sw-select
                v-model="shipping_country"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :tabindex="16"
                :allow-empty="true"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.state')"
              class="md:col-span-3"
            >
              <sw-input
                v-model="shipping.state"
                name="shipping.state"
                type="text"
                tabindex="17"
              />
            </sw-input-group>

            <sw-input-group :label="$t('customers.city')" class="md:col-span-3">
              <sw-input
                v-model="shipping.city"
                name="shipping.city"
                type="text"
                tabindex="18"
              />
            </sw-input-group>

            <sw-input-group
              :label="$t('customers.address')"
              class="md:col-span-3"
            >
              <sw-textarea
                v-model.trim="shipping.address_street_1"
                :tabindex="19"
                :placeholder="$t('general.street_1')"
                type="text"
                name="street_1"
                rows="3"
                @input="$v.shipping.address_street_1.$touch()"
              />

              <div v-if="$v.shipping.address_street_1.$error">
                <span
                  v-if="!$v.shipping.address_street_1.maxLength"
                  class="text-sm text-danger"
                  >{{ $t('validation.address_maxlength') }}</span
                >
              </div>

              <sw-textarea
                v-model.trim="shipping.address_street_2"
                :tabindex="20"
                :placeholder="$t('general.street_2')"
                type="text"
                name="street_2"
                rows="3"
                @input="$v.shipping.address_street_2.$touch()"
              />

              <div v-if="$v.shipping.address_street_2.$error">
                <span
                  v-if="!$v.shipping.address_street_2.maxLength"
                  class="text-danger"
                  >{{ $t('validation.address_maxlength') }}</span
                >
              </div>
            </sw-input-group>

            <div class="md:col-span-3">
              <sw-input-group :label="$t('customers.phone')" class="mb-6">
                <sw-input
                  v-model.trim="shipping.phone"
                  type="text"
                  name="phone"
                  tabindex="21"
                />
              </sw-input-group>

              <sw-input-group :label="$t('customers.zip_code')">
                <sw-input
                  v-model.trim="shipping.zip"
                  type="text"
                  name="zip"
                  tabindex="22"
                />
              </sw-input-group>
            </div>
          </div>
        </div>

        <sw-divider v-if="customFields.length > 0" class="mb-5 md:mb-8" />

        <!-- Custom Fields  -->
        <div v-if="customFields.length > 0" class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 sw-section-title lg:col-span-1">
            {{ $t('settings.custom_fields.title') }}
          </h6>

          <div
            class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
          >
            <sw-input-group
              v-for="(field, index) in customFields"
              :label="field.label"
              :required="field.is_required ? true : false"
              :key="index"
              class="md:col-span-3"
            >
              <component
                :type="field.type.label"
                :field="field"
                :is-edit="isEdit"
                :is="field.type + 'Field'"
                :invalid-fields="invalidFields"
                :tabindex="23 + index"
                @update="setCustomFieldValue"
              />
            </sw-input-group>
          </div>
        </div>

        <!-- Mobile Submit Button  -->
        <sw-button
          :disabled="isLoading"
          :loading="isLoading"
          variant="primary"
          type="submit"
          size="lg"
          class="flex w-full sm:hidden md:hidden"
        >
          <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
          {{
            isEdit
              ? $t('customers.update_customer')
              : $t('customers.save_customer')
          }}
        </sw-button>
      </sw-card>
    </form>
    <base-loader v-else />
  </base-page>
</template>

<script>
import AddressStub from '../../stub/address'
import { mapActions, mapGetters } from 'vuex'
import _ from 'lodash'
import CustomFieldsMixin from '../../mixins/customFields'
import { DocumentDuplicateIcon } from '@vue-hero-icons/solid'

const {
  required,
  minLength,
  email,
  url,
  maxLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentDuplicateIcon,
  },
  mixins: [CustomFieldsMixin],
  data() {
    return {
      isCopyFromBilling: false,
      isLoading: false,
      initLoad: false,
      formData: {
        name: null,
        contact_name: null,
        email: null,
        phone: null,
        currency_id: null,
        website: null,
        addresses: [],
      },
      currency: null,
      billing: {
        name: null,
        country_id: null,
        state: null,
        city: null,
        phone: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'billing',
      },
      shipping: {
        name: null,
        country_id: null,
        state: null,
        city: null,
        phone: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'shipping',
      },
      currencyList: [],

      billing_country: null,
      shipping_country: null,

      countries: [],
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        email,
      },
      website: {
        url,
      },
    },
    billing: {
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
    shipping: {
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
  },
  computed: {
    ...mapGetters(['currencies']),
    ...mapGetters('company', ['defaultCurrency']),
    isEdit() {
      if (this.$route.name === 'customers.edit') {
        return true
      }
      return false
    },
    pageTitle() {
      if (this.$route.name === 'customers.edit') {
        return this.$t('customers.edit_customer')
      }
      return this.$t('customers.new_customer')
    },
    hasBillingAdd() {
      let billing = this.billing
      if (
        billing.name ||
        billing.country_id ||
        billing.state ||
        billing.city ||
        billing.phone ||
        billing.zip ||
        billing.address_street_1 ||
        billing.address_street_2
      ) {
        return true
      }
      return false
    },
    hasShippingAdd() {
      let shipping = this.shipping
      if (
        shipping.name ||
        shipping.country_id ||
        shipping.state ||
        shipping.city ||
        shipping.phone ||
        shipping.zip ||
        shipping.address_street_1 ||
        shipping.address_street_2
      ) {
        return true
      }
      return false
    },
    displayNameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    urlError() {
      if (!this.$v.formData.website.$error) {
        return ''
      }

      if (!this.$v.formData.website.url) {
        return this.$tc('validation.invalid_url')
      }
    },
    billAddress1Error() {
      if (!this.$v.billing.address_street_1.$error) {
        return ''
      }

      if (!this.$v.billing.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    billAddress2Error() {
      if (!this.$v.billing.address_street_2.$error) {
        return ''
      }

      if (!this.$v.billing.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress1Error() {
      if (!this.$v.shipping.address_street_1.$error) {
        return ''
      }

      if (!this.$v.shipping.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress2Error() {
      if (!this.$v.shipping.address_street_2.$error) {
        return ''
      }

      if (!this.$v.shipping.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
  },
  watch: {
    billing_country(newCountry) {
      if (newCountry) {
        this.billing.country_id = newCountry.id
        this.isDisabledBillingState = false
      } else {
        this.billing.country_id = null
      }
    },
    shipping_country(newCountry) {
      if (newCountry) {
        this.shipping.country_id = newCountry.id
        return true
      } else {
        this.shipping.country_id = null
      }
    },
  },
  created() {
    this.fetchInitData()
  },
  mounted() {
    if (this.isEdit) {
      this.loadCustomer()
      return true
    }
    this.currency = this.defaultCurrency
    this.setInitialCustomFields('Customer')
  },
  methods: {
    ...mapActions('customer', [
      'addCustomer',
      'fetchCustomer',
      'updateCustomer',
      'fetchViewCustomer',
    ]),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('customFields', ['fetchCustomFields']),

    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },

    async loadCustomer() {
      let params = {
        id: this.$route.params.id,
      }
      let response = await this.fetchCustomer(params)

      this.formData = { ...this.formData, ...response.data.customer }

      if (response.data.customer.billing_address) {
        this.billing = response.data.customer.billing_address

        if (response.data.customer.billing_address.country_id) {
          this.billing_country = response.data.customer.billing_address.country
        }
      }

      if (response.data.customer.shipping_address) {
        this.shipping = response.data.customer.shipping_address

        if (response.data.customer.shipping_address.country_id) {
          this.shipping_country =
            response.data.customer.shipping_address.country
        }
      }

      this.formData.currency_id = response.data.customer.currency_id
      this.currency = response.data.customer.currency

      let res = await this.fetchCustomFields({ type: 'Customer', limit: 'all' })
      let customFields = res.data.customFields.data
      this.formData.fields = response.data.customer.fields

      this.setEditCustomFields(response.data.customer.fields, customFields)
    },
    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },
    copyAddress(val) {
      if (val === true) {
        this.isCopyFromBilling = true
        this.shipping = { ...this.billing, type: 'shipping' }
        this.shipping_country = this.billing_country
        this.shipping_state = this.billing_state
        this.shipping_city = this.billing_city
      } else {
        this.shipping = { ...AddressStub, type: 'shipping' }
        this.shipping_country = null
        this.shipping_state = null
        this.shipping_city = null
      }
    },
    async submitCustomerData() {
      this.$v.formData.$touch()
      let validate = await this.touchCustomField()

      if (this.$v.$invalid || validate.error) {
        return true
      }

      if (this.hasBillingAdd && this.hasShippingAdd) {
        this.formData.addresses = [{ ...this.billing }, { ...this.shipping }]
      } else if (this.hasBillingAdd) {
        this.formData.addresses = [{ ...this.billing }]
      } else if (this.hasShippingAdd) {
        this.formData.addresses = [{ ...this.shipping }]
      }

      try {
        let response = null
        this.isLoading = true
        if (this.currency) {
          this.formData.currency_id = this.currency.id
        }

        if (this.isEdit) {
          response = await this.updateCustomer(this.formData)
          if (response.data.success) {
            this.$router.push(
              `/admin/customers/${response.data.customer.id}/view`
            )
            this.showNotification({
              type: 'success',
              message: this.$t('customers.updated_message'),
            })
          }
          if (response.data.error) {
            this.showNotification({
              type: 'error',
              message: this.$t('validation.email_already_taken'),
            })
          }
        } else {
          response = await this.addCustomer(this.formData)
          if (response.data.success) {
            this.$router.push(
              `/admin/customers/${response.data.customer.id}/view`
            )
            this.showNotification({
              type: 'success',
              message: this.$t('customers.created_message'),
            })
          }
        }

        this.isLoading = false
        return true
      } catch (error) {
        this.isLoading = false
        if (err.response.data.errors.email) {
          this.showNotification({
            type: 'error',
            message: this.$t('validation.email_already_taken'),
          })
        }
      }
    },
  },
}
</script>

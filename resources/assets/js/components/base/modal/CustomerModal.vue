<template>
  <div class="customer-modal">
    <form action="" @submit.prevent="submitCustomerData">
      <div class="card-body">
        <!-- tab-1 -->
        <tabs :options="{defaultTabHash: 'basic-home' }" class="tabs-simple">
          <tab id="basic-home" :name="$t('customers.basic_info')">
            <div class="basic-info">
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.display_name') }} <span class="required">*</span></label>
                <div class="col-sm-7">
                  <base-input
                    ref="name"
                    :invalid="$v.formData.name.$error"
                    v-model.trim="formData.name"
                    type="text"
                    name="name"
                    @input="$v.formData.name.$touch()"
                  />
                  <div v-if="$v.formData.name.$error">
                    <span v-if="!$v.formData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
                    <span v-if="!$v.formData.name.minLength" class="text-danger"> {{ $tc('validation.name_min_length', $v.formData.name.$params.minLength.min, { count: $v.formData.name.$params.minLength.min }) }} </span>
                    <span v-if="!$v.formData.name.alpha" class="text-danger">{{ $tc('validation.characters_only') }}</span>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.primary_display_name') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="formData.contact_name"
                    type="text"
                  />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('login.email') }}</label>
                <div class="col-sm-7">
                  <base-input
                    :invalid="$v.formData.email.$error"
                    v-model.trim="formData.email"
                    type="text"
                    name="email"
                    @input="$v.formData.email.$touch()"
                  />
                  <div v-if="$v.formData.email.$error">
                    <span v-if="!$v.formData.email.email" class="text-danger"> {{ $t('validation.email_incorrect') }} </span>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $tc('settings.currencies.currency') }}</label>
                <div class="col-sm-7">
                  <base-select
                    v-model="currency"
                    :options="currencies"
                    :searchable="true"
                    :allow-empty="false"
                    :show-labels="false"
                    :placeholder="$t('customers.select_currency')"
                    label="name"
                    track-by="id"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.phone') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model.trim="formData.phone"
                    type="text"
                    name="phone"
                  />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.website') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="formData.website"
                    :invalid="$v.formData.website.$error"
                    type="url"
                    @input="$v.formData.website.$touch()"
                  />
                  <div v-if="$v.formData.website.$error">
                    <span v-if="!$v.formData.website.url" class="text-danger">{{ $tc('validation.invalid_url') }}</span>
                  </div>
                </div>
              </div>
            </div>

          </tab>

          <!-- tab-2 -->
          <tab id="basic-profile" :name="$t('customers.billing_address')">
            <div class="basic-info">
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.name') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="billing.name"
                    type="text"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.phone') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model.trim="billing.phone"
                    type="text"
                    name="phone"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.address') }}</label>
                <div class="col-sm-7">
                  <base-text-area
                    v-model="billing.address_street_1"
                    :placeholder="$t('general.street_1')"
                    rows="2"
                    cols="50"
                    class="mb-1"
                    @input="$v.billing.address_street_1.$touch()"
                  />
                  <div v-if="$v.billing.address_street_1.$error">
                    <span v-if="!$v.billing.address_street_1.maxLength" class="text-danger">{{ $t('validation.address_maxlength') }}</span>
                  </div>

                  <base-text-area
                    v-model="billing.address_street_2"
                    :placeholder="$t('general.street_2')"
                    rows="2"
                    cols="50"
                    @input="$v.billing.address_street_2.$touch()"
                  />

                  <div v-if="$v.billing.address_street_2.$error">
                    <span v-if="!$v.billing.address_street_2.maxLength" class="text-danger">{{ $t('validation.address_maxlength') }}</span>
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.country') }}</label>
                <div class="col-sm-7">
                  <base-select
                    v-model="billingCountry"
                    :options="countryList"
                    :searchable="true"
                    :show-labels="false"
                    :placeholder="$t('general.select_country')"
                    :allow-empty="false"
                    track-by="id"
                    label="name"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.state') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="billing.state"
                    type="text"
                    name="billingState"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.city') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="billing.city"
                    type="text"
                    name="billingCity"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.zip_code') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="billing.zip"
                    type="text"
                  />
                </div>
              </div>

            </div>
          </tab>

          <!-- tab-3 -->
          <tab id="basic-message" :name="$t('customers.shipping_address')">
            <div class="basic-info">
              <div class="form-group row ">
                <div class="col-sm-12 copy-address-button">
                  <base-button ref="sameAddress" icon="copy" class="mr-2 btn-sm" color="theme" @click="copyAddress(true)">
                    {{ $t('customers.copy_billing_address') }}
                  </base-button>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.name') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="shipping.name"
                    type="text"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.phone') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model.trim="shipping.phone"
                    type="text"
                    name="phone"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.address') }}</label>
                <div class="col-sm-7">
                  <base-text-area
                    v-model="shipping.address_street_1"
                    :placeholder="$t('general.street_1')"
                    rows="2"
                    cols="50"
                    class="mb-1"
                    @input="$v.shipping.address_street_1.$touch()"
                  />
                  <div v-if="$v.shipping.address_street_1.$error">
                    <span v-if="!$v.shipping.address_street_1.maxLength" class="text-danger">{{ $t('validation.address_maxlength') }}</span>
                  </div>

                  <base-text-area
                    v-model="shipping.address_street_2"
                    :placeholder="$t('general.street_2')"
                    rows="2"
                    cols="50"
                    @input="$v.shipping.address_street_2.$touch()"
                  />
                  <div v-if="$v.shipping.address_street_2.$error">
                    <span v-if="!$v.shipping.address_street_2.maxLength" class="text-danger">{{ $t('validation.address_maxlength') }}</span>
                  </div>

                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.country') }}</label>
                <div class="col-sm-7">
                  <base-select
                    v-model="shippingCountry"
                    :options="countryList"
                    :searchable="true"
                    :show-labels="false"
                    :allow-empty="false"
                    :placeholder="$t('general.select_country')"
                    track-by="id"
                    label="name"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.state') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="shipping.state"
                    type="text"
                    name="shippingState"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.city') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="shipping.city"
                    type="text"
                    name="shippingCity"
                  />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label input-label">{{ $t('customers.zip_code') }}</label>
                <div class="col-sm-7">
                  <base-input
                    v-model="shipping.zip"
                    type="text"
                  />
                </div>
              </div>
            </div>
          </tab>
        </tabs>
      </div>
      <div class="card-footer">
        <base-button :outline="true" class="mr-3" color="theme" @click="cancelCustomer">
          {{ $t('general.cancel') }}
        </base-button>
        <base-button
          :loading="isLoading"
          icon="save"
          color="theme"
          type="submit"
        >
          {{ $t('general.save') }}
        </base-button>
      </div>
    </form>
  </div>
</template>

<script>
import { Tabs, Tab } from 'vue-tabs-component'
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
import { mapActions, mapGetters } from 'vuex'
import AddressStub from '../../../stub/address'
const { required, minLength, email, numeric, url, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    'tabs': Tabs,
    'tab': Tab,
    MultiSelect
  },
  mixins: [validationMixin],
  data () {
    return {
      isEdit: false,
      isLoading: false,
      countryList: [],
      billingCountry: null,
      shippingCountry: null,
      isCopyFromBilling: false,
      currencyList: [],
      currency: '',
      isDisabledBillingState: true,
      isDisabledBillingCity: true,
      isDisabledShippingState: true,
      isDisabledShippingCity: true,
      formData: {
        id: null,
        name: null,
        currency_id: null,
        phone: null,
        website: null,
        contact_name: null,
        addresses: []
      },
      billing: {...AddressStub},
      shipping: {...AddressStub}
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3)
      },
      email: {
        email
      },
      website: {
        url
      }
    },
    billing: {
      address_street_1: {
        maxLength: maxLength(255)
      },
      address_street_2: {
        maxLength: maxLength(255)
      }
    },
    shipping: {
      address_street_1: {
        maxLength: maxLength(255)
      },
      address_street_2: {
        maxLength: maxLength(255)
      }
    }
  },
  computed: {
    ...mapGetters('currency', [
      'defaultCurrency',
      'currencies'
    ]),
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive'
    ])
  },
  watch: {
    'modalDataID' (val) {
      if (val) {
        this.isEdit = true
        this.setData()
      } else {
        this.isEdit = false
      }
    },
    billingCountry () {
      if (this.billingCountry) {
        this.billing.country_id = this.billingCountry.id
        return true
      }
    },
    shippingCountry () {
      if (this.shippingCountry) {
        this.shipping.country_id = this.shippingCountry.id
        return true
      }
    }
  },
  mounted () {
    this.$refs.name.focus = true
    this.currency = this.defaultCurrency
    this.fetchCountry()
    if (this.modalDataID) {
      this.setData()
    }
  },
  methods: {
    ...mapActions('invoice', {
      setInvoiceCustomer: 'selectCustomer'
    }),
    ...mapActions('estimate', {
      setEstimateCustomer: 'selectCustomer'
    }),
    ...mapActions('customer', [
      'fetchCustomer',
      'addCustomer',
      'updateCustomer'
    ]),
    ...mapActions('modal', [
      'closeModal'
    ]),
    resetData () {
      this.formData = {
        name: null,
        currency_id: null,
        phone: null,
        website: null,
        contact_name: null,
        addresses: []
      }

      this.billingCountry = null
      this.shippingCountry = null

      this.billing = {...AddressStub}
      this.shipping = {...AddressStub}
      this.$v.formData.$reset()
    },
    cancelCustomer () {
      this.resetData()
      this.closeModal()
    },
    copyAddress (val) {
      if (val === true) {
        this.isCopyFromBilling = true
        this.shipping = {...this.billing, type: 'shipping'}
        this.shippingCountry = this.billingCountry
      } else {
        this.shipping = {...AddressStub, type: 'shipping'}
        this.shippingCountry = null
      }
    },
    async loadData () {
      let response = await this.fetchCustomer()
      this.currencyList = this.currencies
      this.formData.currency_id = response.data.currency.id
      return true
    },
    checkAddress () {
      const isBillingEmpty = Object.values(this.billing).every(val => (val === null || val === ''))
      const isShippingEmpty = Object.values(this.shipping).every(val => (val === null || val === ''))
      if (isBillingEmpty === true && isBillingEmpty === true) {
        this.formData.addresses = []
        return true
      }

      if (isBillingEmpty === false && isShippingEmpty === false) {
        this.formData.addresses = [{...this.billing, type: 'billing'}, {...this.shipping, type: 'shipping'}]
        return true
      }

      if (isBillingEmpty === false) {
        this.formData.addresses.push({...this.billing, type: 'billing'})
        return true
      }

      this.formData.addresses = [{...this.shipping, type: 'shipping'}]
      return true
    },
    async setData () {
      this.formData.id = this.modalData.id
      this.formData.name = this.modalData.name
      this.formData.email = this.modalData.email
      this.formData.contact_name = this.modalData.contact_name
      this.formData.phone = this.modalData.phone
      this.formData.website = this.modalData.website
      this.currency = this.modalData.currency

      if (this.modalData.billing_address) {
        this.billing = this.modalData.billing_address
        this.billingCountry = this.modalData.billing_address.country
      }
      if (this.modalData.shipping_address) {
        this.shipping = this.modalData.shipping_address
        this.shippingCountry = this.modalData.shipping_address.country
      }
    },
    async submitCustomerData () {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      // this.checkAddress()
      this.formData.addresses = [{...this.shipping, type: 'shipping'}, {...this.billing, type: 'billing'}]
      this.isLoading = true

      if (this.currency) {
        this.formData.currency_id = this.currency.id
      } else {
        this.formData.currency_id = this.defaultCurrency.id
      }
      try {
        let response = null
        if (this.modalDataID) {
          response = await this.updateCustomer(this.formData)
        } else {
          response = await this.addCustomer(this.formData)
        }
        if (response.data) {
          if (this.modalDataID) {
            window.toastr['success'](this.$tc('customers.updated_message'))
          } else {
            window.toastr['success'](this.$tc('customers.created_message'))
          }
          this.isLoading = false
          if (this.$route.name === 'invoices.create' || this.$route.name === 'invoices.edit') {
            this.setInvoiceCustomer(response.data.customer.id)
          }
          if (this.$route.name === 'estimates.create' || this.$route.name === 'estimates.edit') {
            this.setEstimateCustomer(response.data.customer.id)
          }
          this.resetData()
          this.closeModal()
          return true
        }
      // window.toastr['error'](response.data.error)
      } catch (err) {
        if (err.response.data.errors.email) {
          this.isLoading = false
          window.toastr['error'](this.$t('validation.email_already_taken'))
        }
      }
    },
    async fetchCountry () {
      let res = await window.axios.get('/api/countries')
      if (res) {
        this.countryList = res.data.countries
      }
    }
  }
}
</script>

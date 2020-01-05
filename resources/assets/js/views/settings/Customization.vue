<template>
  <div class="setting-main-container customization">
    <div class="card setting-card">
      <ul class="tabs">
        <li class="tab" @click="setActiveTab('INVOICES')">
          <a :class="['tab-link', {'a-active': activeTab === 'INVOICES'}]" href="#">{{ $t('settings.customization.invoices.title') }}</a>
        </li>
        <li class="tab" @click="setActiveTab('ESTIMATES')">
          <a :class="['tab-link', {'a-active': activeTab === 'ESTIMATES'}]" href="#">{{ $t('settings.customization.estimates.title') }}</a>
        </li>
        <li class="tab" @click="setActiveTab('PAYMENTS')">
          <a :class="['tab-link', {'a-active': activeTab === 'PAYMENTS'}]" href="#">{{ $t('settings.customization.payments.title') }}</a>
        </li>
        <li class="tab" @click="setActiveTab('ITEMS')">
          <a :class="['tab-link', {'a-active': activeTab === 'ITEMS'}]" href="#">{{ $t('settings.customization.items.title') }}</a>
        </li>
      </ul>

      <!-- Invoices Tab -->
      <transition name="fade-customize">
        <div v-if="activeTab === 'INVOICES'" class="invoice-tab">
          <form action="" class="mt-3" @submit.prevent="updateInvoiceSetting">
            <div class="row">
              <div class="col-md-12 mb-4">
                <label class="input-label">{{ $t('settings.customization.invoices.invoice_prefix') }}</label>
                <base-input
                  v-model="invoices.invoice_prefix"
                  :invalid="$v.invoices.invoice_prefix.$error"
                  class="prefix-input"
                  @input="$v.invoices.invoice_prefix.$touch()"
                  @keyup="changeToUppercase('INVOICES')"
                />
                <span v-show="!$v.invoices.invoice_prefix.required" class="text-danger mt-1">{{ $t('validation.required') }}</span>
                <span v-if="!$v.invoices.invoice_prefix.maxLength" class="text-danger">{{ $t('validation.prefix_maxlength') }}</span>
                <span v-if="!$v.invoices.invoice_prefix.alpha" class="text-danger">{{ $t('validation.characters_only') }}</span>
              </div>
            </div>
            <div class="row pb-3">
              <div class="col-md-12">
                <base-button
                  icon="save"
                  color="theme"
                  type="submit"
                >
                  {{ $t('settings.customization.save') }}
                </base-button>
              </div>
            </div>
          </form>
          <hr>
          <div class="page-header pt-3">
            <h3 class="page-title">
              {{ $t('settings.customization.invoices.invoice_settings') }}
            </h3>
            <div class="flex-box">
              <div class="left">
                <base-switch
                  v-model="invoiceAutogenerate"
                  class="btn-switch"
                  @change="setInvoiceSetting"
                />
              </div>
              <div class="right ml-15">
                <p class="box-title">  {{ $t('settings.customization.invoices.autogenerate_invoice_number') }} </p>
                <p class="box-desc">  {{ $t('settings.customization.invoices.invoice_setting_description') }} </p>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Estimates Tab -->
      <transition name="fade-customize">
        <div v-if="activeTab === 'ESTIMATES'" class="estimate-tab">
          <form action="" class="mt-3" @submit.prevent="updateEstimateSetting">
            <div class="row">
              <div class="col-md-12 mb-4">
                <label class="input-label">{{ $t('settings.customization.estimates.estimate_prefix') }}</label>
                <base-input
                  v-model="estimates.estimate_prefix"
                  :invalid="$v.estimates.estimate_prefix.$error"
                  class="prefix-input"
                  @input="$v.estimates.estimate_prefix.$touch()"
                  @keyup="changeToUppercase('ESTIMATES')"
                />
                <span v-show="!$v.estimates.estimate_prefix.required" class="text-danger mt-1">{{ $t('validation.required') }}</span>
                <span v-if="!$v.estimates.estimate_prefix.maxLength" class="text-danger">{{ $t('validation.prefix_maxlength') }}</span>
                <span v-if="!$v.estimates.estimate_prefix.alpha" class="text-danger">{{ $t('validation.characters_only') }}</span>
              </div>
            </div>
            <div class="row pb-3">
              <div class="col-md-12">
                <base-button
                  icon="save"
                  color="theme"
                  type="submit"
                >
                  {{ $t('settings.customization.save') }}
                </base-button>
              </div>
            </div>
            <hr>
          </form>
          <div class="page-header pt-3">
            <h3 class="page-title">
              {{ $t('settings.customization.estimates.estimate_settings') }}
            </h3>
            <div class="flex-box">
              <div class="left">
                <base-switch
                  v-model="estimateAutogenerate"
                  class="btn-switch"
                  @change="setEstimateSetting"
                />
              </div>
              <div class="right ml-15">
                <p class="box-title">  {{ $t('settings.customization.estimates.autogenerate_estimate_number') }} </p>
                <p class="box-desc">  {{ $t('settings.customization.estimates.estimate_setting_description') }} </p>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Payments Tab -->
      <transition name="fade-customize">
        <div v-if="activeTab === 'PAYMENTS'" class="payment-tab">
          <div class="page-header">
            <div class="row">
              <div class="col-md-8">
                <!-- <h3 class="page-title">
                  {{ $t('settings.customization.payments.payment_mode') }}
                </h3> -->
              </div>
              <div class="col-md-4 d-flex flex-row-reverse">
                <base-button
                  outline
                  class="add-new-tax"
                  color="theme"
                  @click="addPaymentMode"
                >
                  {{ $t('settings.customization.payments.add_payment_mode') }}
                </base-button>
              </div>
            </div>
          </div>
          <table-component
            ref="table"
            :show-filter="false"
            :data="paymentModes"
            table-class="table tax-table"
            class="mb-3"
          >
            <table-column
              :sortable="true"
              :label="$t('settings.customization.payments.payment_mode')"
              show="name"
            />
            <table-column
              :sortable="false"
              :filterable="false"
              cell-class="action-dropdown"
            >
              <template slot-scope="row">
                <span>{{ $t('settings.tax_types.action') }}</span>
                <v-dropdown>
                  <a slot="activator" href="#">
                    <dot-icon />
                  </a>
                  <v-dropdown-item>
                    <div class="dropdown-item" @click="editPaymentMode(row)">
                      <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                      {{ $t('general.edit') }}
                    </div>
                  </v-dropdown-item>
                  <v-dropdown-item>
                    <div class="dropdown-item" @click="removePaymentMode(row.id)">
                      <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                      {{ $t('general.delete') }}
                    </div>
                  </v-dropdown-item>
                </v-dropdown>
              </template>
            </table-column>
          </table-component>
          <hr>
          <form action="" class="pt-3" @submit.prevent="updatePaymentSetting">
            <div class="row">
              <div class="col-md-12 mb-4">
                <label class="input-label">{{ $t('settings.customization.payments.payment_prefix') }}</label>
                <base-input
                  v-model="payments.payment_prefix"
                  :invalid="$v.payments.payment_prefix.$error"
                  class="prefix-input"
                  @input="$v.payments.payment_prefix.$touch()"
                  @keyup="changeToUppercase('PAYMENTS')"
                />
                <span v-show="!$v.payments.payment_prefix.required" class="text-danger mt-1">{{ $t('validation.required') }}</span>
                <span v-if="!$v.payments.payment_prefix.maxLength" class="text-danger">{{ $t('validation.prefix_maxlength') }}</span>
                <span v-if="!$v.payments.payment_prefix.alpha" class="text-danger">{{ $t('validation.characters_only') }}</span>
              </div>
            </div>
            <div class="row pb-3">
              <div class="col-md-12">
                <base-button
                  icon="save"
                  color="theme"
                  type="submit"
                >
                  {{ $t('settings.customization.save') }}
                </base-button>
              </div>
            </div>
          </form>
          <hr>
          <div class="page-header pt-3">
            <h3 class="page-title">
              {{ $t('settings.customization.payments.payment_settings') }}
            </h3>
            <div class="flex-box">
              <div class="left">
                <base-switch
                  v-model="paymentAutogenerate"
                  class="btn-switch"
                  @change="setPaymentSetting"
                />
              </div>
              <div class="right ml-15">
                <p class="box-title">  {{ $t('settings.customization.payments.autogenerate_payment_number') }} </p>
                <p class="box-desc">  {{ $t('settings.customization.payments.payment_setting_description') }} </p>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Items Tab -->
      <transition name="fade-customize">
        <div v-if="activeTab === 'ITEMS'" class="item-tab">
          <div class="page-header">
            <div class="row">
              <div class="col-md-8">
                <!-- <h3 class="page-title">
                  {{ $t('settings.customization.items.title') }}
                </h3> -->
              </div>
              <div class="col-md-4 d-flex flex-row-reverse">
                <base-button
                  outline
                  class="add-new-tax"
                  color="theme"
                  @click="addItemUnit"
                >
                  {{ $t('settings.customization.items.add_item_unit') }}
                </base-button>
              </div>
            </div>
          </div>
          <table-component
            ref="itemTable"
            :show-filter="false"
            :data="itemUnits"
            table-class="table tax-table"
            class="mb-3"
          >
            <table-column
              :sortable="true"
              :label="$t('settings.customization.items.units')"
              show="name"
            />
            <table-column
              :sortable="false"
              :filterable="false"
              cell-class="action-dropdown"
            >
              <template slot-scope="row">
                <span>{{ $t('settings.tax_types.action') }}</span>
                <v-dropdown>
                  <a slot="activator" href="#">
                    <dot-icon />
                  </a>
                  <v-dropdown-item>
                    <div class="dropdown-item" @click="editItemUnit(row)">
                      <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                      {{ $t('general.edit') }}
                    </div>
                  </v-dropdown-item>
                  <v-dropdown-item>
                    <div class="dropdown-item" @click="removeItemUnit(row.id)">
                      <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                      {{ $t('general.delete') }}
                    </div>
                  </v-dropdown-item>
                </v-dropdown>
              </template>
            </table-column>
          </table-component>
        </div>
      </transition>

    </div>
  </div>
</template>
<script>
import { validationMixin } from 'vuelidate'
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
export default {
  mixins: [validationMixin],
  data () {
    return {
      activeTab: 'INVOICES',
      invoiceAutogenerate: false,
      estimateAutogenerate: false,
      paymentAutogenerate: false,
      invoices: {
        invoice_prefix: null,
        invoice_notes: null,
        invoice_terms_and_conditions: null
      },
      estimates: {
        estimate_prefix: null,
        estimate_notes: null,
        estimate_terms_and_conditions: null
      },
      payments: {
        payment_prefix: null
      },
      items: {
        units: []
      },
      currentData: null
    }
  },
  computed: {
    ...mapGetters('item', [
      'itemUnits'
    ]),
    ...mapGetters('payment', [
      'paymentModes'
    ])
  },
  watch: {
    activeTab () {
      this.loadData()
    }
  },
  validations: {
    invoices: {
      invoice_prefix: {
        required,
        maxLength: maxLength(5),
        alpha
      }
    },
    estimates: {
      estimate_prefix: {
        required,
        maxLength: maxLength(5),
        alpha
      }
    },
    payments: {
      payment_prefix: {
        required,
        maxLength: maxLength(5),
        alpha
      }
    }
  },
  created () {
    this.loadData()
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('payment', [
      'deletePaymentMode'
    ]),
    ...mapActions('item', [
      'deleteItemUnit'
    ]),
    async setInvoiceSetting () {
      let data = {
        key: 'invoice_auto_generate',
        value: this.invoiceAutogenerate ? 'YES' : 'NO'
      }
      let response = await window.axios.put('/api/settings/update-setting', data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    async setEstimateSetting () {
      let data = {
        key: 'estimate_auto_generate',
        value: this.estimateAutogenerate ? 'YES' : 'NO'
      }
      let response = await window.axios.put('/api/settings/update-setting', data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    async addItemUnit () {
      this.openModal({
        'title': 'Add Item Unit',
        'componentName': 'ItemUnit'
      })
      this.$refs.itemTable.refresh()
    },
    async editItemUnit (data) {
      this.openModal({
        'title': 'Edit Item Unit',
        'componentName': 'ItemUnit',
        'id': data.id,
        'data': data
      })
      this.$refs.itemTable.refresh()
    },
    async removeItemUnit (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.items.item_unit_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteItemUnit(id)
          if (response.data.success) {
            window.toastr['success'](this.$t('settings.customization.items.deleted_message'))
            this.id = null
            this.$refs.itemTable.refresh()
            return true
          }
          window.toastr['error'](this.$t('settings.customization.items.already_in_use'))
        }
      })
    },
    async addPaymentMode () {
      this.openModal({
        'title': 'Add Payment Mode',
        'componentName': 'PaymentMode'
      })
      this.$refs.table.refresh()
    },
    async editPaymentMode (data) {
      this.openModal({
        'title': 'Edit Payment Mode',
        'componentName': 'PaymentMode',
        'id': data.id,
        'data': data
      })
      this.$refs.table.refresh()
    },
    removePaymentMode (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.payments.payment_mode_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let response = await this.deletePaymentMode(id)
          if (response.data.success) {
            window.toastr['success'](this.$t('settings.customization.payments.deleted_message'))
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](this.$t('settings.customization.payments.already_in_use'))
        }
      })
    },
    changeToUppercase (currentTab) {
      if (currentTab === 'INVOICES') {
        this.invoices.invoice_prefix = this.invoices.invoice_prefix.toUpperCase()
        return true
      }

      if (currentTab === 'ESTIMATES') {
        this.estimates.estimate_prefix = this.estimates.estimate_prefix.toUpperCase()
        return true
      }

      if (currentTab === 'PAYMENTS') {
        this.payments.payment_prefix = this.payments.payment_prefix.toUpperCase()
        return true
      }
    },
    async setPaymentSetting () {
      let data = {
        key: 'payment_auto_generate',
        value: this.paymentAutogenerate ? 'YES' : 'NO'
      }
      let response = await window.axios.put('/api/settings/update-setting', data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },
    async loadData () {
      let res = await window.axios.get('/api/settings/get-customize-setting')

      if (res.data) {
        this.invoices.invoice_prefix = res.data.invoice_prefix
        this.invoices.invoice_notes = res.data.invoice_notes
        this.invoices.invoice_terms_and_conditions = res.data.invoice_terms_and_conditions
        this.estimates.estimate_prefix = res.data.estimate_prefix
        this.estimates.estimate_notes = res.data.estimate_notes
        this.estimates.estimate_terms_and_conditions = res.data.estimate_terms_and_conditions
        this.payments.payment_prefix = res.data.payment_prefix

        if (res.data.invoice_auto_generate === 'YES') {
          this.invoiceAutogenerate = true
        } else {
          this.invoiceAutogenerate = false
        }

        if (res.data.estimate_auto_generate === 'YES') {
          this.estimateAutogenerate = true
        } else {
          this.estimateAutogenerate = false
        }

        if (res.data.payment_auto_generate === 'YES') {
          this.paymentAutogenerate = true
        } else {
          this.paymentAutogenerate = false
        }
      }
    },
    async updateInvoiceSetting () {
      this.$v.invoices.$touch()

      if (this.$v.invoices.$invalid) {
        return false
      }

      let data = {type: 'INVOICES', ...this.invoices}

      if (this.updateSetting(data)) {
        window.toastr['success'](this.$t('settings.customization.invoices.invoice_setting_updated'))
      }
    },
    async updateEstimateSetting () {
      this.$v.estimates.$touch()

      if (this.$v.estimates.$invalid) {
        return false
      }

      let data = {type: 'ESTIMATES', ...this.estimates}

      if (this.updateSetting(data)) {
        window.toastr['success'](this.$t('settings.customization.estimates.estimate_setting_updated'))
      }
    },
    async updatePaymentSetting () {
      this.$v.payments.$touch()

      if (this.$v.payments.$invalid) {
        return false
      }

      let data = {type: 'PAYMENTS', ...this.payments}

      if (this.updateSetting(data)) {
        window.toastr['success'](this.$t('settings.customization.payments.payment_setting_updated'))
      }
    },
    async updateSetting (data) {
      let res = await window.axios.put('/api/settings/update-customize-setting', data)

      if (res.data.success) {
        return true
      }

      return false
    },
    setActiveTab (val) {
      this.activeTab = val
    }
  }
}
</script>
<style>
  .fade-customize-enter-active {
    transition: opacity 0.9s;
  }

  .fade-customize-leave-active  {
    transition: opacity 0s;
  }

  .fade-customize-enter, .fade-customize-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }
</style>

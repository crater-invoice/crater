<template>
  <div class="invoice-create-page main-content">
    <form action="" @submit.prevent="submitInvoiceData">
      <div class="page-header">
        <h3 class="page-title">{{ $t('invoices.new_invoice') }}</h3>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/invoices">{{ $tc('invoices.invoice', 2) }}</router-link></li>
          <li class="breadcrumb-item">{{ $t('invoices.new_invoice') }}</li>
        </ol>
        <div class="page-actions row">
          <base-button class="mr-3" outline color="theme">
            {{ $t('general.download_pdf') }}
          </base-button>
          <base-button
            :loading="isLoading"
            icon="save"
            color="theme"
            type="submit">
            {{ $t('invoices.save_invoice') }}
          </base-button>
        </div>
      </div>
      <div class="row invoice-input-group">
        <div class="col-md-5">
          <div
            v-if="selectedCustomer"
            class="show-customer"
          >
            <div class="row p-2">
              <div class="col col-6">
                <div v-if="selectedCustomer.billing_address != null" class="row address-menu">
                  <label class="col-sm-4 px-2 title">{{ $t('general.bill_to') }}</label>
                  <div class="col-sm p-0 px-2 content">
                    <label>{{ selectedCustomer.billing_address.name }}</label>
                    <label>{{ selectedCustomer.billing_address.address_street_1 }}</label>
                    <label>{{ selectedCustomer.billing_address.address_street_2 }}</label>
                    <label>
                      {{ selectedCustomer.billing_address.city.name }}, {{ selectedCustomer.billing_address.state.name }} {{ selectedCustomer.billing_address.zip }}
                    </label>
                    <label>{{ selectedCustomer.billing_address.country.name }}</label>
                    <label>{{ selectedCustomer.billing_address.phone }}</label>
                  </div>
                </div>
              </div>
              <div class="col col-6">
                <div v-if="selectedCustomer.shipping_address != null" class="row address-menu">
                  <label class="col-sm-4 px-2 title">{{ $t('general.ship_to') }}</label>
                  <div class="col-sm p-0 px-2 content">
                    <label>{{ selectedCustomer.shipping_address.name }}</label>
                    <label>{{ selectedCustomer.shipping_address.address_street_1 }}</label>
                    <label>{{ selectedCustomer.shipping_address.address_street_2 }}</label>
                    <label v-show="selectedCustomer.shipping_address.city">
                      {{ selectedCustomer.shipping_address.city.name }}, {{ selectedCustomer.shipping_address.state.name }} {{ selectedCustomer.shipping_address.zip }}
                    </label>
                    <label class="country">{{ selectedCustomer.shipping_address.country.name }}</label>
                    <label class="phone">{{ selectedCustomer.shipping_address.phone }}</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="customer-content">
              <label class="email">{{ selectedCustomer.email ? selectedCustomer.email : selectedCustomer.name }}</label>
              <label class="action" @click="removeCustomer">{{ $t('general.remove') }}</label>
            </div>
          </div>

          <base-popup v-else class="add-customer">
            <div slot="activator" class="add-customer-action">
              <font-awesome-icon icon="user" class="customer-icon"/>
              <label>{{ $t('customers.new_customer') }}<span class="text-danger"> * </span></label>
            </div>
            <customer-select />
          </base-popup>
        </div>
        <div class="col invoice-input">
          <div class="row mb-3">
            <div class="col">
              <label>{{ $t('invoices.invoice_date') }}<span class="text-danger"> * </span></label>
              <base-date-picker
                v-model="newInvoice.invoice_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
                @change="$v.newInvoice.invoice_date.$touch()"
              />
              <span v-if="$v.newInvoice.invoice_date.$error && !$v.newInvoice.invoice_date.required" class="text-danger"> {{ $t('validation.required') }} </span>
            </div>
            <div class="col">
              <label>{{ $t('invoices.due_date') }}<span class="text-danger"> * </span></label>
              <base-date-picker
                v-model="newInvoice.due_date"
                :invalid="$v.newInvoice.due_date.$error"
                :calendar-button="true"
                calendar-button-icon="calendar"
                @change="$v.newInvoice.due_date.$touch()"
              />
              <span v-if="$v.newInvoice.due_date.$error && !$v.newInvoice.due_date.required" class="text-danger mt-1"> {{ $t('validation.required') }}</span>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col">
              <label>{{ $t('invoices.invoice_number') }}<span class="text-danger"> * </span></label>
              <base-input
                :invalid="$v.newInvoice.invoice_number.$error"
                :read-only="true"
                v-model="newInvoice.invoice_number"
                icon="hashtag"
                @input="$v.newInvoice.invoice_number.$touch()"
              />
              <span v-show="$v.newInvoice.invoice_number.$error && !$v.newInvoice.invoice_number.required" class="text-danger mt-1"> {{ $tc('validation.required') }}  </span>
            </div>
            <div class="col">
              <label>{{ $t('invoices.ref_number') }}</label>
              <base-input icon="hashtag" type="number"/>
            </div>
          </div>
        </div>
      </div>

      <table class="item-table">
        <colgroup>
          <col style="width: 50%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col v-if="discountPerItem === 'YES'" style="width: 15%;">
          <col style="width: 15%;">
        </colgroup>
        <thead class="item-table-header">
          <tr>
            <th class="text-left">
              <span class="column-heading item-heading">
                {{ $tc('items.item',2) }}
              </span>
            </th>
            <th class="text-right">
              <span class="column-heading">
                {{ $t('invoices.item.quantity') }}
              </span>
            </th>
            <th class="text-left">
              <span class="column-heading">
                {{ $t('invoices.item.price') }}
              </span>
            </th>
            <th v-if="discountPerItem === 'YES'" class="text-right">
              <span class="column-heading">
                {{ $t('invoices.item.discount') }}
              </span>
            </th>
            <th class="text-right">
              <span class="column-heading amount-heading">
                {{ $t('invoices.item.amount') }}
              </span>
            </th>
          </tr>
        </thead>
        <tbody class="item-body">
          <invoice-item
            v-for="(item, index) in newInvoice.items"
            :key="'inv-item-' + item.id"
            :index="index"
            :item-data="item"
            :currency="currency"
            :tax-per-item="taxPerItem"
            :discount-per-item="discountPerItem"
            @remove="removeItem"
            @update="updateItem"
            @itemValidate="checkItemsData"
          />
        </tbody>
      </table>
      <div class="add-item-action" @click="addItem">
        <font-awesome-icon icon="shopping-basket" class="mr-2"/>
        {{ $t('invoices.add_item') }}
      </div>

      <div class="invoice-foot">
        <div>
          <label>{{ $t('invoices.notes') }}</label>
          <base-text-area
            v-model="newInvoice.notes"
            rows="3"
            cols="50"
          />
          <label class="mt-3 mb-1 d-block">{{ $t('invoices.invoice_template') }} <span class="text-danger"> * </span></label>
          <base-button class="btn-template" icon="pencil-alt" right-icon @click="openTemplateModal" >
            <span class="mr-4"> {{ $t('invoices.invoice_template') }} {{ getTemplateId }} </span>
          </base-button>
        </div>

        <div class="invoice-total">
          <div class="section">
            <label class="invoice-label">{{ $t('invoices.sub_total') }}</label>
            <label class="invoice-amount">
              <div v-html="$utils.formatMoney(subtotal, currency)" />
            </label>
          </div>
          <div v-for="tax in allTaxes" :key="tax.tax_type_id" class="section">
            <label class="invoice-label">{{ tax.name }} - {{ tax.percent }}% </label>
            <label class="invoice-amount">
              <div v-html="$utils.formatMoney(tax.amount, currency)" />
            </label>
          </div>
          <div v-if="discountPerItem === 'NO' || discountPerItem === null" class="section mt-2">
            <label class="invoice-label">{{ $t('invoices.discount') }}</label>
            <div
              class="btn-group discount-drop-down"
              role="group"
            >
              <base-input
                v-model="discount"
                input-class="item-discount"
              />
              <v-dropdown :show-arrow="false">
                <button
                  slot="activator"
                  type="button"
                  class="btn item-dropdown dropdown-toggle"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  {{ newInvoice.discount_type == 'fixed' ? currency.symbol : '%' }}
                </button>
                <v-dropdown-item>
                  <a class="dropdown-item" href="#" @click.prevent="selectFixed">
                    {{ $t('general.fixed') }}
                  </a>
                </v-dropdown-item>
                <v-dropdown-item>
                  <a class="dropdown-item" href="#" @click.prevent="selectPercentage">
                    {{ $t('general.percentage') }}
                  </a>
                </v-dropdown-item>
              </v-dropdown>
            </div>
          </div>

          <div v-if="taxPerItem === 'NO' || taxPerItem === null">
            <tax
              v-for="(tax, index) in newInvoice.taxes"
              :index="index"
              :total="subtotalWithDiscount"
              :key="tax.taxKey"
              :tax="tax"
              :taxes="newInvoice.taxes"
              :currency="currency"
              :total-tax="totalSimpleTax"
              @remove="removeInvoiceTax"
              @update="updateTax"
            />
          </div>

          <base-popup v-if="taxPerItem === 'NO' || taxPerItem === null" ref="taxModal" class="tax-selector">
            <div slot="activator" class="float-right">
              + {{ $t('invoices.add_tax') }}
            </div>
            <tax-select @select="onSelectTax"/>
          </base-popup>

          <div class="section border-top mt-3">
            <label class="invoice-label">{{ $t('invoices.total') }} {{ $t('invoices.amount') }}:</label>
            <label class="invoice-amount total">
              <div v-html="$utils.formatMoney(total, currency)" />
            </label>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>

import MultiSelect from 'vue-multiselect'
import InvoiceItem from './Item'
import InvoiceStub from '../../stub/invoice'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { validationMixin } from 'vuelidate'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './InvoiceTax'
const { required } = require('vuelidate/lib/validators')

export default {
  components: {
    InvoiceItem,
    MultiSelect,
    Tax
  },
  mixins: [validationMixin],
  data () {
    return {
      newInvoice: {
        invoice_date: null,
        due_date: null,
        invoice_number: null,
        user_id: null,
        invoice_template_id: 1,
        sub_total: null,
        total: null,
        tax: null,
        notes: null,
        discount_type: 'fixed',
        discount_val: 0,
        discount: 0,
        items: [{
          ...InvoiceStub,
          id: 1
        }],
        taxes: []
      },
      invoiceTemplates: [],
      selectedCurrency: '',
      newItem: {
        ...InvoiceStub
      },
      taxPerItem: null,
      discountPerItem: null,
      isLoading: false
    }
  },
  validations: {
    newInvoice: {
      invoice_date: {
        required
      },
      due_date: {
        required
      },
      invoice_number: {
        required
      }
    }
  },
  computed: {
    ...mapGetters('currency', [
      'defaultCurrency'
    ]),
    currency () {
      return this.selectedCurrency
    },
    subtotalWithDiscount () {
      return this.subtotal - this.newInvoice.discount_val
    },
    total () {
      return this.subtotalWithDiscount + this.totalTax
    },
    subtotal () {
      return this.newInvoice.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },
    discount: {
      get: function () {
        return this.newInvoice.discount
      },
      set: function (newValue) {
        if (this.newInvoice.discount_type === 'percentage') {
          this.newInvoice.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.newInvoice.discount_val = newValue * 100
        }

        this.newInvoice.discount = newValue
      }
    },
    totalSimpleTax () {
      return window._.sumBy(this.newInvoice.taxes, function (tax) {
        if (!tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },

    totalCompoundTax () {
      return window._.sumBy(this.newInvoice.taxes, function (tax) {
        if (tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },
    totalTax () {
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      }

      return window._.sumBy(this.newInvoice.items, function (tax) {
        return tax.totalTax
      })
    },
    allTaxes () {
      let taxes = []

      this.newInvoice.items.forEach((item) => {
        item.taxes.forEach((tax) => {
          let found = taxes.find((_tax) => {
            return _tax.tax_type_id === tax.tax_type_id
          })

          if (found) {
            found.amount += tax.amount
          } else if (tax.tax_type_id) {
            taxes.push({
              tax_type_id: tax.tax_type_id,
              amount: tax.amount,
              percent: tax.percent,
              name: tax.name
            })
          }
        })
      })

      return taxes
    },
    ...mapGetters('customer', [
      'selectedCustomer'
    ]),
    ...mapGetters('invoice', [
      'getTemplateId'
    ]),
    ...mapGetters('general', [
      'itemDiscount'
    ])
  },
  watch: {
    selectedCustomer (newVal) {

      if (newVal.currency !== null) {
        this.selectedCurrency = newVal.currency
      } else {
        this.selectedCurrency = this.defaultCurrency
      }
    },
    subtotal (newValue) {
      if (this.newInvoice.discount_type === 'percentage') {
        this.newInvoice.discount_val = (this.newInvoice.discount * newValue) / 100
      }
    }
  },
  mounted () {
    this.$nextTick(() => {
      this.loadData()
    })
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('customer', [
      'resetSelectedCustomer'
    ]),
    ...mapActions('taxType', {
      loadTaxTypes: 'indexLoadData'
    }),
    ...mapActions('invoice', [
      'addInvoice',
      'fetchInvoice'
    ]),
    selectFixed () {
      if (this.newInvoice.discount_type === 'fixed') {
        return
      }

      this.newInvoice.discount_val = this.newInvoice.discount * 100
      this.newInvoice.discount_type = 'fixed'
    },
    selectPercentage () {
      if (this.newInvoice.discount_type === 'percentage') {
        return
      }

      this.newInvoice.discount_val = (this.subtotal * this.newInvoice.discount) / 100

      this.newInvoice.discount_type = 'percentage'
    },
    updateTax (data) {
      Object.assign(this.newInvoice.taxes[data.index], {...data.item})
    },
    async loadData () {
      let response = await this.fetchInvoice(this.$route.params.id)

      this.loadTaxTypes()

      if (response.data) {
        this.newInvoice = response.data.invoice
        this.discountPerItem = response.data.discount_per_item
        this.taxPerItem = response.data.tax_per_item
        this.selectedCurrency = this.defaultCurrency
        this.invoiceTemplates = response.data.invoiceTemplates
      }
    },
    removeCustomer () {
      this.resetSelectedCustomer()
    },
    openTemplateModal () {
      this.openModal({
        'title': this.$t('general.choose_template'),
        'componentName': 'InvoiceTemplate',
        'data': this.invoiceTemplates
      })
    },
    addItem () {
      this.newInvoice.items.push({...this.newItem, id: (this.newInvoice.items.length + 1)})
    },
    removeItem (index) {
      this.newInvoice.items.splice(index, 1)
    },
    updateItem (data) {
      Object.assign(this.newInvoice.items[data.index], {...data.item})
    },
    async submitInvoiceData () {
      if (!this.checkValid()) {
        return false
      }

      this.isLoading = true

      let data = {
        ...this.newInvoice,
        invoice_date: moment(this.newInvoice.invoice_date).format('DD/MM/YYYY'),
        due_date: moment(this.newInvoice.due_date).format('DD/MM/YYYY'),
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        invoice_template_id: this.getTemplateId
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer.id
      }
      let response = await this.addInvoice(data)

      if (response.data) {
        window.toastr['success'](this.$t('invoices.created_message'))
        this.isLoading = false
        this.$route.push('/admin/invoices')
      }
    },
    checkItemsData (index, isValid) {
      this.newInvoice.items[index].valid = isValid
    },
    onSelectTax (selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = ((this.subtotalWithDiscount + this.totalSimpleTax) * selectedTax.percent) / 100
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = (this.subtotalWithDiscount * selectedTax.percent) / 100
      }

      this.newInvoice.taxes.push({
        ...TaxStub,
        taxKey: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount
      })

      this.$refs.taxModal.close()
    },
    removeInvoiceTax (index) {
      this.newInvoice.taxes.splice(index, 1)
    },
    checkValid () {
      this.$v.newInvoice.$touch()
      window.hub.$emit('checkItems')
      let isValid = true
      this.newInvoice.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (this.$v.newInvoice.$invalid === false && isValid === true) {
        return true
      }
      return false
    }
  }
}
</script>

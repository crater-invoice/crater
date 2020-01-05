<template>
  <div class="invoice-create-page main-content">
    <form v-if="!initLoading" action="" @submit.prevent="submitInvoiceData">
      <div class="page-header">
        <h3 v-if="$route.name === 'invoices.edit'" class="page-title">{{ $t('invoices.edit_invoice') }}</h3>
        <h3 v-else class="page-title">{{ $t('invoices.new_invoice') }} </h3>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/invoices">{{ $tc('invoices.invoice', 2) }}</router-link></li>
          <li v-if="$route.name === 'invoices.edit'" class="breadcrumb-item">{{ $t('invoices.edit_invoice') }}</li>
          <li v-else class="breadcrumb-item">{{ $t('invoices.new_invoice') }}</li>
        </ol>
        <div class="page-actions row">
          <a v-if="$route.name === 'invoices.edit'" :href="`/invoices/pdf/${newInvoice.unique_hash}`" target="_blank" class="mr-3 invoice-action-btn base-button btn btn-outline-primary default-size" outline color="theme">
            {{ $t('general.view_pdf') }}
          </a>
          <base-button
            :loading="isLoading"
            :disabled="isLoading"
            icon="save"
            color="theme"
            class="invoice-action-btn"
            type="submit">
            {{ $t('invoices.save_invoice') }}
          </base-button>
        </div>
      </div>
      <div class="row invoice-input-group">
        <div class="col-md-5 invoice-customer-container">
          <div
            v-if="selectedCustomer" class="show-customer">
            <div class="row px-2 mt-1">
              <div v-if="selectedCustomer.billing_address" class="col col-6">
                <div class="row address-menu">
                  <label class="col-sm-4 px-2 title">{{ $t('general.bill_to') }}</label>
                  <div class="col-sm p-0 px-2 content">
                    <label v-if="selectedCustomer.billing_address.name">
                      {{ selectedCustomer.billing_address.name }}
                    </label>
                    <label v-if="selectedCustomer.billing_address.address_street_1">
                      {{ selectedCustomer.billing_address.address_street_1 }}
                    </label>
                    <label v-if="selectedCustomer.billing_address.address_street_2">
                      {{ selectedCustomer.billing_address.address_street_2 }}
                    </label>
                    <label v-if="selectedCustomer.billing_address.city && selectedCustomer.billing_address.state">
                      {{ selectedCustomer.billing_address.city }}, {{ selectedCustomer.billing_address.state }} {{ selectedCustomer.billing_address.zip }}
                    </label>
                    <label v-if="selectedCustomer.billing_address.country">
                      {{ selectedCustomer.billing_address.country.name }}
                    </label>
                    <label v-if="selectedCustomer.billing_address.phone">
                      {{ selectedCustomer.billing_address.phone }}
                    </label>
                  </div>
                </div>
              </div>
              <div v-if="selectedCustomer.shipping_address" class="col col-6">
                <div class="row address-menu">
                  <label class="col-sm-4 px-2 title">{{ $t('general.ship_to') }}</label>
                  <div class="col-sm p-0 px-2 content">
                    <label v-if="selectedCustomer.shipping_address.name">
                      {{ selectedCustomer.shipping_address.name }}
                    </label>
                    <label v-if="selectedCustomer.shipping_address.address_street_1">
                      {{ selectedCustomer.shipping_address.address_street_1 }}
                    </label>
                    <label v-if="selectedCustomer.shipping_address.address_street_2">
                      {{ selectedCustomer.shipping_address.address_street_2 }}
                    </label>
                    <label v-if="selectedCustomer.shipping_address.city && selectedCustomer.shipping_address">
                      {{ selectedCustomer.shipping_address.city }}, {{ selectedCustomer.shipping_address.state }} {{ selectedCustomer.shipping_address.zip }}
                    </label>
                    <label v-if="selectedCustomer.shipping_address.country" class="country">
                      {{ selectedCustomer.shipping_address.country.name }}
                    </label>
                    <label v-if="selectedCustomer.shipping_address.phone" class="phone">
                      {{ selectedCustomer.shipping_address.phone }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="customer-content mb-1">
              <label class="email">{{ selectedCustomer.name }}</label>
              <label class="action" @click="editCustomer">{{ $t('general.edit') }}</label>
              <label class="action" @click="removeCustomer">{{ $t('general.deselect') }}</label>
            </div>
          </div>

          <base-popup v-else :class="['add-customer', {'customer-required': $v.selectedCustomer.$error}]" >
            <div slot="activator" class="add-customer-action">
              <font-awesome-icon icon="user" class="customer-icon"/>
              <div>
                <label>{{ $t('customers.new_customer') }} <span class="text-danger"> * </span></label>
                <p v-if="$v.selectedCustomer.$error && !$v.selectedCustomer.required" class="text-danger">
                  {{ $t('validation.required') }}
                </p>
              </div>
            </div>
            <customer-select-popup type="invoice" />
          </base-popup>
        </div>
        <div class="col invoice-input">
          <div class="row mb-3">
            <div class="col collapse-input">
              <label>{{ $tc('invoices.invoice',1) }} {{ $t('invoices.date') }}<span class="text-danger"> * </span></label>
              <base-date-picker
                v-model="newInvoice.invoice_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
                @change="$v.newInvoice.invoice_date.$touch()"
              />
              <span v-if="$v.newInvoice.invoice_date.$error && !$v.newInvoice.invoice_date.required" class="text-danger"> {{ $t('validation.required') }} </span>
            </div>
            <div class="col collapse-input">
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
            <div class="col collapse-input">
              <label>{{ $t('invoices.invoice_number') }}<span class="text-danger"> * </span></label>
              <base-prefix-input
                v-model="invoiceNumAttribute"
                :invalid="$v.invoiceNumAttribute.$error"
                :prefix="invoicePrefix"
                icon="hashtag"
                @input="$v.invoiceNumAttribute.$touch()"
              />
              <span v-show="$v.invoiceNumAttribute.$error && !$v.invoiceNumAttribute.required" class="text-danger mt-1"> {{ $tc('validation.required') }}  </span>
              <span v-show="!$v.invoiceNumAttribute.numeric" class="text-danger mt-1"> {{ $tc('validation.numbers_only') }}  </span>
            </div>
            <div class="col collapse-input">
              <label>{{ $t('invoices.ref_number') }}</label>
              <base-input
                v-model="newInvoice.reference_number"
                :invalid="$v.newInvoice.reference_number.$error"
                icon="hashtag"
                @input="$v.newInvoice.reference_number.$touch()"
              />
              <div v-if="$v.newInvoice.reference_number.$error" class="text-danger">{{ $tc('validation.ref_number_maxlength') }}</div>
            </div>
          </div>
        </div>
      </div>
      <table class="item-table">
        <colgroup>
          <col style="width: 40%;">
          <col style="width: 10%;">
          <col style="width: 15%;">
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
        <draggable v-model="newInvoice.items" class="item-body" tag="tbody" handle=".handle">
          <invoice-item
            v-for="(item, index) in newInvoice.items"
            :key="item.id"
            :index="index"
            :item-data="item"
            :invoice-items="newInvoice.items"
            :currency="currency"
            :tax-per-item="taxPerItem"
            :discount-per-item="discountPerItem"
            @remove="removeItem"
            @update="updateItem"
            @itemValidate="checkItemsData"
          />
        </draggable>
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
            @input="$v.newInvoice.notes.$touch()"
          />
          <div v-if="$v.newInvoice.notes.$error">
            <span v-if="!$v.newInvoice.notes.maxLength" class="text-danger">{{ $t('validation.notes_maxlength') }}</span>
          </div>
          <label class="mt-3 mb-1 d-block">{{ $t('invoices.invoice_template') }} <span class="text-danger"> * </span></label>
          <base-button type="button" class="btn-template" icon="pencil-alt" right-icon @click="openTemplateModal" >
            <span class="mr-4"> {{ $t('invoices.template') }} {{ getTemplateId }} </span>
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
                :invalid="$v.newInvoice.discount_val.$error"
                input-class="item-discount"
                @input="$v.newInvoice.discount_val.$touch()"
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
              :key="tax.id"
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
            <tax-select-popup :taxes="newInvoice.taxes" @select="onSelectTax"/>
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
    <base-loader v-else />
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import MultiSelect from 'vue-multiselect'
import InvoiceItem from './Item'
import InvoiceStub from '../../stub/invoice'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { validationMixin } from 'vuelidate'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './InvoiceTax'
const { required, between, maxLength, numeric } = require('vuelidate/lib/validators')

export default {
  components: {
    InvoiceItem,
    MultiSelect,
    Tax,
    draggable
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
        reference_number: null,
        items: [{
          ...InvoiceStub,
          id: Guid.raw(),
          taxes: [{...TaxStub, id: Guid.raw()}]
        }],
        taxes: []
      },
      customers: [],
      itemList: [],
      invoiceTemplates: [],
      selectedCurrency: '',
      taxPerItem: null,
      discountPerItem: null,
      initLoading: false,
      isLoading: false,
      maxDiscount: 0,
      invoicePrefix: null,
      invoiceNumAttribute: null
    }
  },
  validations () {
    return {
      newInvoice: {
        invoice_date: {
          required
        },
        due_date: {
          required
        },
        discount_val: {
          between: between(0, this.subtotal)
        },
        notes: {
          maxLength: maxLength(255)
        },
        reference_number: {
          maxLength: maxLength(255)
        }
      },
      selectedCustomer: {
        required
      },
      invoiceNumAttribute: {
        required,
        numeric
      }
    }
  },
  computed: {
    ...mapGetters('general', [
      'itemDiscount'
    ]),
    ...mapGetters('currency', [
      'defaultCurrency'
    ]),
    ...mapGetters('invoice', [
      'getTemplateId',
      'selectedCustomer'
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
        return tax.tax
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
    }
  },
  watch: {
    selectedCustomer (newVal) {
      if (newVal && newVal.currency) {
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
  created () {
    this.loadData()
    this.fetchInitialItems()
    this.resetSelectedCustomer()
    window.hub.$on('newTax', this.onSelectTax)
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('invoice', [
      'addInvoice',
      'fetchCreateInvoice',
      'fetchInvoice',
      'resetSelectedCustomer',
      'selectCustomer',
      'updateInvoice'
    ]),
    ...mapActions('item', [
      'fetchItems'
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
    async fetchInitialItems () {
      await this.fetchItems({
        filter: {},
        orderByField: '',
        orderBy: ''
      })
    },
    async loadData () {
      if (this.$route.name === 'invoices.edit') {
        this.initLoading = true
        let response = await this.fetchInvoice(this.$route.params.id)

        if (response.data) {
          this.selectCustomer(response.data.invoice.user_id)
          this.newInvoice = response.data.invoice
          this.newInvoice.invoice_date = moment(response.data.invoice.invoice_date, 'YYYY-MM-DD').toString()
          this.newInvoice.due_date = moment(response.data.invoice.due_date, 'YYYY-MM-DD').toString()
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
          this.selectedCurrency = this.defaultCurrency
          this.invoiceTemplates = response.data.invoiceTemplates
          this.invoicePrefix = response.data.invoice_prefix
          this.invoiceNumAttribute = response.data.nextInvoiceNumber
        }
        this.initLoading = false
        return
      }

      this.initLoading = true
      let response = await this.fetchCreateInvoice()
      if (response.data) {
        this.discountPerItem = response.data.discount_per_item
        this.taxPerItem = response.data.tax_per_item
        this.selectedCurrency = this.defaultCurrency
        this.invoiceTemplates = response.data.invoiceTemplates
        let today = new Date()
        this.newInvoice.invoice_date = moment(today).toString()
        this.newInvoice.due_date = moment(today).add(7, 'days').toString()
        this.itemList = response.data.items
        this.invoicePrefix = response.data.invoice_prefix
        this.invoiceNumAttribute = response.data.nextInvoiceNumberAttribute
      }
      this.initLoading = false
    },
    removeCustomer () {
      this.resetSelectedCustomer()
    },
    editCustomer () {
      this.openModal({
        'title': this.$t('customers.edit_customer'),
        'componentName': 'CustomerModal',
        'id': this.selectedCustomer.id,
        'data': this.selectedCustomer
      })
    },
    openTemplateModal () {
      this.openModal({
        'title': this.$t('general.choose_template'),
        'componentName': 'InvoiceTemplate',
        'data': this.invoiceTemplates
      })
    },
    addItem () {
      this.newInvoice.items.push({...InvoiceStub, id: Guid.raw(), taxes: [{...TaxStub, id: Guid.raw()}]})
    },
    removeItem (index) {
      this.newInvoice.items.splice(index, 1)
    },
    updateItem (data) {
      Object.assign(this.newInvoice.items[data.index], {...data.item})
    },
    submitInvoiceData () {
      if (!this.checkValid()) {
        return false
      }

      this.isLoading = true
      this.newInvoice.invoice_number = this.invoicePrefix + '-' + this.invoiceNumAttribute

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

      if (this.$route.name === 'invoices.edit') {
        this.submitUpdate(data)
        return
      }

      this.submitSave(data)
    },
    submitSave (data) {
      this.addInvoice(data).then((res) => {
        if (res.data) {
          window.toastr['success'](this.$t('invoices.created_message'))
          this.$router.push('/admin/invoices')
        }

        this.isLoading = false
      }).catch((err) => {
        this.isLoading = false
        if (err.response.data.errors.invoice_number) {
          window.toastr['error'](err.response.data.errors.invoice_number)
          return true
        }
        console.log(err)
      })
    },
    submitUpdate (data) {
      this.updateInvoice(data).then((res) => {
        this.isLoading = false
        if (res.data.success) {
          window.toastr['success'](this.$t('invoices.updated_message'))
          this.$router.push('/admin/invoices')
        }

        if (res.data.error === 'invalid_due_amount') {
          window.toastr['error'](this.$t('invoices.invalid_due_amount_message'))
        }
      }).catch((err) => {
        this.isLoading = false
        if (err.response.data.errors.invoice_number) {
          window.toastr['error'](err.response.data.errors.invoice_number)
          return true
        }
        console.log(err)
      })
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
        id: Guid.raw(),
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
      this.$v.selectedCustomer.$touch()

      window.hub.$emit('checkItems')
      let isValid = true
      this.newInvoice.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (!this.$v.selectedCustomer.$invalid && this.$v.newInvoice.$invalid === false && isValid === true) {
        return true
      }
      return false
    }
  }
}
</script>

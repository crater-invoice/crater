<template>
  <div class="estimate-create-page main-content">
    <form v-if="!initLoading" action="" @submit.prevent="submitEstimateData">
      <div class="page-header">
        <h3 v-if="$route.name === 'estimates.edit'" class="page-title">{{ $t('estimates.edit_estimate') }}</h3>
        <h3 v-else class="page-title">{{ $t('estimates.new_estimate') }}</h3>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/dashboard">{{ $t('general.home') }}</router-link></li>
          <li class="breadcrumb-item"><router-link slot="item-title" to="/admin/estimates">{{ $tc('estimates.estimate', 2) }}</router-link></li>
          <li v-if="$route.name === 'estimates.edit'" class="breadcrumb-item">{{ $t('estimates.edit_estimate') }}</li>
          <li v-else class="breadcrumb-item">{{ $t('estimates.new_estimate') }}</li>
        </ol>
        <div class="page-actions row">
          <a v-if="$route.name === 'estimates.edit'" :href="`/estimates/pdf/${newEstimate.unique_hash}`" target="_blank" class="mr-3 base-button btn btn-outline-primary default-size invoice-action-btn" outline color="theme">
            {{ $t('general.view_pdf') }}
          </a>
          <base-button
            :loading="isLoading"
            :disabled="isLoading"
            icon="save"
            class="invoice-action-btn"
            color="theme"
            type="submit">
            {{ $t('estimates.save_estimate') }}
          </base-button>
        </div>
      </div>
      <div class="row estimate-input-group">
        <div class="col-md-5 estimate-customer-container">
          <div
            v-if="selectedCustomer"
            class="show-customer"
          >
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
                <p v-if="$v.selectedCustomer.$error && !$v.selectedCustomer.required" class="text-danger"> {{ $t('estimates.errors.required') }} </p>
              </div>
            </div>
            <customer-select-popup type="estimate" />
          </base-popup>
        </div>
        <div class="col estimate-input">
          <div class="row mb-3">
            <div class="col collapse-input">
              <label>{{ $t('reports.estimates.estimate_date') }}<span class="text-danger"> * </span></label>
              <base-date-picker
                v-model="newEstimate.estimate_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
                @change="$v.newEstimate.estimate_date.$touch()"
              />
              <span v-if="$v.newEstimate.estimate_date.$error && !$v.newEstimate.estimate_date.required" class="text-danger"> {{ $t('validation.required') }} </span>
            </div>
            <div class="col collapse-input">
              <label>{{ $t('estimates.due_date') }}<span class="text-danger"> * </span></label>
              <base-date-picker
                v-model="newEstimate.expiry_date"
                :invalid="$v.newEstimate.expiry_date.$error"
                :calendar-button="true"
                calendar-button-icon="calendar"
                @change="$v.newEstimate.expiry_date.$touch()"
              />
              <span v-if="$v.newEstimate.expiry_date.$error && !$v.newEstimate.expiry_date.required" class="text-danger mt-1"> {{ $t('validation.required') }}</span>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col collapse-input">
              <label>{{ $t('estimates.estimate_number') }}<span class="text-danger"> * </span></label>
              <base-prefix-input
                v-model="estimateNumAttribute"
                :invalid="$v.estimateNumAttribute.$error"
                :prefix="estimatePrefix"
                icon="hashtag"
                @input="$v.estimateNumAttribute.$touch()"
              />
              <span v-show="$v.estimateNumAttribute.$error && !$v.estimateNumAttribute.required" class="text-danger mt-1"> {{ $tc('estimates.errors.required') }}  </span>
              <span v-show="!$v.estimateNumAttribute.numeric" class="text-danger mt-1"> {{ $tc('validation.numbers_only') }}  </span>
            </div>
            <div class="col collapse-input">
              <label>{{ $t('estimates.ref_number') }}</label>
              <base-input
                v-model="newEstimate.reference_number"
                :invalid="$v.newEstimate.reference_number.$error"
                icon="hashtag"
                @input="$v.newEstimate.reference_number.$touch()"
              />
              <div v-if="$v.newEstimate.reference_number.$error" class="text-danger">{{ $tc('validation.ref_number_maxlength') }}</div>
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
                {{ $t('estimates.item.quantity') }}
              </span>
            </th>
            <th class="text-left">
              <span class="column-heading">
                {{ $t('estimates.item.price') }}
              </span>
            </th>
            <th v-if="discountPerItem === 'YES'" class="text-right">
              <span class="column-heading">
                {{ $t('estimates.item.discount') }}
              </span>
            </th>
            <th class="text-right">
              <span class="column-heading amount-heading">
                {{ $t('estimates.item.amount') }}
              </span>
            </th>
          </tr>
        </thead>
        <draggable v-model="newEstimate.items" class="item-body" tag="tbody" handle=".handle">
          <estimate-item
            v-for="(item, index) in newEstimate.items"
            :key="item.id"
            :index="index"
            :item-data="item"
            :currency="currency"
            :estimate-items="newEstimate.items"
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
        {{ $t('estimates.add_item') }}
      </div>

      <div class="estimate-foot">
        <div>
          <label>{{ $t('estimates.notes') }}</label>
          <base-text-area
            v-model="newEstimate.notes"
            rows="3"
            cols="50"
            @input="$v.newEstimate.notes.$touch()"
          />
          <div v-if="$v.newEstimate.notes.$error">
            <span v-if="!$v.newEstimate.notes.maxLength" class="text-danger">{{ $t('validation.notes_maxlength') }}</span>
          </div>
          <label class="mt-3 mb-1 d-block">{{ $t('estimates.estimate_template') }} <span class="text-danger"> * </span></label>
          <base-button type="button" class="btn-template" icon="pencil-alt" right-icon @click="openTemplateModal" >
            <span class="mr-4"> {{ $t('estimates.estimate_template') }} {{ getTemplateId }} </span>
          </base-button>
        </div>

        <div class="estimate-total">
          <div class="section">
            <label class="estimate-label">{{ $t('estimates.sub_total') }}</label>
            <label class="estimate-amount">
              <div v-html="$utils.formatMoney(subtotal, currency)" />
            </label>
          </div>
          <div v-for="tax in allTaxes" :key="tax.tax_type_id" class="section">
            <label class="estimate-label">{{ tax.name }} - {{ tax.percent }}% </label>
            <label class="estimate-amount">
              <div v-html="$utils.formatMoney(tax.amount, currency)" />
            </label>
          </div>
          <div v-if="discountPerItem === 'NO' || discountPerItem === null" class="section mt-2">
            <label class="estimate-label">{{ $t('estimates.discount') }}</label>
            <div
              class="btn-group discount-drop-down"
              role="group"
            >
              <base-input
                v-model="discount"
                :invalid="$v.newEstimate.discount_val.$error"
                input-class="item-discount"
                @input="$v.newEstimate.discount_val.$touch()"
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
                  {{ newEstimate.discount_type == 'fixed' ? currency.symbol : '%' }}
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
              v-for="(tax, index) in newEstimate.taxes"
              :index="index"
              :total="subtotalWithDiscount"
              :key="tax.id"
              :tax="tax"
              :taxes="newEstimate.taxes"
              :currency="currency"
              :total-tax="totalSimpleTax"
              @remove="removeEstimateTax"
              @update="updateTax"
            />
          </div>

          <base-popup v-if="taxPerItem === 'NO' || taxPerItem === null" ref="taxModal" class="tax-selector">
            <div slot="activator" class="float-right">
              + {{ $t('estimates.add_tax') }}
            </div>
            <tax-select-popup :taxes="newEstimate.taxes" @select="onSelectTax" />
          </base-popup>

          <div class="section border-top mt-3">
            <label class="estimate-label">{{ $t('estimates.total') }} {{ $t('estimates.amount') }}:</label>
            <label class="estimate-amount total">
              <div v-html="$utils.formatMoney(total, currency)" />
            </label>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import MultiSelect from 'vue-multiselect'
import EstimateItem from './Item'
import EstimateStub from '../../stub/estimate'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import { validationMixin } from 'vuelidate'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './EstimateTax'
const { required, between, maxLength, numeric } = require('vuelidate/lib/validators')

export default {
  components: {
    EstimateItem,
    MultiSelect,
    Tax,
    draggable
  },
  mixins: [validationMixin],
  data () {
    return {
      newEstimate: {
        estimate_date: null,
        expiry_date: null,
        estimate_number: null,
        user_id: null,
        estimate_template_id: 1,
        sub_total: null,
        total: null,
        tax: null,
        notes: null,
        discount_type: 'fixed',
        discount_val: 0,
        reference_number: null,
        discount: 0,
        items: [{
          ...EstimateStub,
          id: Guid.raw(),
          taxes: [{...TaxStub, id: Guid.raw()}]
        }],
        taxes: []
      },
      customers: [],
      itemList: [],
      estimateTemplates: [],
      selectedCurrency: '',
      taxPerItem: null,
      discountPerItem: null,
      initLoading: false,
      isLoading: false,
      maxDiscount: 0,
      estimatePrefix: null,
      estimateNumAttribute: null
    }
  },
  validations () {
    return {
      newEstimate: {
        estimate_date: {
          required
        },
        expiry_date: {
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
      estimateNumAttribute: {
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
    ...mapGetters('estimate', [
      'getTemplateId',
      'selectedCustomer'
    ]),
    currency () {
      return this.selectedCurrency
    },
    subtotalWithDiscount () {
      return this.subtotal - this.newEstimate.discount_val
    },
    total () {
      return this.subtotalWithDiscount + this.totalTax
    },
    subtotal () {
      return this.newEstimate.items.reduce(function (a, b) {
        return a + b['total']
      }, 0)
    },
    discount: {
      get: function () {
        return this.newEstimate.discount
      },
      set: function (newValue) {
        if (this.newEstimate.discount_type === 'percentage') {
          this.newEstimate.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.newEstimate.discount_val = newValue * 100
        }

        this.newEstimate.discount = newValue
      }
    },
    totalSimpleTax () {
      return window._.sumBy(this.newEstimate.taxes, function (tax) {
        if (!tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },

    totalCompoundTax () {
      return window._.sumBy(this.newEstimate.taxes, function (tax) {
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

      return window._.sumBy(this.newEstimate.items, function (tax) {
        return tax.tax
      })
    },
    allTaxes () {
      let taxes = []

      this.newEstimate.items.forEach((item) => {
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
      if (this.newEstimate.discount_type === 'percentage') {
        this.newEstimate.discount_val = (this.newEstimate.discount * newValue) / 100
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
    ...mapActions('estimate', [
      'addEstimate',
      'fetchCreateEstimate',
      'fetchEstimate',
      'resetSelectedCustomer',
      'selectCustomer',
      'updateEstimate'
    ]),
    ...mapActions('item', [
      'fetchItems'
    ]),
    selectFixed () {
      if (this.newEstimate.discount_type === 'fixed') {
        return
      }

      this.newEstimate.discount_val = this.newEstimate.discount * 100
      this.newEstimate.discount_type = 'fixed'
    },
    selectPercentage () {
      if (this.newEstimate.discount_type === 'percentage') {
        return
      }

      this.newEstimate.discount_val = (this.subtotal * this.newEstimate.discount) / 100

      this.newEstimate.discount_type = 'percentage'
    },
    updateTax (data) {
      Object.assign(this.newEstimate.taxes[data.index], {...data.item})
    },
    async fetchInitialItems () {
      await this.fetchItems({
        filter: {},
        orderByField: '',
        orderBy: ''
      })
    },
    async loadData () {
      if (this.$route.name === 'estimates.edit') {
        this.initLoading = true
        let response = await this.fetchEstimate(this.$route.params.id)

        if (response.data) {
          this.selectCustomer(response.data.estimate.user_id)
          this.newEstimate = response.data.estimate
          this.newEstimate.estimate_date = moment(response.data.estimate.estimate_date, 'YYYY-MM-DD').toString()
          this.newEstimate.expiry_date = moment(response.data.estimate.expiry_date, 'YYYY-MM-DD').toString()
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
          this.selectedCurrency = this.defaultCurrency
          this.estimateTemplates = response.data.estimateTemplates
          this.estimatePrefix = response.data.estimate_prefix
          this.estimateNumAttribute = response.data.nextEstimateNumber
        }
        this.initLoading = false
        return
      }

      this.initLoading = true
      let response = await this.fetchCreateEstimate()
      if (response.data) {
        this.discountPerItem = response.data.discount_per_item
        this.taxPerItem = response.data.tax_per_item
        this.selectedCurrency = this.defaultCurrency
        this.estimateTemplates = response.data.estimateTemplates
        let today = new Date()
        this.newEstimate.estimate_date = moment(today).toString()
        this.newEstimate.expiry_date = moment(today).add(7, 'days').toString()
        this.itemList = response.data.items
        this.estimatePrefix = response.data.estimate_prefix
        this.estimateNumAttribute = response.data.nextEstimateNumberAttribute
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
        'componentName': 'EstimateTemplate',
        'data': this.estimateTemplates
      })
    },
    addItem () {
      this.newEstimate.items.push({...EstimateStub, id: Guid.raw(), taxes: [{...TaxStub, id: Guid.raw()}]})
    },
    removeItem (index) {
      this.newEstimate.items.splice(index, 1)
    },
    updateItem (data) {
      Object.assign(this.newEstimate.items[data.index], {...data.item})
    },
    submitEstimateData () {
      if (!this.checkValid()) {
        return false
      }

      this.isLoading = true
      this.newEstimate.estimate_number = this.estimatePrefix + '-' + this.estimateNumAttribute

      let data = {
        ...this.newEstimate,
        estimate_date: moment(this.newEstimate.estimate_date).format('DD/MM/YYYY'),
        expiry_date: moment(this.newEstimate.expiry_date).format('DD/MM/YYYY'),
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        estimate_template_id: this.getTemplateId
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer.id
      }

      if (this.$route.name === 'estimates.edit') {
        this.submitUpdate(data)
        return
      }

      this.submitSave(data)
    },
    submitSave (data) {
      this.addEstimate(data).then((res) => {
        if (res.data) {
          window.toastr['success'](this.$t('estimates.created_message'))
          this.$router.push('/admin/estimates')
        }

        this.isLoading = false
      }).catch((err) => {
        this.isLoading = false
        if (err.response.data.errors.estimate_number) {
          window.toastr['error'](err.response.data.errors.estimate_number)
          return true
        }
        window.toastr['error'](err.response.data.message)
      })
    },
    submitUpdate (data) {
      this.updateEstimate(data).then((res) => {
        if (res.data) {
          window.toastr['success'](this.$t('estimates.updated_message'))
          this.$router.push('/admin/estimates')
        }

        this.isLoading = false
      }).catch((err) => {
        this.isLoading = false
        if (err.response.data.errors.estimate_number) {
          window.toastr['error'](err.response.data.errors.estimate_number)
          return true
        }
        window.toastr['error'](err.response.data.message)
      })
    },
    checkItemsData (index, isValid) {
      this.newEstimate.items[index].valid = isValid
    },
    onSelectTax (selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = ((this.subtotalWithDiscount + this.totalSimpleTax) * selectedTax.percent) / 100
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = (this.subtotalWithDiscount * selectedTax.percent) / 100
      }

      this.newEstimate.taxes.push({
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
    removeEstimateTax (index) {
      this.newEstimate.taxes.splice(index, 1)
    },
    checkValid () {
      this.$v.newEstimate.$touch()
      this.$v.selectedCustomer.$touch()
      window.hub.$emit('checkItems')
      let isValid = true
      this.newEstimate.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (!this.$v.selectedCustomer.$invalid && this.$v.newEstimate.$invalid === false && isValid === true) {
        return true
      }
      return false
    }
  }
}
</script>

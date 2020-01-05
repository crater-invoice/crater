<template>
  <tr class="item-row estimate-item-row">
    <td colspan="5">
      <table class="full-width">
        <colgroup>
          <col style="width: 40%;">
          <col style="width: 10%;">
          <col style="width: 15%;">
          <col v-if="discountPerItem === 'YES'" style="width: 15%;">
          <col style="width: 15%;">
        </colgroup>
        <tbody>
          <tr>
            <td class="">
              <div class="item-select-wrapper">
                <div class="sort-icon-wrapper handle">
                  <font-awesome-icon
                    class="sort-icon"
                    icon="grip-vertical"
                  />
                </div>
                <item-select
                  ref="itemSelect"
                  :invalid="$v.item.name.$error"
                  :invalid-description="$v.item.description.$error"
                  :item="item"
                  :tax-per-item="taxPerItem"
                  :taxes="item.taxes"
                  @search="searchVal"
                  @select="onSelectItem"
                  @deselect="deselectItem"
                  @onDesriptionInput="$v.item.description.$touch()"
                  @onSelectItem="isSelected = true"
                />
              </div>
            </td>
            <td class="text-right">
              <base-input
                v-model="item.quantity"
                :invalid="$v.item.quantity.$error"
                type="text"
                small
                @keyup="updateItem"
                @input="$v.item.quantity.$touch()"
              />
              <div v-if="$v.item.quantity.$error">
                <span v-if="!$v.item.quantity.maxLength" class="text-danger">{{ $t('validation.quantity_maxlength') }}</span>
              </div>
            </td>
            <td class="text-left">
              <div class="d-flex flex-column">
                <div class="flex-fillbd-highlight">
                  <div class="base-input">
                    <money
                      v-model="price"
                      v-bind="customerCurrency"
                      class="input-field"
                      @input="$v.item.price.$touch()"
                    />
                  </div>
                  <div v-if="$v.item.price.$error">
                    <span v-if="!$v.item.price.maxLength" class="text-danger">{{ $t('validation.price_maxlength') }}</span>
                  </div>
                </div>
              </div>
            </td>
            <td v-if="discountPerItem === 'YES'" class="">
              <div class="d-flex flex-column bd-highlight">
                <div
                  class="btn-group flex-fill bd-highlight"
                  role="group"
                >
                  <base-input
                    v-model="discount"
                    :invalid="$v.item.discount_val.$error"
                    input-class="item-discount"
                    @input="$v.item.discount_val.$touch()"
                  />
                  <v-dropdown :show-arrow="false" theme-light>
                    <button
                      slot="activator"
                      type="button"
                      class="btn item-dropdown dropdown-toggle"
                      data-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                    >
                      {{ item.discount_type == 'fixed' ? currency.symbol : '%' }}
                    </button>
                    <v-dropdown-item>
                      <a class="dropdown-item" href="#" @click.prevent="selectFixed" >
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
                <!-- <div v-if="$v.item.discount.$error"> discount error </div> -->
              </div>
            </td>
            <td class="text-right">
              <div class="item-amount">
                <span>
                  <div v-html="$utils.formatMoney(total, currency)" />
                </span>

                <div class="remove-icon-wrapper">
                  <font-awesome-icon
                    v-if="isShowRemoveItemIcon"
                    class="remove-icon"
                    icon="trash-alt"
                    @click="removeItem"
                  />
                </div>
              </div>
            </td>
          </tr>
          <tr v-if="taxPerItem === 'YES'" class="tax-tr">
            <td />
            <td colspan="4">
              <tax
                v-for="(tax, index) in item.taxes"
                :key="tax.id"
                :index="index"
                :tax-data="tax"
                :taxes="item.taxes"
                :discounted-total="total"
                :total-tax="totalSimpleTax"
                :total="total"
                :currency="currency"
                @update="updateTax"
                @remove="removeTax"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </td>
  </tr>
</template>
<script>
import Guid from 'guid'
import { validationMixin } from 'vuelidate'
import { mapGetters } from 'vuex'
import TaxStub from '../../stub/tax'
import EstimateStub from '../../stub/estimate'
import ItemSelect from './ItemSelect'
import Tax from './Tax'
const { required, minValue, between, maxLength } = require('vuelidate/lib/validators')

export default {
  components: {
    Tax,
    ItemSelect
  },
  mixins: [validationMixin],
  props: {
    itemData: {
      type: Object,
      default: null
    },
    index: {
      type: Number,
      default: null
    },
    type: {
      type: String,
      default: ''
    },
    currency: {
      type: [Object, String],
      required: true
    },
    taxPerItem: {
      type: String,
      default: ''
    },
    discountPerItem: {
      type: String,
      default: ''
    },
    estimateItems: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      isClosePopup: false,
      itemSelect: null,
      item: {...this.itemData},
      maxDiscount: 0,
      money: {
        decimal: '.',
        thousands: ',',
        prefix: '$ ',
        precision: 2,
        masked: false
      },
      isSelected: false
    }
  },
  computed: {
    ...mapGetters('item', [
      'items'
    ]),
    ...mapGetters('modal', [
      'modalActive'
    ]),
    ...mapGetters('currency', [
      'defaultCurrencyForInput'
    ]),
    customerCurrency () {
      if (this.currency) {
        return {
          decimal: this.currency.decimal_separator,
          thousands: this.currency.thousand_separator,
          prefix: this.currency.symbol + ' ',
          precision: this.currency.precision,
          masked: false
        }
      } else {
        return this.defaultCurrencyForInput
      }
    },
    isShowRemoveItemIcon () {
      if (this.estimateItems.length == 1) {
        return false
      }
      return true
    },
    subtotal () {
      return this.item.price * this.item.quantity
    },
    discount: {
      get: function () {
        return this.item.discount
      },
      set: function (newValue) {
        if (this.item.discount_type === 'percentage') {
          this.item.discount_val = (this.subtotal * newValue) / 100
        } else {
          this.item.discount_val = newValue * 100
        }

        this.item.discount = newValue
      }
    },
    total () {
      return this.subtotal - this.item.discount_val
    },
    totalSimpleTax () {
      return window._.sumBy(this.item.taxes, function (tax) {
        if (!tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },
    totalCompoundTax () {
      return window._.sumBy(this.item.taxes, function (tax) {
        if (tax.compound_tax) {
          return tax.amount
        }

        return 0
      })
    },
    totalTax () {
      return this.totalSimpleTax + this.totalCompoundTax
    },
    price: {
      get: function () {
        if (parseFloat(this.item.price) > 0) {
          return this.item.price / 100
        }

        return this.item.price
      },
      set: function (newValue) {
        if (parseFloat(newValue) > 0) {
          this.item.price = newValue * 100
          this.maxDiscount = this.item.price
        } else {
          this.item.price = newValue
        }
      }
    }
  },
  watch: {
    item: {
      handler: 'updateItem',
      deep: true
    },
    subtotal (newValue) {
      if (this.item.discount_type === 'percentage') {
        this.item.discount_val = (this.item.discount * newValue) / 100
      }
    },
    modalActive (val) {
      if (!val) {
        this.isSelected = false
      }
    }
  },
  validations () {
    return {
      item: {
        name: {
          required
        },
        quantity: {
          required,
          minValue: minValue(1),
          maxLength: maxLength(20)
        },
        price: {
          required,
          minValue: minValue(1),
          maxLength: maxLength(20)
        },
        discount_val: {
          between: between(0, this.maxDiscount)
        },
        description: {
          maxLength: maxLength(255)
        }
      }
    }
  },
  created () {
    window.hub.$on('checkItems', this.validateItem)
    window.hub.$on('newItem', (val) => {
      if (this.taxPerItem === 'YES') {
        this.item.taxes = val.taxes
      }
      if (!this.item.item_id && this.modalActive && this.isSelected) {
        this.onSelectItem(val)
      }
    })
  },
  methods: {
    updateTax (data) {
      this.$set(this.item.taxes, data.index, data.item)

      let lastTax = this.item.taxes[this.item.taxes.length - 1]

      if (lastTax.tax_type_id !== 0) {
        this.item.taxes.push({...TaxStub, id: Guid.raw()})
      }

      this.updateItem()
    },
    removeTax (index) {
      this.item.taxes.splice(index, 1)

      this.updateItem()
    },
    taxWithPercentage ({ name, percent }) {
      return `${name} (${percent}%)`
    },
    searchVal (val) {
      this.item.name = val
    },
    deselectItem () {
      this.item = {...EstimateStub, id: this.item.id, taxes: [{...TaxStub, id: Guid.raw()}]}
      this.$nextTick(() => {
        this.$refs.itemSelect.$refs.baseSelect.$refs.search.focus()
      })
    },
    onSelectItem (item) {
      this.item.name = item.name
      this.item.price = item.price
      this.item.item_id = item.id
      this.item.description = item.description
      if (this.taxPerItem === 'YES' && item.taxes) {
        let index = 0
        item.taxes.forEach(tax => {
          this.updateTax({index, item: { ...tax }})
          index++
        })
      }
      // if (this.item.taxes.length) {
      //   this.item.taxes = {...item.taxes}
      // }
    },
    selectFixed () {
      if (this.item.discount_type === 'fixed') {
        return
      }

      this.item.discount_val = this.item.discount * 100
      this.item.discount_type = 'fixed'
    },
    selectPercentage () {
      if (this.item.discount_type === 'percentage') {
        return
      }

      this.item.discount_val = (this.subtotal * this.item.discount) / 100

      this.item.discount_type = 'percentage'
    },
    updateItem () {
      this.$emit('update', {
        'index': this.index,
        'item': {
          ...this.item,
          total: this.total,
          totalSimpleTax: this.totalSimpleTax,
          totalCompoundTax: this.totalCompoundTax,
          totalTax: this.totalTax,
          tax: this.totalTax,
          taxes: [...this.item.taxes]
        }
      })
    },
    removeItem () {
      this.$emit('remove', this.index)
    },
    validateItem () {
      this.$v.item.$touch()

      if (this.item !== null) {
        this.$emit('itemValidate', this.index, !this.$v.$invalid)
      } else {
        this.$emit('itemValidate', this.index, false)
      }
    }
  }
}
</script>

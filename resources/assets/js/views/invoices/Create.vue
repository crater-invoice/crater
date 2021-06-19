<template>
  <base-page class="relative invoice-create-page">
    <form
      v-if="!isLoadingInvoice && !isLoadingData"
      @submit.prevent="submitForm"
    >
      <sw-page-header :title="pageTitle">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('general.home')"
            to="/admin/dashboard"
          />
          <sw-breadcrumb-item
            :title="$tc('invoices.invoice', 2)"
            to="/admin/invoices"
          />
          <sw-breadcrumb-item
            v-if="$route.name === 'invoices.edit'"
            :title="$t('invoices.edit_invoice')"
            to="#"
            active
          />
          <sw-breadcrumb-item
            v-else
            :title="$t('invoices.new_invoice')"
            to="#"
            active
          />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            v-if="$route.name === 'invoices.edit'"
            :disabled="isLoading"
            :href="`/invoices/pdf/${newInvoice.unique_hash}`"
            tag-name="a"
            variant="primary-outline"
            class="mr-3"
            target="_blank"
          >
            {{ $t('general.view_pdf') }}
          </sw-button>

          <sw-button
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            class="flex justify-center w-full lg:w-auto"
            type="submit"
            size="lg"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $t('invoices.save_invoice') }}
          </sw-button>
        </template>
      </sw-page-header>

      <!-- Select Customer & Basic Fields  -->
      <div class="grid-cols-12 gap-8 mt-6 mb-8 lg:grid">
        <customer-select
          :valid="$v.selectedCustomer"
          :customer-id="customerId"
          class="col-span-5 pr-0"
        />

        <div
          class="grid grid-cols-1 col-span-7 gap-4 mt-8 lg:gap-6 lg:mt-0 lg:grid-cols-2"
        >
          <sw-input-group
            :label="$t('invoices.invoice_date')"
            :error="invoiceDateError"
            required
          >
            <base-date-picker
              v-model="newInvoice.invoice_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
              class="mt-2"
              @input="$v.newInvoice.invoice_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('invoices.due_date')"
            :error="dueDateError"
            required
          >
            <base-date-picker
              v-model="newInvoice.due_date"
              :invalid="$v.newInvoice.due_date.$error"
              :calendar-button="true"
              calendar-button-icon="calendar"
              class="mt-2"
              @input="$v.newInvoice.due_date.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('invoices.invoice_number')"
            :error="invoiceNumError"
            class="lg:mt-0"
            required
          >
            <sw-input
              :prefix="`${invoicePrefix} - `"
              v-model="invoiceNumAttribute"
              :invalid="$v.invoiceNumAttribute.$error"
              class="mt-2"
              @input="$v.invoiceNumAttribute.$touch()"
            >
              <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-input-group
            :label="$t('invoices.ref_number')"
            :error="referenceError"
            class="lg:mt-0"
          >
            <sw-input
              v-model="newInvoice.reference_number"
              :invalid="$v.newInvoice.reference_number.$error"
              class="mt-2"
              @input="$v.newInvoice.reference_number.$touch()"
            >
              <hashtag-icon slot="leftIcon" class="h-4 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>
      </div>

      <!-- Items -->
      <table class="w-full text-center item-table">
        <colgroup>
          <col style="width: 40%" />
          <col style="width: 10%" />
          <col style="width: 15%" />
          <col v-if="discountPerItem === 'YES'" style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <thead class="bg-white border border-gray-200 border-solid">
          <tr>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span class="pl-12">
                {{ $tc('items.item', 2) }}
              </span>
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              {{ $t('invoices.item.quantity') }}
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              {{ $t('invoices.item.price') }}
            </th>
            <th
              v-if="discountPerItem === 'YES'"
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-left text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              {{ $t('invoices.item.discount') }}
            </th>
            <th
              class="px-5 py-3 text-sm not-italic font-medium leading-5 text-right text-gray-700 border-t border-b border-gray-200 border-solid"
            >
              <span class="pr-10">
                {{ $t('invoices.item.amount') }}
              </span>
            </th>
          </tr>
        </thead>

        <draggable
          v-model="newInvoice.items"
          class="item-body"
          tag="tbody"
          handle=".handle"
        >
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

      <div
        class="flex items-center justify-center w-full px-6 py-3 text-base border-b border-gray-200 border-solid cursor-pointer text-primary-400 hover:bg-gray-200"
        @click="addItem"
      >
        <shopping-cart-icon class="h-5 mr-2" />
        {{ $t('invoices.add_item') }}
      </div>

      <!-- Notes, Custom Fields & Total Section -->
      <div
        class="block my-10 invoice-foot lg:justify-between lg:flex lg:items-start"
      >
        <div class="w-full lg:w-1/2">
          <div class="mb-6">
            <sw-popup
              ref="notePopup"
              class="z-10 text-sm font-semibold leading-5 text-primary-400"
            >
              <div slot="activator" class="float-right mt-1">
                + {{ $t('general.insert_note') }}
              </div>
              <note-select-popup type="Invoice" @select="onSelectNote" />
            </sw-popup>
            <sw-input-group :label="$t('invoices.notes')">
              <base-custom-input
                v-model="newInvoice.notes"
                :fields="InvoiceFields"
              />
            </sw-input-group>
          </div>

          <div
            v-if="customFields.length > 0"
            class="grid gap-x-4 gap-y-2 md:gap-x-8 md:gap-y-4 grid-col-1 md:grid-cols-2"
          >
            <sw-input-group
              v-for="(field, index) in customFields"
              :label="field.label"
              :required="field.is_required ? true : false"
              :key="index"
            >
              <component
                :type="field.type.label"
                :field="field"
                :is-edit="isEdit"
                :is="field.type + 'Field'"
                :invalid-fields="invalidFields"
                @update="setCustomFieldValue"
              />
            </sw-input-group>
          </div>

          <sw-input-group
            :label="$t('invoices.invoice_template')"
            class="mt-6 mb-1"
            required
          >
            <sw-button
              type="button"
              class="flex justify-center w-full text-sm text-black lg:w-auto hover:bg-gray-400"
              variant="gray"
              @click="openTemplateModal"
            >
              <span class="flex text-black">
                {{ getTemplateName }}
                <pencil-icon class="h-5 ml-2 -mr-1" />
              </span>
            </sw-button>
          </sw-input-group>
        </div>

        <div
          class="px-5 py-4 mt-6 bg-white border border-gray-200 border-solid rounded invoice-total lg:mt-0"
        >
          <div class="flex items-center justify-between w-full">
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.sub_total') }}</label
            >
            <label
              class="flex items-center justify-center m-0 text-lg text-black uppercase"
            >
              <div v-html="$utils.formatMoney(subtotal, currency)" />
            </label>
          </div>
          <div
            v-for="tax in allTaxes"
            :key="tax.tax_type_id"
            class="flex items-center justify-between w-full"
          >
            <label
              class="m-0 text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ tax.name }} - {{ tax.percent }}%
            </label>
            <label
              class="flex items-center justify-center m-0 text-lg text-black uppercase"
              style="font-size: 18px"
            >
              <div v-html="$utils.formatMoney(tax.amount, currency)" />
            </label>
          </div>
          <div
            v-if="discountPerItem === 'NO' || discountPerItem === null"
            class="flex items-center justify-between w-full mt-2"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
              >{{ $t('invoices.discount') }}</label
            >
            <div class="flex" style="width: 105px" role="group">
              <sw-input
                v-model="discount"
                :invalid="$v.newInvoice.discount_val.$error"
                class="border-r-0 rounded-tr-sm rounded-br-sm"
                @input="$v.newInvoice.discount_val.$touch()"
              />
              <sw-dropdown position="bottom-end">
                <sw-button
                  slot="activator"
                  type="button"
                  data-toggle="dropdown"
                  size="discount"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="height: 43px"
                  variant="white"
                >
                  <span class="flex">
                    {{
                      newInvoice.discount_type == 'fixed'
                        ? currency.symbol
                        : '%'
                    }}
                    <chevron-down-icon class="h-5" />
                  </span>
                </sw-button>

                <sw-dropdown-item @click="selectFixed">
                  {{ $t('general.fixed') }}
                </sw-dropdown-item>

                <sw-dropdown-item @click="selectPercentage">
                  {{ $t('general.percentage') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </div>
          </div>

          <div v-if="taxPerItem ? 'NO' : null">
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

          <sw-popup
            v-if="taxPerItem === 'NO' || taxPerItem === null"
            ref="taxModal"
            class="my-3 text-sm font-semibold leading-5 text-primary-400"
          >
            <div slot="activator" class="float-right pt-2 pb-5">
              + {{ $t('invoices.add_tax') }}
            </div>
            <tax-select-popup :taxes="newInvoice.taxes" @select="onSelectTax" />
          </sw-popup>

          <div
            class="flex items-center justify-between w-full pt-2 mt-5 border-t border-gray-200 border-solid"
          >
            <label
              class="text-sm font-semibold leading-5 text-gray-500 uppercase"
            >
              {{ $t('invoices.total') }} {{ $t('invoices.amount') }}:
            </label>
            <label
              class="flex items-center justify-center text-lg uppercase text-primary-400"
            >
              <div v-html="$utils.formatMoney(total, currency)" />
            </label>
          </div>
        </div>
      </div>
    </form>
    <base-loader v-else />
  </base-page>
</template>

<script>
import draggable from 'vuedraggable'
import InvoiceItem from './Item'
import CustomerSelect from './CustomerSelect'
import InvoiceStub from '../../stub/invoice'
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import Guid from 'guid'
import TaxStub from '../../stub/tax'
import Tax from './InvoiceTax'
import { PlusSmIcon } from '@vue-hero-icons/outline'
import {
  ChevronDownIcon,
  PencilIcon,
  ShoppingCartIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
import invoice from '../../stub/invoice'

const {
  required,
  between,
  maxLength,
  numeric,
} = require('vuelidate/lib/validators')

export default {
  components: {
    InvoiceItem,
    CustomerSelect,
    Tax,
    draggable,
    PlusSmIcon,
    ChevronDownIcon,
    PencilIcon,
    ShoppingCartIcon,
    HashtagIcon,
  },
  mixins: [CustomFieldsMixin],

  data() {
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
        items: [
          {
            ...InvoiceStub,
            id: Guid.raw(),
            taxes: [{ ...TaxStub, id: Guid.raw() }],
          },
        ],
        taxes: [],
      },
      selectedCurrency: '',
      taxPerItem: null,
      discountPerItem: null,
      isLoadingInvoice: false,
      isLoadingData: true,
      isLoading: false,
      maxDiscount: 0,
      invoicePrefix: null,
      invoiceNumAttribute: null,
      InvoiceFields: [
        'customer',
        'customerCustom',
        'company',
        'invoice',
        'invoiceCustom',
      ],
      customerId: null,
    }
  },

  validations() {
    return {
      newInvoice: {
        invoice_date: {
          required,
        },
        due_date: {
          required,
        },
        discount_val: {
          between: between(0, this.subtotal),
        },
        reference_number: {
          maxLength: maxLength(255),
        },
      },
      selectedCustomer: {
        required,
      },
      invoiceNumAttribute: {
        required,
        numeric,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['itemDiscount']),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('notes', ['notes']),

    ...mapGetters('invoice', [
      'getTemplateName',
      'selectedCustomer',
      'selectedNote',
    ]),

    ...mapGetters('invoiceTemplate', ['getInvoiceTemplates']),

    currency() {
      return this.selectedCurrency
    },

    pageTitle() {
      if (this.isEdit) {
        return this.$t('invoices.edit_invoice')
      }
      return this.$t('invoices.new_invoice')
    },

    isEdit() {
      if (this.$route.name === 'invoices.edit') {
        return true
      }
      return false
    },

    subtotalWithDiscount() {
      return this.subtotal - this.newInvoice.discount_val
    },

    total() {
      return this.subtotalWithDiscount + this.totalTax
    },

    subtotal() {
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
          this.newInvoice.discount_val = Math.round(newValue * 100)
        }

        this.newInvoice.discount = newValue
      },
    },

    totalSimpleTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },

    totalCompoundTax() {
      return Math.round(
        window._.sumBy(this.newInvoice.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }

          return 0
        })
      )
    },

    totalTax() {
      if (this.taxPerItem === 'NO' || this.taxPerItem === null) {
        return this.totalSimpleTax + this.totalCompoundTax
      }

      return Math.round(
        window._.sumBy(this.newInvoice.items, function (tax) {
          return tax.tax
        })
      )
    },

    allTaxes() {
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
              name: tax.name,
            })
          }
        })
      })

      return taxes
    },

    invoiceDateError() {
      if (!this.$v.newInvoice.invoice_date.$error) {
        return ''
      }
      if (!this.$v.newInvoice.invoice_date.required) {
        return this.$t('validation.required')
      }
    },

    dueDateError() {
      if (!this.$v.newInvoice.due_date.$error) {
        return ''
      }
      if (!this.$v.newInvoice.due_date.required) {
        return this.$t('validation.required')
      }
    },

    invoiceNumError() {
      if (!this.$v.invoiceNumAttribute.$error) {
        return ''
      }

      if (!this.$v.invoiceNumAttribute.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.invoiceNumAttribute.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },

    referenceError() {
      if (!this.$v.newInvoice.reference_number.$error) {
        return ''
      }

      if (!this.$v.newInvoice.reference_number.maxLength) {
        return this.$tc('validation.ref_number_maxlength')
      }
    },
  },

  watch: {
    selectedCustomer(newVal) {
      if (newVal && newVal.currency) {
        this.selectedCurrency = newVal.currency
      } else {
        this.selectedCurrency = this.defaultCurrency
      }
    },

    selectedNote() {
      if (this.selectedNote) {
        this.newInvoice.notes = this.selectedNote
      }
    },

    subtotal(newValue) {
      if (this.newInvoice.discount_type === 'percentage') {
        this.newInvoice.discount_val =
          (this.newInvoice.discount * newValue) / 100
      }
    },
  },

  created() {
    this.loadData()
    this.fetchInitialData()
    window.hub.$on('newTax', this.onSelectTax)
    if (this.$route.query.customer) {
      this.customerId = parseInt(this.$route.query.customer)
    }
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('invoice', [
      'addInvoice',
      'fetchInvoice',
      'getInvoiceNumber',
      'selectCustomer',
      'updateInvoice',
      'resetSelectedNote',
      'setTemplate',
    ]),

    ...mapActions('invoiceTemplate', ['fetchInvoiceTemplates']),

    ...mapActions('company', ['fetchCompanySettings']),

    ...mapActions('item', ['fetchItems']),

    ...mapActions('taxType', ['fetchTaxTypes']),

    ...mapActions('customFields', ['fetchCustomFields']),

    ...mapActions('notification', ['showNotification']),

    selectFixed() {
      if (this.newInvoice.discount_type === 'fixed') {
        return
      }

      this.newInvoice.discount_val = Math.round(this.newInvoice.discount * 100)
      this.newInvoice.discount_type = 'fixed'
    },

    selectPercentage() {
      if (this.newInvoice.discount_type === 'percentage') {
        return
      }

      this.newInvoice.discount_val =
        (this.subtotal * this.newInvoice.discount) / 100

      this.newInvoice.discount_type = 'percentage'
    },

    updateTax(data) {
      Object.assign(this.newInvoice.taxes[data.index], { ...data.item })
    },

    async fetchInitialData() {
      this.isLoadingData = true

      if (!this.isEdit) {
        let response = await this.fetchCompanySettings([
          'discount_per_item',
          'tax_per_item',
        ])

        if (response.data) {
          this.discountPerItem = response.data.discount_per_item
          this.taxPerItem = response.data.tax_per_item
        }
      }

      Promise.all([
        this.fetchItems({
          filter: {},
          orderByField: '',
          orderBy: '',
        }),
        this.fetchInvoiceTemplates(),
        this.resetSelectedNote(),
        this.getInvoiceNumber(),
        this.fetchCompanySettings(['invoice_auto_generate']),
      ])
        .then(async ([res1, res2, res3, res4, res5]) => {
          if (
            !this.isEdit &&
            res5.data &&
            res5.data.invoice_auto_generate === 'YES'
          ) {
            if (res4.data) {
              this.invoiceNumAttribute = res4.data.nextNumber
              this.invoicePrefix = res4.data.prefix
            }
            this.setTemplate(this.getInvoiceTemplates[0].name)
          } else {
            this.invoicePrefix = res4.data.prefix
          }

          // this.discountPerItem = res5.data.discount_per_item
          // this.taxPerItem = res5.data.tax_per_item
          this.isLoadingData = false
        })
        .catch((error) => {
          console.log(error)
        })
    },

    async loadData() {
      if (this.isEdit) {
        this.isLoadingInvoice = true

        Promise.all([
          this.fetchInvoice(this.$route.params.id),
          this.fetchCustomFields({
            type: 'Invoice',
            limit: 'all',
          }),
          this.fetchTaxTypes({ limit: 'all' }),
        ])
          .then(async ([res1, res2]) => {
            if (res1.data) {
              this.customerId = res1.data.invoice.user_id
              this.newInvoice = res1.data.invoice
              this.formData = { ...this.formData, ...res1.data.invoice }

              this.newInvoice.invoice_date = moment(
                res1.data.invoice.invoice_date,
                'YYYY-MM-DD'
              ).toString()

              this.newInvoice.due_date = moment(
                res1.data.invoice.due_date,
                'YYYY-MM-DD'
              ).toString()

              this.discountPerItem = res1.data.invoice.discount_per_item
              this.selectedCurrency = this.defaultCurrency
              this.invoiceNumAttribute = res1.data.nextInvoiceNumber
              this.invoicePrefix = res1.data.invoicePrefix
              this.taxPerItem = res1.data.invoice.tax_per_item
              let fields = res1.data.invoice.fields

              if (res2.data) {
                let customFields = res2.data.customFields.data
                await this.setEditCustomFields(fields, customFields)
              }
            }
            this.isLoadingInvoice = false
          })
          .catch((error) => {
            console.log(error)
          })

        return true
      }

      this.isLoadingInvoice = true
      await this.setInitialCustomFields('Invoice')
      await this.fetchTaxTypes({ limit: 'all' })
      this.selectedCurrency = this.defaultCurrency
      this.newInvoice.invoice_date = moment().toString()
      this.newInvoice.due_date = moment().add(7, 'days').toString()

      this.isLoadingInvoice = false
    },

    openTemplateModal() {
      this.openModal({
        title: this.$t('general.choose_template'),
        componentName: 'InvoiceTemplate',
        data: this.getInvoiceTemplates,
      })
    },

    addItem() {
      this.newInvoice.items.push({
        ...InvoiceStub,
        id: Guid.raw(),
        taxes: [{ ...TaxStub, id: Guid.raw() }],
      })
    },

    removeItem(index) {
      this.newInvoice.items.splice(index, 1)
    },

    updateItem(data) {
      Object.assign(this.newInvoice.items[data.index], { ...data.item })
    },

    async submitForm() {
      // return
      let validate = await this.touchCustomField()

      if (!this.checkValid() || validate.error) {
        return false
      }

      this.isLoading = true
      this.newInvoice.invoice_number =
        this.invoicePrefix + '-' + this.invoiceNumAttribute

      let data = {
        ...this.formData,
        ...this.newInvoice,
        sub_total: this.subtotal,
        total: this.total,
        tax: this.totalTax,
        user_id: null,
        template_name: this.getTemplateName,
      }

      if (this.selectedCustomer != null) {
        data.user_id = this.selectedCustomer.id
      }

      if (this.$route.name === 'invoices.edit') {
        this.submitUpdate(data)
        return
      }

      this.submitCreate(data)
    },

    submitCreate(data) {
      this.addInvoice(data)
        .then((res) => {
          if (res.data) {
            this.$router.push(`/admin/invoices/${res.data.invoice.id}/view`)
            this.showNotification({
              type: 'success',
              message: this.$t('invoices.created_message'),
            })
          }

          this.isLoading = false
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    submitUpdate(data) {
      this.updateInvoice(data)
        .then((res) => {
          this.isLoading = false
          if (res.data.success) {
            this.$router.push(`/admin/invoices/${res.data.invoice.id}/view`)
            this.showNotification({
              type: 'success',
              message: this.$t('invoices.updated_message'),
            })
          }

          if (res.data.error === 'invalid_due_amount') {
            this.showNotification({
              type: 'error',
              message: this.$t('invoices.invalid_due_amount_message'),
            })
          }
        })
        .catch((err) => {
          this.isLoading = false
        })
    },

    checkItemsData(index, isValid) {
      this.newInvoice.items[index].valid = isValid
    },

    onSelectTax(selectedTax) {
      let amount = 0

      if (selectedTax.compound_tax && this.subtotalWithDiscount) {
        amount = Math.round(
          ((this.subtotalWithDiscount + this.totalSimpleTax) *
            selectedTax.percent) /
            100
        )
      } else if (this.subtotalWithDiscount && selectedTax.percent) {
        amount = Math.round(
          (this.subtotalWithDiscount * selectedTax.percent) / 100
        )
      }

      this.newInvoice.taxes.push({
        ...TaxStub,
        id: Guid.raw(),
        name: selectedTax.name,
        percent: selectedTax.percent,
        compound_tax: selectedTax.compound_tax,
        tax_type_id: selectedTax.id,
        amount,
      })

      if (this.$refs) {
        this.$refs.taxModal.close()
      }
    },

    removeInvoiceTax(index) {
      this.newInvoice.taxes.splice(index, 1)
    },

    checkValid() {
      this.$v.newInvoice.$touch()
      this.$v.selectedCustomer.$touch()
      this.$v.invoiceNumAttribute.$touch()

      window.hub.$emit('checkItems')
      let isValid = true
      this.newInvoice.items.forEach((item) => {
        if (!item.valid) {
          isValid = false
        }
      })
      if (
        !this.$v.selectedCustomer.$invalid &&
        !this.$v.invoiceNumAttribute.$invalid &&
        this.$v.newInvoice.$invalid === false &&
        isValid === true
      ) {
        return true
      }
      return false
    },
    onSelectNote(data) {
      this.newInvoice.notes = '' + data.notes
      this.$refs.notePopup.close()
    },
  },
}
</script>

<style lang="scss">
.invoice-create-page {
  .invoice-foot {
    .invoice-total {
      min-width: 390px;
    }
  }
  @media (max-width: 480px) {
    .invoice-foot {
      .invoice-total {
        min-width: 384px;
      }
    }
  }
}
</style>

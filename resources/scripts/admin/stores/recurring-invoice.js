import { defineStore } from 'pinia'
import axios from 'axios'
import recurringInvoiceStub from '@/scripts/admin/stub/recurring-invoice'
import recurringInvoiceItemStub from '@/scripts/admin/stub/recurring-invoice-item'
import TaxStub from '../stub/tax'
import { useRoute } from 'vue-router'
import { useCompanyStore } from './company'
import { useItemStore } from './item'
import { useTaxTypeStore } from './tax-type'
import { useCustomerStore } from './customer'
import Guid from 'guid'
import { handleError } from '@/scripts/helpers/error-handling'
import moment from 'moment'
import _ from 'lodash'
import { useInvoiceStore } from './invoice'
import { useNotificationStore } from '@/scripts/stores/notification'

export const useRecurringInvoiceStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'recurring-invoice',

    state: () => ({
      templates: [],
      recurringInvoices: [],
      selectedRecurringInvoices: [],
      totalRecurringInvoices: 0,
      isFetchingInitialSettings: false,
      isFetchingViewData: false,
      showExchangeRate: false,
      selectAllField: false,
      newRecurringInvoice: {
        ...recurringInvoiceStub(),
      },

      frequencies: [
        { label: 'Every Minute', value: '* * * * *' },
        { label: 'Every 30 Minute', value: '*/30 * * * *' },
        { label: 'Every Hour', value: '0 * * * *' },
        { label: 'Every 2 Hour', value: '0 */2 * * *' },
        { label: 'Every day at midnight ', value: '0 0 * * *' },
        { label: 'Every Week', value: '0 0 * * 0' },
        { label: 'Every 15 days at midnight', value: '0 5 */15 * *' },
        { label: 'On the first day of every month at 00:00', value: '0 0 1 * *' },
        { label: 'Every 6 Month', value: '0 0 1 */6 *' },
        { label: 'Every year on the first day of january at 00:00', value: '0 0 1 1 *' },
        { label: 'Custom', value: 'CUSTOM' },
      ],
    }),

    getters: {
      getSubTotal() {
        return (
          this.newRecurringInvoice?.items.reduce(function (a, b) {
            return a + b['total']
          }, 0) || 0
        )
      },

      getTotalSimpleTax() {
        return _.sumBy(this.newRecurringInvoice.taxes, function (tax) {
          if (!tax.compound_tax) {
            return tax.amount
          }
          return 0
        })
      },

      getTotalCompoundTax() {
        return _.sumBy(this.newRecurringInvoice.taxes, function (tax) {
          if (tax.compound_tax) {
            return tax.amount
          }
          return 0
        })
      },

      getTotalTax() {
        if (
          this.newRecurringInvoice.tax_per_item === 'NO' ||
          this.newRecurringInvoice.tax_per_item === null
        ) {
          return this.getTotalSimpleTax + this.getTotalCompoundTax
        }
        return _.sumBy(this.newRecurringInvoice.items, function (tax) {
          return tax.tax
        })
      },

      getSubtotalWithDiscount() {
        return this.getSubTotal - this.newRecurringInvoice.discount_val
      },

      getTotal() {
        return this.getSubtotalWithDiscount + this.getTotalTax
      },
    },

    actions: {
      resetCurrentRecurringInvoice() {
        this.newRecurringInvoice = {
          ...recurringInvoiceStub(),
        }
      },

      deselectItem(index) {
        this.newRecurringInvoice.items[index] = {
          ...recurringInvoiceItemStub,
          id: Guid.raw(),
          taxes: [{ ...TaxStub, id: Guid.raw() }],
        }
      },

      addRecurringInvoice(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/recurring-invoices', data)
            .then((response) => {
              this.recurringInvoices = [
                ...this.recurringInvoices,
                response.data.recurringInvoice,
              ]
              const notificationStore = useNotificationStore()

              notificationStore.showNotification({
                type: 'success',
                message: global.t('recurring_invoices.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchRecurringInvoice(id) {
        return new Promise((resolve, reject) => {
          this.isFetchingViewData = true
          axios
            .get(`/api/v1/recurring-invoices/${id}`)
            .then((response) => {
              Object.assign(this.newRecurringInvoice, response.data.data)
              this.newRecurringInvoice.invoices =
                response.data.data.invoices || []
              this.setSelectedFrequency()
              this.isFetchingViewData = false
              resolve(response)
            })
            .catch((err) => {
              this.isFetchingViewData = false
              handleError(err)
              reject(err)
            })
        })
      },

      updateRecurringInvoice(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/recurring-invoices/${data.id}`, data)
            .then((response) => {
              resolve(response)

              const notificationStore = useNotificationStore()

              notificationStore.showNotification({
                type: 'success',
                message: global.t('recurring_invoices.updated_message'),
              })

              let pos = this.recurringInvoices.findIndex(
                (invoice) => invoice.id === response.data.data.id
              )

              this.recurringInvoices[pos] = response.data.data
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      selectCustomer(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/customers/${id}`)
            .then((response) => {
              this.newRecurringInvoice.customer = response.data.data
              this.newRecurringInvoice.customer_id = response.data.data.id
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      searchRecurringInvoice(data) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/recurring-invoices?${data}`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchRecurringInvoices(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/recurring-invoices`, { params })
            .then((response) => {
              this.recurringInvoices = response.data.data
              this.totalRecurringInvoices =
                response.data.meta.recurring_invoice_total_count

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteRecurringInvoice(id) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/recurring-invoices/delete`, id)
            .then((response) => {
              let index = this.recurringInvoices.findIndex(
                (invoice) => invoice.id === id
              )
              this.recurringInvoices.splice(index, 1)
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteMultipleRecurringInvoices(id) {
        return new Promise((resolve, reject) => {
          let ids = this.selectedRecurringInvoices
          if (id) {
            ids = [id]
          }
          axios
            .post(`/api/v1/recurring-invoices/delete`, {
              ids: ids,
            })
            .then((response) => {
              this.selectedRecurringInvoices.forEach((invoice) => {
                let index = this.recurringInvoices.findIndex(
                  (_inv) => _inv.id === invoice.id
                )
                this.recurringInvoices.splice(index, 1)
              })
              this.selectedRecurringInvoices = []
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      resetSelectedCustomer() {
        this.newRecurringInvoice.customer = null
        this.newRecurringInvoice.customer_id = ''
      },

      selectRecurringInvoice(data) {
        this.selectedRecurringInvoices = data
        if (
          this.selectedRecurringInvoices.length ===
          this.recurringInvoices.length
        ) {
          this.selectAllField = true
        } else {
          this.selectAllField = false
        }
      },

      selectAllRecurringInvoices() {
        if (
          this.selectedRecurringInvoices.length ===
          this.recurringInvoices.length
        ) {
          this.selectedRecurringInvoices = []
          this.selectAllField = false
        } else {
          let allInvoiceIds = this.recurringInvoices.map(
            (invoice) => invoice.id
          )
          this.selectedRecurringInvoices = allInvoiceIds
          this.selectAllField = true
        }
      },

      addItem() {
        this.newRecurringInvoice.items.push({
          ...recurringInvoiceItemStub,
          id: Guid.raw(),
          taxes: [{ ...TaxStub, id: Guid.raw() }],
        })
      },

      removeItem(index) {
        this.newRecurringInvoice.items.splice(index, 1)
      },

      updateItem(data) {
        Object.assign(this.newRecurringInvoice.items[data.index], { ...data })
      },

      async fetchRecurringInvoiceInitialSettings(isEdit) {
        const companyStore = useCompanyStore()
        const customerStore = useCustomerStore()
        const itemStore = useItemStore()
        const invoiceStore = useInvoiceStore()
        const taxTypeStore = useTaxTypeStore()
        const route = useRoute()

        this.isFetchingInitialSettings = true
        this.newRecurringInvoice.currency = companyStore.selectedCompanyCurrency

        if (route.query.customer) {
          let response = await customerStore.fetchCustomer(route.query.customer)
          this.newRecurringInvoice.customer = response.data.data
          this.selectCustomer(response.data.data.id)
        }

        let editActions = []

        // on create
        if (!isEdit) {
          this.newRecurringInvoice.tax_per_item =
            companyStore.selectedCompanySettings.tax_per_item
          this.newRecurringInvoice.discount_per_item =
            companyStore.selectedCompanySettings.discount_per_item
          this.newRecurringInvoice.sales_tax_type = companyStore.selectedCompanySettings.sales_tax_type
          this.newRecurringInvoice.sales_tax_address_type = companyStore.selectedCompanySettings.sales_tax_address_type
          this.newRecurringInvoice.starts_at = moment().format('YYYY-MM-DD')
          this.newRecurringInvoice.next_invoice_date = moment()
            .add(7, 'days')
            .format('YYYY-MM-DD')
        } else {
          editActions = [this.fetchRecurringInvoice(route.params.id)]
        }

        Promise.all([
          itemStore.fetchItems({
            filter: {},
            orderByField: '',
            orderBy: '',
          }),
          this.resetSelectedNote(),
          invoiceStore.fetchInvoiceTemplates(),
          taxTypeStore.fetchTaxTypes({ limit: 'all' }),
          ...editActions,
        ])
          .then(async ([res1, res2, res3, res4, res5]) => {
            if (res3.data) {
              this.templates = invoiceStore.templates
            }

            if (!isEdit) {
              this.setTemplate(this.templates[0].name)
            }

            if (isEdit && res5?.data) {
              let data = {
                ...res5.data.data,
              }

              this.setTemplate(res5?.data?.data?.template_name)
            }
            if (isEdit) {
              this.addSalesTaxUs()
            }
            this.isFetchingInitialSettings = false
          })
          .catch((err) => {
            console.log(err);
            handleError(err)
          })
      },

      addSalesTaxUs() {
        const taxTypeStore = useTaxTypeStore()
        let salesTax = { ...TaxStub }
        let found = this.newRecurringInvoice.taxes.find((_t) => _t.name === 'Sales Tax' && _t.type === 'MODULE')
        if (found) {
          for (const key in found) {
            if (Object.prototype.hasOwnProperty.call(salesTax, key)) {
              salesTax[key] = found[key]
            }
          }
          salesTax.id = found.tax_type_id
          taxTypeStore.taxTypes.push(salesTax)
        }
      },

      setTemplate(data) {
        this.newRecurringInvoice.template_name = data
      },

      setSelectedFrequency() {
        let data = this.frequencies.find(
          (frequency) => {
            return frequency.value === this.newRecurringInvoice.frequency
          }
        )
        data ? this.newRecurringInvoice.selectedFrequency = data
          : this.newRecurringInvoice.selectedFrequency = { label: 'Custom', value: 'CUSTOM' }

      },

      resetSelectedNote() {
        this.newRecurringInvoice.selectedNote = null
      },

      fetchRecurringInvoiceFrequencyDate(params) {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/recurring-invoice-frequency', { params })
            .then((response) => {
              this.newRecurringInvoice.next_invoice_at =
                response.data.next_invoice_at

              resolve(response)
            })
            .catch((err) => {
              const notificationStore = useNotificationStore()

              notificationStore.showNotification({
                type: 'error',
                message: global.t('errors.enter_valid_cron_format'),
              })
              reject(err)
            })
        })
      },
    },
  })()
}

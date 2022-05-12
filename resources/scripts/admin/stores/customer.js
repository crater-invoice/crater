import axios from 'axios'
import { defineStore } from 'pinia'
import { useRoute } from 'vue-router'
import { handleError } from '@/scripts/helpers/error-handling'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import addressStub from '@/scripts/admin/stub/address.js'
import customerStub from '@/scripts/admin/stub/customer'

export const useCustomerStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'customer',
    state: () => ({
      customers: [],
      totalCustomers: 0,
      selectAllField: false,
      selectedCustomers: [],
      selectedViewCustomer: {},
      isFetchingInitialSettings: false,
      isFetchingViewData: false,
      currentCustomer: {
        ...customerStub(),
      },
      editCustomer: null
    }),

    getters: {
      isEdit: (state) => (state.currentCustomer.id ? true : false),
    },

    actions: {
      resetCurrentCustomer() {
        this.currentCustomer = {
          ...customerStub(),
        }
      },

      copyAddress() {
        this.currentCustomer.shipping = {
          ...this.currentCustomer.billing,
          type: 'shipping',
        }
      },

      fetchCustomerInitialSettings(isEdit) {
        const route = useRoute()
        const globalStore = useGlobalStore()
        const companyStore = useCompanyStore()

        this.isFetchingInitialSettings = true
        let editActions = []
        if (isEdit) {
          editActions = [this.fetchCustomer(route.params.id)]
        } else {
          this.currentCustomer.currency_id =
            companyStore.selectedCompanyCurrency.id
        }

        Promise.all([
          globalStore.fetchCurrencies(),
          globalStore.fetchCountries(),
          ...editActions,
        ])
          .then(async ([res1, res2, res3]) => {
            this.isFetchingInitialSettings = false
          })
          .catch((error) => {
            handleError(error)
          })
      },

      fetchCustomers(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/customers`, { params })
            .then((response) => {
              this.customers = response.data.data
              this.totalCustomers = response.data.meta.customer_total_count
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchViewCustomer(params) {
        return new Promise((resolve, reject) => {
          this.isFetchingViewData = true
          axios
            .get(`/api/v1/customers/${params.id}/stats`, { params })

            .then((response) => {
              this.selectedViewCustomer = {}
              Object.assign(this.selectedViewCustomer, response.data.data)
              this.setAddressStub(response.data.data)
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

      fetchCustomer(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/customers/${id}`)
            .then((response) => {
              Object.assign(this.currentCustomer, response.data.data)

              this.setAddressStub(response.data.data)
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addCustomer(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/customers', data)
            .then((response) => {
              this.customers.push(response.data.data)

              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('customers.created_message'),
              })
              resolve(response)
            })

            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateCustomer(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/customers/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.customers.findIndex(
                  (customer) => customer.id === response.data.data.id
                )
                this.customers[pos] = data
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('customers.updated_message'),
                })
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteCustomer(id) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/customers/delete`, id)
            .then((response) => {
              let index = this.customers.findIndex(
                (customer) => customer.id === id
              )
              this.customers.splice(index, 1)
              notificationStore.showNotification({
                type: 'success',
                message: global.tc('customers.deleted_message', 1),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteMultipleCustomers() {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/customers/delete`, { ids: this.selectedCustomers })
            .then((response) => {
              this.selectedCustomers.forEach((customer) => {
                let index = this.customers.findIndex(
                  (_customer) => _customer.id === customer.id
                )
                this.customers.splice(index, 1)
              })

              notificationStore.showNotification({
                type: 'success',
                message: global.tc('customers.deleted_message', 2),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      setSelectAllState(data) {
        this.selectAllField = data
      },

      selectCustomer(data) {
        this.selectedCustomers = data
        if (this.selectedCustomers.length === this.customers.length) {
          this.selectAllField = true
        } else {
          this.selectAllField = false
        }
      },

      selectAllCustomers() {
        if (this.selectedCustomers.length === this.customers.length) {
          this.selectedCustomers = []
          this.selectAllField = false
        } else {
          let allCustomerIds = this.customers.map((customer) => customer.id)
          this.selectedCustomers = allCustomerIds
          this.selectAllField = true
        }
      },

      setAddressStub(data) {
        if (!data.billing) this.currentCustomer.billing = { ...addressStub }
        if (!data.shipping) this.currentCustomer.shipping = { ...addressStub }
      },
    },
  })()
}

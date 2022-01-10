import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useTaxTypeStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'taxType',

    state: () => ({
      taxTypes: [],
      currentTaxType: {
        id: null,
        name: '',
        percent: 0,
        description: '',
        compound_tax: false,
        collective_tax: 0,
      },
    }),

    getters: {
      isEdit: (state) => (state.currentTaxType.id ? true : false),
    },

    actions: {
      resetCurrentTaxType() {
        this.currentTaxType = {
          id: null,
          name: '',
          percent: 0,
          description: '',
          compound_tax: false,
          collective_tax: 0,
        }
      },

      fetchTaxTypes(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/tax-types`, { params })
            .then((response) => {
              this.taxTypes = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchTaxType(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/tax-types/${id}`)
            .then((response) => {
              this.currentTaxType = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addTaxType(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/tax-types', data)
            .then((response) => {
              this.taxTypes.push(response.data.data)
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.tax_types.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateTaxType(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/tax-types/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.taxTypes.findIndex(
                  (taxTypes) => taxTypes.id === response.data.data.id
                )
                this.taxTypes[pos] = data.taxTypes
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.tax_types.updated_message'),
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

      fetchSalesTax(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/m/sales-tax-us/current-tax', data)
            .then((response) => {
              if (response.data) {
                let pos = this.taxTypes.findIndex(
                  (_t) => _t.name === 'SalesTaxUs'
                )
                pos > -1 ? this.taxTypes.splice(pos, 1) : ''
                this.taxTypes.push({ ...response.data.data, tax_type_id: response.data.data.id })
              }

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteTaxType(id) {
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/tax-types/${id}`)
            .then((response) => {
              if (response.data.success) {
                let index = this.taxTypes.findIndex(
                  (taxType) => taxType.id === id
                )
                this.taxTypes.splice(index, 1)
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.tax_types.deleted_message'),
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
    },
  })()
}

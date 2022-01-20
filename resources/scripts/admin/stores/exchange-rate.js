import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useExchangeRateStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n
  const notificationStore = useNotificationStore()

  return defineStoreFunc({
    id: 'exchange-rate',

    state: () => ({
      supportedCurrencies: [],
      drivers: [],
      activeUsedCurrencies: [],
      providers: [],
      currencies: null,
      currentExchangeRate: {
        id: null,
        driver: '',
        key: '',
        active: true,
        currencies: [],
      },
      currencyConverter: {
        type: '',
        url: '',
      },
      bulkCurrencies: [],
    }),
    getters: {
      isEdit: (state) => (state.currentExchangeRate.id ? true : false),
    },

    actions: {
      fetchProviders(params) {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/exchange-rate-providers', { params })
            .then((response) => {
              this.providers = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchDefaultProviders() {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/config?key=exchange_rate_drivers`)
            .then((response) => {
              this.drivers = response.data.exchange_rate_drivers

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchProvider(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/exchange-rate-providers/${id}`)
            .then((response) => {
              this.currentExchangeRate = response.data.data
              this.currencyConverter = response.data.data.driver_config
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addProvider(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/exchange-rate-providers', data)
            .then((response) => {
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.exchange_rate.created_message'),
              })

              resolve(response)
            })
            .catch((err) => {
              handleError(err)

              reject(err)
            })
        })
      },

      updateProvider(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/exchange-rate-providers/${data.id}`, data)
            .then((response) => {
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.exchange_rate.updated_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteExchangeRate(id) {
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/exchange-rate-providers/${id}`)
            .then((response) => {
              let index = this.drivers.findIndex((driver) => driver.id === id)
              this.drivers.splice(index, 1)
              if (response.data.success) {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.exchange_rate.deleted_message'),
                })
              } else {
                notificationStore.showNotification({
                  type: 'error',
                  message: global.t('settings.exchange_rate.error'),
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
      fetchCurrencies(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/supported-currencies`, { params })
            .then((response) => {
              this.supportedCurrencies = response.data.supportedCurrencies

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },
      fetchActiveCurrency(params) {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/used-currencies', { params })
            .then((response) => {
              this.activeUsedCurrencies = response.data.activeUsedCurrencies

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },
      fetchBulkCurrencies() {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/currencies/used')
            .then((response) => {
              this.bulkCurrencies = response.data.currencies.map((_m) => {
                _m.exchange_rate = null
                return _m
              })

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },
      updateBulkExchangeRate(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/currencies/bulk-update-exchange-rate', data)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },
      getCurrentExchangeRate(currencyId) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/currencies/${currencyId}/exchange-rate`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              reject(err)
            })
        })
      },
      getCurrencyConverterServers() {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/config?key=currency_converter_servers')
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },
      checkForActiveProvider(currency_id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/currencies/${currency_id}/active-provider`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              reject(err)
            })
        })
      },
    },
  })()
}

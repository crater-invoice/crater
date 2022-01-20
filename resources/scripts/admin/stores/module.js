import axios from 'axios'
import { defineStore } from 'pinia'
import { handleError } from '@/scripts/helpers/error-handling'
import { useNotificationStore } from '@/scripts/stores/notification'

export const useModuleStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'modules',

    state: () => ({
      currentModule: {},
      modules: [],
      apiToken: null,
      currentUser: {
        api_token: null,
      },
      enableModules: []
    }),

    getters: {
      salesTaxUSEnabled: (state) => (state.enableModules.includes('SalesTaxUS')),
    },

    actions: {
      fetchModules(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/modules`)
            .then((response) => {
              this.modules = response.data.data

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchModule(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/modules/${id}`)
            .then((response) => {
              if (response.data.error === 'invalid_token') {
                this.currentModule = {},
                this.modules = [],
                this.apiToken = null,
                this.currentUser.api_token = null,
                window.router.push('/admin/modules')
              } else { 
                this.currentModule = response.data
              }

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      checkApiToken(token) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/modules/check?api_token=${token}`)
            .then((response) => {
              const notificationStore = useNotificationStore()
              if (response.data.error === 'invalid_token') {
                notificationStore.showNotification({
                  type: 'error',
                  message: global.t('modules.invalid_api_token'),
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

      disableModule(module) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/modules/${module}/disable`)
            .then((response) => {
              const notificationStore = useNotificationStore()
              if (response.data.success) {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('modules.module_disabled'),
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

      enableModule(module) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/modules/${module}/enable`)
            .then((response) => {
              const notificationStore = useNotificationStore()
              if (response.data.success) {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('modules.module_enabled'),
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

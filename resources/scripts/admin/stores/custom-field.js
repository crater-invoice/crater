import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import customFieldStub from '@/scripts/admin/stub/custom-field'
import utilities from '@/scripts/helpers/utilities'
import { util } from 'prettier'
import { handleError } from '@/scripts/helpers/error-handling'

export const useCustomFieldStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'custom-field',

    state: () => ({
      customFields: [],
      isRequestOngoing: false,

      currentCustomField: {
        ...customFieldStub,
      },
    }),

    getters: {
      isEdit() {
        return this.currentCustomField.id ? true : false
      },
    },

    actions: {
      resetCustomFields() {
        this.customFields = []
      },

      resetCurrentCustomField() {
        this.currentCustomField = {
          ...customFieldStub,
        }
      },

      fetchCustomFields(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/custom-fields`, { params })
            .then((response) => {
              this.customFields = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchNoteCustomFields(params) {
        return new Promise((resolve, reject) => {
          if (this.isRequestOngoing) {
            resolve({ requestOnGoing: true })
            return true
          }

          this.isRequestOngoing = true

          axios
            .get(`/api/v1/custom-fields`, { params })
            .then((response) => {
              this.customFields = response.data.data
              this.isRequestOngoing = false
              resolve(response)
            })
            .catch((err) => {
              this.isRequestOngoing = false
              handleError(err)
              reject(err)
            })
        })
      },

      fetchCustomField(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/custom-fields/${id}`)
            .then((response) => {
              this.currentCustomField = response.data.data

              if (
                this.currentCustomField.options &&
                this.currentCustomField.options.length
              ) {
                this.currentCustomField.options =
                  this.currentCustomField.options.map((option) => {
                    return (option = { name: option })
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

      addCustomField(params) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/custom-fields`, params)
            .then((response) => {
              let data = {
                ...response.data.data,
              }

              if (data.options) {
                data.options = data.options.map((option) => {
                  return { name: option ? option : '' }
                })
              }

              this.customFields.push(data)

              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.custom_fields.added_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateCustomField(params) {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/custom-fields/${params.id}`, params)
            .then((response) => {
              let data = {
                ...response.data.data,
              }

              if (data.options) {
                data.options = data.options.map((option) => {
                  return { name: option ? option : '' }
                })
              }

              let pos = this.customFields.findIndex((_f) => _f.id === data.id)

              if (this.customFields[pos]) {
                this.customFields[pos] = data
              }

              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.custom_fields.updated_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteCustomFields(id) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/custom-fields/${id}`)
            .then((response) => {
              let index = this.customFields.findIndex(
                (field) => field.id === id
              )

              this.customFields.splice(index, 1)

              if (response.data.error) {
                notificationStore.showNotification({
                  type: 'error',
                  message: global.t('settings.custom_fields.already_in_use'),
                })
              } else {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.custom_fields.deleted_message'),
                })
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              // notificationStore.showNotification({
              //   type: 'error',
              //   message: global.t('settings.custom_fields.already_in_use'),
              // })
              reject(err)
            })
        })
      },
    },
  })()
}

import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useBackupStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'backup',

    state: () => ({
      backups: [],
      currentBackupData: {
        option: 'full',
        selected_disk: null,
      },
    }),

    actions: {
      fetchBackups(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/backups`, { params })
            .then((response) => {
              this.backups = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      createBackup(data) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/backups`, data)
            .then((response) => {
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.backup.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      removeBackup(params) {
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/backups/${params.disk}`, { params })
            .then((response) => {
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.backup.deleted_message'),
              })
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

import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useDiskStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'disk',

    state: () => ({
      disks: [],
      diskDrivers: [],
      diskConfigData: null,
      selected_driver: 'local',

      doSpaceDiskConfig: {
        name: '',
        selected_driver: 'doSpaces',
        key: '',
        secret: '',
        region: '',
        bucket: '',
        endpoint: '',
        root: '',
      },

      dropBoxDiskConfig: {
        name: '',
        selected_driver: 'dropbox',
        token: '',
        key: '',
        secret: '',
        app: '',
      },

      localDiskConfig: {
        name: '',
        selected_driver: 'local',
        root: '',
      },

      s3DiskConfigData: {
        name: '',
        selected_driver: 's3',
        key: '',
        secret: '',
        region: '',
        bucket: '',
        root: '',
      },
    }),

    getters: {
      getDiskDrivers: (state) => state.diskDrivers,
    },

    actions: {
      fetchDiskEnv(data) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/disks/${data.disk}`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchDisks(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/disks`, { params })
            .then((response) => {
              this.disks = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchDiskDrivers() {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/disk/drivers`)
            .then((response) => {
              this.diskConfigData = response.data
              this.diskDrivers = response.data.drivers
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteFileDisk(id) {
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/disks/${id}`)
            .then((response) => {
              if (response.data.success) {
                let index = this.disks.findIndex(
                  (category) => category.id === id
                )
                this.disks.splice(index, 1)
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.disk.deleted_message'),
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

      updateDisk(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/disks/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.disks.findIndex(
                  (disk) => disk.id === response.data.data
                )
                this.disks[pos] = data.disks
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.disk.success_set_default_disk'),
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

      createDisk(data) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/disks`, data)
            .then((response) => {
              if (response.data) {
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.disk.success_create'),
                })
              }
              this.disks.push(response.data)
              resolve(response)
            })
            .catch((err) => {
              /*   notificationStore.showNotification({
                type: 'error',
                message: global.t('settings.disk.invalid_disk_credentials'),
              }) */
              handleError(err)
              reject(err)
            })
        })
      },
    },
  })()
}

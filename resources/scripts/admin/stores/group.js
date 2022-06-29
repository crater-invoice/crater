import axios from 'axios'
import {defineStore} from 'pinia'
import {useNotificationStore} from '@/scripts/stores/notification'
import {handleError} from '@/scripts/helpers/error-handling'

export const useGroupStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'item',
    state: () => ({
      groups: [],
      totalGroups: 0,
      selectAllField: false,
      selectedGroups: [],
      currentGroup: {
        name: '',
      },
    }),
    getters: {
    },
    actions: {
      resetCurrentGroup() {
        this.currentGroup = {
          name: '',
        }
      },
      fetchGroups(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/groups`, { params })
            .then((response) => {
              this.groups = response.data.data
              this.totalGroups = response.data.meta.group_total_count

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchGroup(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/groups/${id}`)
            .then((response) => {
              if (response.data) {
                Object.assign(this.currentGroup, response.data.data)
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addGroup(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/groups', data)
            .then((response) => {
              const notificationStore = useNotificationStore()

              this.groups.push(response.data.data)

              notificationStore.showNotification({
                type: 'success',
                message: global.t('groups.created_message'),
              })

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateGroup(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/groups/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                const notificationStore = useNotificationStore()

                let pos = this.groups.findIndex(
                  (group) => group.id === response.data.data.id
                )

                this.group[pos] = data.group

                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('groups.updated_message'),
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

      deleteGroup(id) {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/groups/delete`, id)
            .then((response) => {
              let index = this.groups.findIndex((group) => group.id === id)
              this.groups.splice(index, 1)

              notificationStore.showNotification({
                type: 'success',
                message: global.tc('groups.deleted_message', 1),
              })

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteMultipleGroups() {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/groups/delete`, { ids: this.selectedGroups })
            .then((response) => {
              this.selectedGroups.forEach((group) => {
                let index = this.groups.findIndex(
                  (_group) => _group.id === group.id
                )
                this.groups.splice(index, 1)
              })

              notificationStore.showNotification({
                type: 'success',
                message: global.tc('groups.deleted_message', 2),
              })

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      selectGroup(data) {
        this.selectedGroups = data
        this.selectAllField = this.selectedGroups.length === this.groups.length;
      },

      selectAllGroups(data) {
        if (this.selectedGroups.length === this.groups.length) {
          this.selectedGroups = []
          this.selectAllField = false
        } else {
          this.selectedGroups = this.groups.map((group) => group.id)
          this.selectAllField = true
        }
      },
    },
  })()
}

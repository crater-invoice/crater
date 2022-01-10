import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import _ from 'lodash'
import { handleError } from '@/scripts/helpers/error-handling'

export const useRoleStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'role',
    state: () => ({
      roles: [],
      allAbilities: [],
      selectedRoles: [],
      currentRole: {
        id: null,
        name: '',
        abilities: [],
      },
    }),

    getters: {
      isEdit: (state) => (state.currentRole.id ? true : false),
      abilitiesList: (state) => {
        let abilities = state.allAbilities.map((a) => ({
          modelName: a.model
            ? a.model.substring(a.model.lastIndexOf('\\') + 1)
            : 'Common',
          disabled: false,
          ...a,
        }))
        return _.groupBy(abilities, 'modelName')
      },
    },

    actions: {
      fetchRoles(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/roles`, { params })
            .then((response) => {
              this.roles = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchRole(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/roles/${id}`)
            .then((response) => {
              this.currentRole.name = response.data.data.name
              this.currentRole.id = response.data.data.id

              response.data.data.abilities.forEach((_ra) => {
                for (const property in this.abilitiesList) {
                  this.abilitiesList[property].forEach((_p) => {
                    if (_p.ability === _ra.name) {
                      this.currentRole.abilities.push(_p)
                    }
                  })
                }
              })

              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addRole(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/roles', data)
            .then((response) => {
              this.roles.push(response.data.role)
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.roles.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateRole(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/roles/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.roles.findIndex(
                  (role) => role.id === response.data.data.id
                )
                this.roles[pos] = data.role
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.roles.updated_message'),
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

      fetchAbilities(params) {
        return new Promise((resolve, reject) => {
          if (this.allAbilities.length) {
            resolve(this.allAbilities)
          } else {
            axios
              .get(`/api/v1/abilities`, { params })
              .then((response) => {
                this.allAbilities = response.data.abilities

                resolve(response)
              })
              .catch((err) => {
                handleError(err)
                reject(err)
              })
          }
        })
      },

      deleteRole(id) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/roles/${id}`)
            .then((response) => {
              let index = this.roles.findIndex((role) => role.id === id)
              this.roles.splice(index, 1)

              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.roles.deleted_message'),
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

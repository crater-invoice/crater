import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useUsersStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'users',
    state: () => ({
      roles: [],
      users: [],
      totalUsers: 0,
      currentUser: null,
      selectAllField: false,
      selectedUsers: [],
      customerList: [],
      userList: [],

      userData: {
        name: '',
        email: '',
        password: null,
        phone: null,
        companies: [],
      },
    }),

    actions: {
      resetUserData() {
        this.userData = {
          name: '',
          email: '',
          password: null,
          phone: null,
          role: null,
          companies: [],
        }
      },

      fetchUsers(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/users`, { params })
            .then((response) => {
              this.users = response.data.data
              this.totalUsers = response.data.meta.total
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchUser(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/users/${id}`)
            .then((response) => {
              this.userData = response.data.data
              if (this.userData?.companies?.length) {
                this.userData.companies.forEach((c, i) => {
                  this.userData.roles.forEach((r) => {
                    if (r.scope === c.id)
                      this.userData.companies[i].role = r.name
                  })
                })
              }
              resolve(response)
            })
            .catch((err) => {
              console.log(err)
              handleError(err)
              reject(err)
            })
        })
      },

      fetchRoles(state) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/roles`)
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

      addUser(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/users', data)
            .then((response) => {
              this.users.push(response.data)
              const notificationStore = useNotificationStore()

              notificationStore.showNotification({
                type: 'success',
                message: global.t('users.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateUser(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/users/${data.id}`, data)
            .then((response) => {
              if (response) {
                let pos = this.users.findIndex(
                  (user) => user.id === response.data.data.id
                )
                this.users[pos] = response.data.data
              }
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('users.updated_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteUser(id) {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/users/delete`, { users: id.ids })
            .then((response) => {
              let index = this.users.findIndex((user) => user.id === id)
              this.users.splice(index, 1)
              notificationStore.showNotification({
                type: 'success',
                message: global.tc('users.deleted_message', 1),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteMultipleUsers() {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/users/delete`, { users: this.selectedUsers })
            .then((response) => {
              this.selectedUsers.forEach((user) => {
                let index = this.users.findIndex(
                  (_user) => _user.id === user.id
                )
                this.users.splice(index, 1)
              })
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.tc('users.deleted_message', 2),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      searchUsers(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/search`, { params })
            .then((response) => {
              this.userList = response.data.users.data
              this.customerList = response.data.customers.data
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

      selectUser(data) {
        this.selectedUsers = data
        if (this.selectedUsers.length === this.users.length) {
          this.selectAllField = true
        } else {
          this.selectAllField = false
        }
      },

      selectAllUsers() {
        if (this.selectedUsers.length === this.users.length) {
          this.selectedUsers = []
          this.selectAllField = false
        } else {
          let allUserIds = this.users.map((user) => user.id)
          this.selectedUsers = allUserIds
          this.selectAllField = true
        }
      },
    },
  })()
}

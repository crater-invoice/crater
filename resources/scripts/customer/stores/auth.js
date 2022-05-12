const { defineStore } = window.pinia
import axios from 'axios'
import { useNotificationStore } from '@/scripts/stores/notification'
import router from '@/scripts/customer/customer-router'
import { handleError } from '@/scripts/customer/helpers/error-handling'
const { global } = window.i18n

export const useAuthStore = defineStore({
  id: 'customerAuth',
  state: () => ({
    loginData: {
      email: '',
      password: '',
      device_name: 'xyz',
      company: '',
    },
  }),

  actions: {
    login(data) {
      const notificationStore = useNotificationStore(true)
      return new Promise((resolve, reject) => {
        axios.get('/sanctum/csrf-cookie').then((response) => {
          if (response) {
            axios
              .post(`/${data.company}/customer/login`, data)
              .then((response) => {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.tm('general.login_successfully'),
                })
                resolve(response)
                setTimeout(() => {
                  this.loginData.email = ''
                  this.loginData.password = ''
                }, 1000)
              })
              .catch((err) => {
                handleError(err)
                reject(err)
              })
          }
        })
      })
    },

    forgotPassword(data) {
      const notificationStore = useNotificationStore(true)
      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/${data.company}/customer/auth/password/email`, data)

          .then((response) => {
            if (response.data) {
              notificationStore.showNotification({
                type: 'success',
                message: global.tm('general.send_mail_successfully'),
              })
            }
            resolve(response)
          })
          .catch((err) => {
            if (err.response && err.response.status === 403) {
              notificationStore.showNotification({
                type: 'error',
                message: global.tm('errors.email_could_not_be_sent'),
              })
            } else {
              handleError(err)
            }
            reject(err)
          })
      })
    },

    resetPassword(data, company) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/${company}/customer/auth/reset/password`, data)

          .then((response) => {
            if (response.data) {
              const notificationStore = useNotificationStore(true)
              notificationStore.showNotification({
                type: 'success',
                message: global.tm('login.password_reset_successfully'),
              })
            }
            resolve(response)
          })
          .catch((err) => {
            if (err.response && err.response.status === 403) {
              notificationStore.showNotification({
                type: 'error',
                message: global.tm('validation.email_incorrect'),
              })
            }
            reject(err)
          })
      })
    },

    logout(data) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/${data}/customer/logout`)
          .then((response) => {
            const notificationStore = useNotificationStore()
            notificationStore.showNotification({
              type: 'success',
              message: global.tm('general.logged_out_successfully'),
            })
            router.push({ name: 'customer.login' })
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },
  },
})

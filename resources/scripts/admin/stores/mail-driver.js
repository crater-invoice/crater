import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useMailDriverStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'mail-driver',

    state: () => ({
      mailConfigData: null,
      mail_driver: 'smtp',
      mail_drivers: [],

      basicMailConfig: {
        mail_driver: '',
        mail_host: '',
        from_mail: '',
        from_name: '',
      },

      mailgunConfig: {
        mail_driver: '',
        mail_mailgun_domain: '',
        mail_mailgun_secret: '',
        mail_mailgun_endpoint: '',
        from_mail: '',
        from_name: '',
      },

      sesConfig: {
        mail_driver: '',
        mail_host: '',
        mail_port: null,
        mail_ses_key: '',
        mail_ses_secret: '',
        mail_encryption: 'tls',
        from_mail: '',
        from_name: '',
      },

      smtpConfig: {
        mail_driver: '',
        mail_host: '',
        mail_port: null,
        mail_username: '',
        mail_password: '',
        mail_encryption: 'tls',
        from_mail: '',
        from_name: '',
      },
    }),

    actions: {
      fetchMailDrivers() {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/mail/drivers')
            .then((response) => {
              if (response.data) {
                this.mail_drivers = response.data
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchMailConfig() {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/mail/config')
            .then((response) => {
              if (response.data) {
                this.mailConfigData = response.data
                this.mail_driver = response.data.mail_driver
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateMailConfig(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/mail/config', data)
            .then((response) => {
              const notificationStore = useNotificationStore()
              if (response.data.success) {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('wizard.success.' + response.data.success),
                })
              } else {
                notificationStore.showNotification({
                  type: 'error',
                  message: global.t('wizard.errors.' + response.data.error),
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

      sendTestMail(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/mail/test', data)
            .then((response) => {
              const notificationStore = useNotificationStore()
              if (response.data.success) {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('general.send_mail_successfully'),
                })
              } else {
                notificationStore.showNotification({
                  type: 'error',
                  message: global.t('validation.something_went_wrong'),
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

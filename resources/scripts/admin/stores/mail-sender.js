import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'
import mailSenderStub from '@/scripts/admin/stub/mail-sender.js'

export const useMailSenderStore = (useWindow = false) => {
  const pre_t = 'settings.mail_sender'
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'mailSender',

    state: () => ({
      mailSenders: [],
      mail_drivers: [], // list of mail drivers
      currentMailSender: { ...mailSenderStub.basicConfig },
      smtpConfig: { ...mailSenderStub.smtpConfig },
      mailgunConfig: { ...mailSenderStub.mailgunConfig },
      sesConfig: { ...mailSenderStub.sesConfig },
      mail_encryptions: ['none', 'tls', 'ssl', 'starttls'],
      isDisable: false
    }),

    getters: {
      isEdit: (state) => (state.currentMailSender.id ? true : false),
    },

    actions: {
      resetCurrentMailSender() {
        this.currentMailSender = { ...mailSenderStub.basicConfig }
        this.smtpConfig = { ...mailSenderStub.smtpConfig }
        this.mailgunConfig = { ...mailSenderStub.mailgunConfig }
        this.sesConfig = { ...mailSenderStub.sesConfig }
        this.isDisable = false
      },

      fetchMailDrivers() {
        return new Promise((resolve, reject) => {
          axios
            .get('/api/v1/mail-drivers')
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


      fetchMailSenders(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/mail-senders`, { params })
            .then((response) => {
              this.mailSenders = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchMailSender(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/mail-senders/${id}`)
            .then((response) => {
              this.currentMailSender = response.data.data
              this.isDisable = response.data.data.is_default
              if (response.data.data.settings) {
                var settings = response.data.data.settings
                const encryptionNone = settings.encryption == '' || settings.encryption == undefined
                switch (response.data.data.driver) {
                  case 'smtp':
                    this.smtpConfig = settings
                    encryptionNone ? this.smtpConfig.encryption = 'none' : ''
                    break
                  case 'mailgun':
                    this.mailgunConfig = settings
                    break
                  case 'ses':
                    this.sesConfig = settings
                    encryptionNone ? this.sesConfig.encryption = 'none' : ''
                    break
                }
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addMailSender(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/mail-senders', data)
            .then((response) => {
              this.mailSenders.push(response.data.data)
              notificationStore.showNotification({
                type: 'success',
                message: global.t(`${pre_t}.created_message`),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateMailSender(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/mail-senders/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.mailSenders.findIndex(
                  (mailSender) => mailSender.id === response.data.data.id
                )
                this.mailSenders[pos] = data
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t(`${pre_t}.updated_message`),
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

      deleteMailSender(id) {
        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/mail-senders/${id}`)
            .then((response) => {
              if (response.data.success) {
                let index = this.mailSenders.findIndex(
                  (mailSender) => mailSender.id === id
                )
                this.mailSenders.splice(index, 1)
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t(`${pre_t}.deleted_message`),
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
            .post('/api/v1/mail-test', data)
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

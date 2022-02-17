import axios from 'axios'
import moment from 'moment'
import { defineStore } from 'pinia'
import { useRoute } from 'vue-router'
import { useCompanyStore } from './company'
import { useNotificationStore } from '@/scripts/stores/notification'
import paymentStub from '../stub/payment'
import { handleError } from '@/scripts/helpers/error-handling'

export const usePaymentStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'payment',

    state: () => ({
      payments: [],
      paymentTotalCount: 0,

      selectAllField: false,
      selectedPayments: [],
      selectedNote: null,
      showExchangeRate: false,
      drivers: [],
      providers: [],

      paymentProviders: {
        id: null,
        name: '',
        driver: '',
        active: false,
        settings: {
          key: '',
          secret: '',
        },
      },

      currentPayment: {
        ...paymentStub,
      },

      paymentModes: [],
      currentPaymentMode: {
        id: '',
        name: null,
      },

      isFetchingInitialData: false,
    }),

    getters: {
      isEdit: (state) => (state.paymentProviders.id ? true : false),
    },

    actions: {
      fetchPaymentInitialData(isEdit) {
        const companyStore = useCompanyStore()
        const route = useRoute()

        this.isFetchingInitialData = true

        let actions = []
        if (isEdit) {
          actions = [this.fetchPayment(route.params.id)]
        }
        Promise.all([
          this.fetchPaymentModes({ limit: 'all' }),
          this.getNextNumber(),
          ...actions,
        ])
          .then(async ([res1, res2, res3]) => {
            if (isEdit) {
              if (res3.data.data.invoice) {
                this.currentPayment.maxPayableAmount = parseInt(
                  res3.data.data.invoice.due_amount
                )
              }
            }

            // On Create
            else if (!isEdit && res2.data) {
              this.currentPayment.payment_date = moment().format('YYYY-MM-DD')
              this.currentPayment.payment_number = res2.data.nextNumber
              this.currentPayment.currency =
                companyStore.selectedCompanyCurrency
            }

            this.isFetchingInitialData = false
          })
          .catch((err) => {
            handleError(err)
          })
      },

      fetchPayments(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/payments`, { params })
            .then((response) => {
              this.payments = response.data.data
              this.paymentTotalCount = response.data.meta.payment_total_count
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchPayment(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/payments/${id}`)
            .then((response) => {
              Object.assign(this.currentPayment, response.data.data)
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addPayment(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/payments', data)
            .then((response) => {
              this.payments.push(response.data)
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('payments.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updatePayment(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/payments/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.payments.findIndex(
                  (payment) => payment.id === response.data.data.id
                )

                this.payments[pos] = data.payment

                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('payments.updated_message'),
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

      deletePayment(id) {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/payments/delete`, id)
            .then((response) => {
              let index = this.payments.findIndex(
                (payment) => payment.id === id
              )
              this.payments.splice(index, 1)

              notificationStore.showNotification({
                type: 'success',
                message: global.t('payments.deleted_message', 1),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteMultiplePayments() {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/payments/delete`, { ids: this.selectedPayments })
            .then((response) => {
              this.selectedPayments.forEach((payment) => {
                let index = this.payments.findIndex(
                  (_payment) => _payment.id === payment.id
                )
                this.payments.splice(index, 1)
              })
              notificationStore.showNotification({
                type: 'success',
                message: global.tc('payments.deleted_message', 2),
              })
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

      selectPayment(data) {
        this.selectedPayments = data
        if (this.selectedPayments.length === this.payments.length) {
          this.selectAllField = true
        } else {
          this.selectAllField = false
        }
      },

      selectAllPayments() {
        if (this.selectedPayments.length === this.payments.length) {
          this.selectedPayments = []
          this.selectAllField = false
        } else {
          let allPaymentIds = this.payments.map((payment) => payment.id)
          this.selectedPayments = allPaymentIds
          this.selectAllField = true
        }
      },

      selectNote(data) {
        this.selectedNote = null
        this.selectedNote = data
      },

      resetSelectedNote(data) {
        this.selectedNote = null
      },

      searchPayment(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/payments`, { params })
            .then((response) => {
              this.payments = response.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      previewPayment(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/payments/${params.id}/send/preview`, { params })
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      sendEmail(data) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/payments/${data.id}/send`, data)
            .then((response) => {
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('payments.send_payment_successfully'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      getNextNumber(params, setState = false) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/next-number?key=payment`, { params })
            .then((response) => {
              if (setState) {
                this.currentPayment.payment_number = response.data.nextNumber
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      resetCurrentPayment() {
        this.currentPayment = {
          ...paymentStub,
        }
      },

      fetchPaymentModes(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/payment-methods`, { params })
            .then((response) => {
              this.paymentModes = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchPaymentMode(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/payment-methods/${id}`)
            .then((response) => {
              this.currentPaymentMode = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addPaymentMode(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/payment-methods`, data)
            .then((response) => {
              this.paymentModes.push(response.data.data)
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.payment_modes.payment_mode_added'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updatePaymentMode(data) {
        const notificationStore = useNotificationStore()
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/payment-methods/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.paymentModes.findIndex(
                  (paymentMode) => paymentMode.id === response.data.data.id
                )
                this.paymentModes[pos] = data.paymentModes
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t(
                    'settings.payment_modes.payment_mode_updated'
                  ),
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

      deletePaymentMode(id) {
        const notificationStore = useNotificationStore()

        return new Promise((resolve, reject) => {
          axios
            .delete(`/api/v1/payment-methods/${id}`)
            .then((response) => {
              let index = this.paymentModes.findIndex(
                (paymentMode) => paymentMode.id === id
              )
              this.paymentModes.splice(index, 1)
              if (response.data.success) {
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t('settings.payment_modes.deleted_message'),
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

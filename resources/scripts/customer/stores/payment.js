import { handleError } from '@/scripts/customer/helpers/error-handling'
const { defineStore } = window.pinia
import axios from 'axios'

export const usePaymentStore = defineStore({
  id: 'customerPaymentStore',
  state: () => ({
    payments: [],
    selectedViewPayment: [],
    totalPayments: 0,
  }),

  actions: {
    fetchPayments(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/payments`, { params })
          .then((response) => {
            this.payments = response.data.data
            this.totalPayments = response.data.meta.paymentTotalCount
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },

    fetchViewPayment(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/payments/${params.id}`)

          .then((response) => {
            this.selectedViewPayment = response.data.data
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },

    searchPayment(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/payments`, { params })
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

    fetchPaymentModes(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/payment-method`, { params })
          .then((response) => {
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

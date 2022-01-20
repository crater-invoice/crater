const { defineStore } = window.pinia
import { useNotificationStore } from '@/scripts/stores/notification'
import axios from 'axios'
import { handleError } from '@/scripts/customer/helpers/error-handling'

export const useEstimateStore = defineStore({
  id: 'customerEstimateStore',
  state: () => ({
    estimates: [],
    totalEstimates: 0,
    selectedViewEstimate: [],
  }),
  actions: {
    fetchEstimate(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/estimates`, { params })
          .then((response) => {
            this.estimates = response.data.data
            this.totalEstimates = response.data.meta.estimateTotalCount
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },

    fetchViewEstimate(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/estimates/${params.id}`, {
            params,
          })

          .then((response) => {
            this.selectedViewEstimate = response.data.data
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },

    searchEstimate(params, slug) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${slug}/customer/estimates`, { params })
          .then((response) => {
            this.estimates = response.data
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },
    acceptEstimate({ slug, id, status }) {

      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/${slug}/customer/estimate/${id}/status`, { status })
          .then((response) => {
            let pos = this.estimates.findIndex(
              (estimate) => estimate.id === id
            )
            if (this.estimates[pos]) {
              this.estimates[pos].status = 'ACCEPTED'

              const notificationStore = useNotificationStore(true)
              notificationStore.showNotification({
                type: 'success',
                message: global.t('estimates.marked_as_accepted_message'),
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

    rejectEstimate({ slug, id, status }) {

      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/${slug}/customer/estimate/${id}/status`, { status })
          .then((response) => {
            let pos = this.estimates.findIndex(
              (estimate) => estimate.id === id
            )
            if (this.estimates[pos]) {
              this.estimates[pos].status = 'REJECTED'

              const notificationStore = useNotificationStore(true)
              notificationStore.showNotification({
                type: 'success',
                message: global.t('estimates.marked_as_rejected_message'),
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
})

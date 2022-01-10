const { defineStore } = window.pinia
import { useGlobalStore } from '@/scripts/customer/stores/global'
import axios from 'axios'
import { handleError } from '@/scripts/customer/helpers/error-handling'

export const useDashboardStore = defineStore({
  id: 'dashboard',
  state: () => ({
    recentInvoices: [],
    recentEstimates: [],
    invoiceCount: 0,
    estimateCount: 0,
    paymentCount: 0,
    totalDueAmount: [],
    isDashboardDataLoaded: false,
  }),

  actions: {
    loadData(data) {
      const globalStore = useGlobalStore()
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${globalStore.companySlug}/customer/dashboard`, {
            data,
          })
          .then((response) => {
            this.totalDueAmount = response.data.due_amount
            this.estimateCount = response.data.estimate_count
            this.invoiceCount = response.data.invoice_count
            this.paymentCount = response.data.payment_count
            this.recentInvoices = response.data.recentInvoices
            this.recentEstimates = response.data.recentEstimates
            globalStore.getDashboardDataLoaded = true
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

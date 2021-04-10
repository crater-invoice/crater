import * as types from './mutation-types'

export default {
  [types.SET_INITIAL_DATA](state, data) {
    state.contacts = data.customersCount
    state.invoices = data.invoicesCount
    state.estimates = data.estimatesCount
    state.expenses = data.expenses
    state.recentInvoices = data.invoices
    state.newContacts = data.contacts
    state.totalDueAmount = data.totalDueAmount

    state.dueInvoices = data.dueInvoices
    state.recentEstimates = data.estimates

    state.weeklyInvoices.days = data.weekDays
    state.weeklyInvoices.counter = data.counters

    if (state.chartData && data.chartData) {
      state.chartData.months = data.chartData.months
      state.chartData.invoiceTotals = data.chartData.invoiceTotals
      state.chartData.expenseTotals = data.chartData.expenseTotals
      state.chartData.netProfits = data.chartData.netProfits
      state.chartData.receiptTotals = data.chartData.receiptTotals
    }

    state.salesTotal = data.salesTotal
    state.totalReceipts = data.totalReceipts
    state.totalExpenses = data.totalExpenses
    state.netProfit = data.netProfit
  },

  [types.SET_DASHBOARD_DATA_LOADED_STATE](state, data) {
    state.isDashboardDataLoaded = data
  },

  [types.UPDATE_INVOICE_STATUS](state, data) {
    let pos = state.dueInvoices.findIndex((invoice) => invoice.id === data.id)

    if (state.dueInvoices[pos]) {
      state.dueInvoices[pos].status = data.status
    }
  },

  [types.DELETE_INVOICE](state, data) {
    let index = state.dueInvoices.findIndex(
      (invoice) => invoice.id === data.ids[0]
    )
    state.dueInvoices.splice(index, 1)
  },

  [types.DELETE_ESTIMATE](state, data) {
    let index = state.recentEstimates.findIndex(
      (estimate) => estimate.id === data.ids[0]
    )
    state.recentEstimates.splice(index, 1)
  },

  [types.UPDATE_ESTIMATE_STATUS](state, data) {
    let pos = state.recentEstimates.findIndex(
      (estimate) => estimate.id === data.id
    )

    if (state.recentEstimates[pos]) {
      state.recentEstimates[pos].status = data.status
    }
  },
}

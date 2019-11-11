import * as types from './mutation-types'

export default {
  [types.SET_INITIAL_DATA] (state, data) {
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

  [types.GET_INITIAL_DATA] (state, data) {
    state.isDataLoaded = data
  }
}

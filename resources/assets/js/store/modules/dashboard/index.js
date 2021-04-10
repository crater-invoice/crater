import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  contacts: 0,
  invoices: 0,
  estimates: 0,
  expenses: 0,
  totalDueAmount: [],
  isDashboardDataLoaded: false,

  weeklyInvoices: {
    days: [],
    counter: [],
  },

  chartData: {
    months: [],
    invoiceTotals: [],
    expenseTotals: [],
    netProfits: [],
    receiptTotals: [],
  },

  salesTotal: null,
  totalReceipts: null,
  totalExpenses: null,
  netProfit: null,

  dueInvoices: [],
  recentEstimates: [],
  newContacts: [],
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

import * as types from './mutation-types'

export default {
  [types.SET_PAYMENTS] (state, payments) {
    state.payments = payments
  },

  [types.SET_TOTAL_PAYMENTS] (state, totalPayments) {
    state.totalPayments = totalPayments
  },

  [types.ADD_PAYMENT] (state, data) {
    state.payments.push(data)
  },

  [types.DELETE_PAYMENT] (state, id) {
    let index = state.payments.findIndex(payment => payment.id === id)
    state.payments.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_PAYMENTS] (state, selectedPayments) {
    selectedPayments.forEach((payment) => {
      let index = state.payments.findIndex(_inv => _inv.id === payment.id)
      state.payments.splice(index, 1)
    })

    state.selectedPayments = []
  },

  [types.SET_SELECTED_PAYMENTS] (state, data) {
    state.selectedPayments = data
  },

  [types.SET_SELECT_ALL_STATE] (state, data) {
    state.selectAllField = data
  }
}

import * as types from './mutation-types'

export default {
  [types.SET_PAYMENTS](state, payments) {
    state.payments = payments
  },

  [types.SET_TOTAL_PAYMENTS](state, totalPayments) {
    state.totalPayments = totalPayments
  },

  [types.ADD_PAYMENT](state, data) {
    state.payments.push(data)
  },

  [types.DELETE_PAYMENT](state, id) {
    let index = state.payments.findIndex((payment) => payment.id === id)
    state.payments.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_PAYMENTS](state, selectedPayments) {
    selectedPayments.forEach((payment) => {
      let index = state.payments.findIndex((_inv) => _inv.id === payment.id)
      state.payments.splice(index, 1)
    })

    state.selectedPayments = []
  },

  [types.SET_SELECTED_PAYMENTS](state, data) {
    state.selectedPayments = data
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.SET_PAYMENT_MODES](state, data) {
    state.paymentModes = data
  },

  [types.ADD_PAYMENT_MODE](state, data) {
    state.paymentModes = [data.paymentMethod, ...state.paymentModes]
  },

  [types.DELETE_PAYMENT_MODE](state, id) {
    let index = state.paymentModes.findIndex(
      (paymentMethod) => paymentMethod.id === id
    )
    state.paymentModes.splice(index, 1)
  },

  [types.UPDATE_PAYMENT_MODE](state, data) {
    let pos = state.paymentModes.findIndex(
      (paymentMethod) => paymentMethod.id === data.paymentMethod.id
    )
    state.paymentModes[pos] = data.paymentMethod
  },

  [types.RESET_SELECTED_NOTE](state, data) {
    state.selectedNote = null
  },

  [types.SET_SELECTED_NOTE](state, data) {
    state.selectedNote = data
  },
}

import * as types from './mutation-types'

export const fetchPayments = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payments`, { params })
      .then((response) => {
        commit(types.SET_PAYMENTS, response.data.payments.data)
        commit(types.SET_TOTAL_PAYMENTS, response.data.paymentTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchPayment = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payments/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addPayment = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/payments', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectPayment = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_PAYMENTS, data)
  if (state.selectedPayments.length === state.payments.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const updatePayment = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/payments/${data.id}`, data.editData)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteMultiplePayments = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/payments/delete`, { ids: state.selectedPayments })
      .then((response) => {
        commit(types.DELETE_MULTIPLE_PAYMENTS, state.selectedPayments)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deletePayment = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/payments/delete`, id)
      .then((response) => {
        commit(types.DELETE_PAYMENT, id)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectAllPayments = ({ commit, dispatch, state }) => {
  if (state.selectedPayments.length === state.payments.length) {
    commit(types.SET_SELECTED_PAYMENTS, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allPaymentIds = state.payments.map((pay) => pay.id)
    commit(types.SET_SELECTED_PAYMENTS, allPaymentIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const addPaymentMode = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/payment-methods`, data)
      .then((response) => {
        commit(types.ADD_PAYMENT_MODE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updatePaymentMode = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/payment-methods/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_PAYMENT_MODE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchPaymentModes = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-methods`, { params })
      .then((response) => {
        commit(types.SET_PAYMENT_MODES, response.data.paymentMethods.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchPaymentMode = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payment-methods/${data.id}`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deletePaymentMode = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/payment-methods/${id}`)
      .then((response) => {
        if (!response.data.error) {
          commit(types.DELETE_PAYMENT_MODE, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const sendEmail = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/payments/${data.id}/send`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const searchPayment = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/payments?${data}`)
      .then((response) => {
        // commit(types.UPDATE_INVOICE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectNote = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_NOTE)
  commit(types.SET_SELECTED_NOTE, data.notes)
}

export const resetSelectedNote = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_NOTE)
}

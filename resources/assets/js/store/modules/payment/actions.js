import * as types from './mutation-types'

export const fetchPayments = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/payments`, {params}).then((response) => {
      commit(types.SET_PAYMENTS, response.data.payments.data)
      commit(types.SET_TOTAL_PAYMENTS, response.data.payments.total)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const fetchCreatePayment = ({ commit, dispatch }, page) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/payments/create`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const fetchPayment = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/payments/${id}/edit`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const addPayment = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post('/api/payments', data).then((response) => {
      resolve(response)
    }).catch((err) => {
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
    window.axios.put(`/api/payments/${data.id}`, data.editData).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deletePayment = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/payments/${id}`).then((response) => {
      commit(types.DELETE_PAYMENT, id)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deleteMultiplePayments = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/payments/delete`, {'id': state.selectedPayments}).then((response) => {
      commit(types.DELETE_MULTIPLE_PAYMENTS, state.selectedPayments)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const selectAllPayments = ({ commit, dispatch, state }) => {
  if (state.selectedPayments.length === state.payments.length) {
    commit(types.SET_SELECTED_PAYMENTS, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allPaymentIds = state.payments.map(pay => pay.id)
    commit(types.SET_SELECTED_PAYMENTS, allPaymentIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

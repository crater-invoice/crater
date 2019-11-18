
import * as types from './mutation-types'

export const loadData = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/dashboard`, { params }).then((response) => {
      commit(types.SET_INITIAL_DATA, response.data)
      commit(types.GET_INITIAL_DATA, true)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const getChart = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/dashboard/expense/chart`).then((response) => {
      commit(types.SET_INITIAL_DATA, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const sendEmail = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/invoices/send`, data).then((response) => {
      commit(types.UPDATE_INVOICE_STATUS, { id: data.id, status: 'SENT' })
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}


export const markAsSent = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/invoices/mark-as-sent`, data).then((response) => {
      commit(types.UPDATE_INVOICE_STATUS, { id: data.id, status: 'SENT' })
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deleteInvoice = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/invoices/${id}`).then((response) => {
      if (response.data.error) {
        resolve(response)
      } else {
        commit(types.DELETE_INVOICE, id)
        resolve(response)
      }
    }).catch((err) => {
      reject(err)
    })
  })
}


export const sendEstimateEmail = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/estimates/send`, data).then((response) => {
      commit(types.UPDATE_ESTIMATE_STATUS, { id: data.id, status: 'SENT' })
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const markAsAccepted = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/estimates/accept`, data).then((response) => {
      commit(types.UPDATE_ESTIMATE_STATUS, { id: data.id, status: 'ACCEPTED' })
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const markAsRejected = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/estimates/reject`, data).then((response) => {
      commit(types.UPDATE_ESTIMATE_STATUS, { id: data.id, status: 'REJECTED' })
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const markEstimateAsSent = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/estimates/mark-as-sent`, data).then((response) => {
      commit(types.UPDATE_ESTIMATE_STATUS, { id: data.id, status: 'SENT' })
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const convertToInvoice = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.post(`/api/estimates/${id}/convert-to-invoice`).then((response) => {
      // commit(types.UPDATE_INVOICE, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deleteEstimate = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/estimates/${id}`).then((response) => {
      commit(types.DELETE_ESTIMATE, id)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}
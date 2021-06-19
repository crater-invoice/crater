import * as types from './mutation-types'
import * as dashboardTypes from '../dashboard/mutation-types'

export const fetchInvoices = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices`, { params })
      .then((response) => {
        commit(types.SET_INVOICES, response.data.invoices.data)
        commit(types.SET_TOTAL_INVOICES, response.data.invoiceTotalCount)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchInvoice = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices/${id}`)
      .then((response) => {
        commit(types.SET_TEMPLATE_NAME, response.data.invoice.template_name)
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
      .post(`/api/v1/invoices/${data.id}/send`, data)
      .then((response) => {
        if (response.data.success) {
          commit(types.UPDATE_INVOICE_STATUS, { id: data.id, status: 'SENT' })
          commit(
            'dashboard/' + dashboardTypes.UPDATE_INVOICE_STATUS,
            { id: data.id, status: 'SENT' },
            { root: true }
          )
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addInvoice = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/invoices', data)
      .then((response) => {
        commit(types.ADD_INVOICE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteInvoice = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/delete`, id)
      .then((response) => {
        if (response.data.error) {
          resolve(response)
        } else {
          commit(types.DELETE_INVOICE, id)
          commit('dashboard/' + dashboardTypes.DELETE_INVOICE, id, {
            root: true,
          })
          resolve(response)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteMultipleInvoices = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/delete`, { ids: state.selectedInvoices })
      .then((response) => {
        if (response.data.error) {
          resolve(response)
        } else {
          commit(types.DELETE_MULTIPLE_INVOICES, state.selectedInvoices)
          resolve(response)
        }
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateInvoice = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/invoices/${data.id}`, data)
      .then((response) => {
        if (response.data.invoice) {
          commit(types.UPDATE_INVOICE, response.data)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const markAsSent = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/${data.id}/status`, data)
      .then((response) => {
        commit(types.UPDATE_INVOICE_STATUS, { id: data.id, status: 'SENT' })
        commit(
          'dashboard/' + dashboardTypes.UPDATE_INVOICE_STATUS,
          { id: data.id, status: 'SENT' },
          {
            root: true,
          }
        )
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const cloneInvoice = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/invoices/${data.id}/clone`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const searchInvoice = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices?${data}`)
      .then((response) => {
        // commit(types.UPDATE_INVOICE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const getInvoiceNumber = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/next-number?key=invoice`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const selectInvoice = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_INVOICES, data)
  if (state.selectedInvoices.length === state.invoices.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllInvoices = ({ commit, dispatch, state }) => {
  if (state.selectedInvoices.length === state.invoices.length) {
    commit(types.SET_SELECTED_INVOICES, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allInvoiceIds = state.invoices.map((inv) => inv.id)
    commit(types.SET_SELECTED_INVOICES, allInvoiceIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const resetSelectedInvoices = ({ commit, dispatch, state }) => {
  commit(types.RESET_SELECTED_INVOICES)
}
export const setCustomer = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_CUSTOMER)
  commit(types.SET_CUSTOMER, data)
}

export const resetCustomer = ({ commit, dispatch, state }) => {
  commit(types.RESET_CUSTOMER)
}

export const setTemplate = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    commit(types.SET_TEMPLATE_NAME, data)
    resolve({})
  })
}

export const selectCustomer = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/customers/${id}`)
      .then((response) => {
        commit(types.RESET_SELECTED_CUSTOMER)
        commit(types.SELECT_CUSTOMER, response.data.customer)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const resetSelectedCustomer = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_CUSTOMER)
}

export const setItem = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_ITEM)
  commit(types.SET_ITEM, data)
}

export const resetItem = ({ commit, dispatch, state }) => {
  commit(types.RESET_ITEM)
}

export const selectNote = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_NOTE)
  commit(types.SET_SELECTED_NOTE, data.notes)
}

export const resetSelectedNote = ({ commit, dispatch, state }, data) => {
  commit(types.RESET_SELECTED_NOTE)
}

import * as types from './mutation-types'

export default {
  [types.SET_INVOICES](state, invoices) {
    state.invoices = invoices
  },

  [types.SET_TOTAL_INVOICES](state, totalInvoices) {
    state.totalInvoices = totalInvoices
  },

  [types.ADD_INVOICE](state, data) {
    state.invoices.push(data)
  },

  [types.DELETE_INVOICE](state, data) {
    let index = state.invoices.findIndex((invoice) => invoice.id === data.id)
    state.invoices.splice(index, 1)
  },

  [types.SET_SELECTED_INVOICES](state, data) {
    state.selectedInvoices = data
  },

  [types.UPDATE_INVOICE](state, data) {
    let pos = state.invoices.findIndex(
      (invoice) => invoice.id === data.invoice.id
    )

    state.invoices[pos] = data.invoice
  },

  [types.UPDATE_INVOICE_STATUS](state, data) {
    let pos = state.invoices.findIndex((invoice) => invoice.id === data.id)

    if (state.invoices[pos]) {
      state.invoices[pos].status = data.status
    }
  },

  [types.RESET_SELECTED_INVOICES](state, data) {
    state.selectedInvoices = []
    state.selectAllField = false
  },

  [types.DELETE_MULTIPLE_INVOICES](state, selectedInvoices) {
    selectedInvoices.forEach((invoice) => {
      let index = state.invoices.findIndex((_inv) => _inv.id === invoice.id)
      state.invoices.splice(index, 1)
    })
    state.selectedInvoices = []
  },

  [types.SET_TEMPLATE_NAME](state, templateName) {
    state.invoiceTemplateName = templateName
  },

  [types.SELECT_CUSTOMER](state, data) {
    state.selectedCustomer = data
  },

  [types.RESET_SELECTED_CUSTOMER](state, data) {
    state.selectedCustomer = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.RESET_SELECTED_NOTE](state, data) {
    state.selectedNote = null
  },

  [types.SET_SELECTED_NOTE](state, data) {
    state.selectedNote = data
  },

  [types.RESET_ITEM](state, data) {
    state.selectedItem = null
  },

  [types.SET_ITEM](state, data) {
    state.selectedItem = data
  },
}

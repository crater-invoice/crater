import * as types from './mutation-types'

export const fetchInvoiceTemplates = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/invoices/templates`, { params })
      .then((response) => {
        commit(types.SET_INVOICE_TEMPLATES, response.data.invoiceTemplates)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

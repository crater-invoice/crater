import * as types from './mutation-types'

export const loadInvoiceData = ({ commit, dispatch, state },data) => {
  return new Promise((resolve, reject) => {
    window.axios.post('/api/report/invoice', data).then((response) => {
      commit(types.SET_INVOICES, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

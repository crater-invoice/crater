import * as types from './mutation-types'

export const loadTaxesReportLink = ({ commit, dispatch, state }, url) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/reports/tax-summary/link`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

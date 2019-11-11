import * as types from './mutation-types'

export const loadProfitLossLink = ({ commit, dispatch, state }, url) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/reports/profit-loss/link`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

import * as types from './mutation-types'

export const loadExpensesLink = ({ commit, dispatch, state }, url) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/reports/expenses/link`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

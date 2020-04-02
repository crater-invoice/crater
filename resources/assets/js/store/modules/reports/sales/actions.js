import * as types from './mutation-types'

export const loadLinkByCustomer = ({ commit, dispatch, state }, url) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/reports/sales/customers/link`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const loadLinkByItems = ({ commit, dispatch, state }, url) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/reports/sales/items/link`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

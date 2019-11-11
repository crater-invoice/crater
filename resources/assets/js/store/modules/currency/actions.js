import * as types from './mutation-types'

// export const indexLoadData = ({ commit, dispatch, state }) => {
//   return new Promise((resolve, reject) => {
//     window.axios.get('/api/settings/currencies').then((response) => {
//       commit(types.BOOTSTRAP_CURRENCIES, response.data)
//       resolve(response)
//     }).catch((err) => {
//       reject(err)
//     })
//   })
// }

// export const loadData = ({ commit, dispatch, state }, id) => {
//   return new Promise((resolve, reject) => {
//     window.axios.get(`/api/settings/currencies/${id}/edit`).then((response) => {
//       resolve(response)
//     }).catch((err) => {
//       reject(err)
//     })
//   })
// }

// export const addCurrency = ({ commit, dispatch, state }, data) => {
//   return new Promise((resolve, reject) => {
//     window.axios.post('/api/settings/currencies', data).then((response) => {
//       commit(types.ADD_CURRENCY, response.data)
//       resolve(response)
//     }).catch((err) => {
//       reject(err)
//     })
//   })
// }

// export const editCurrency = ({ commit, dispatch, state }, data) => {
//   return new Promise((resolve, reject) => {
//     window.axios.put('/api/settings/currency_setting', data).then((response) => {
//       resolve(response)
//     }).catch((err) => {
//       reject(err)
//     })
//   })
// }

// export const removeItem = ({ commit, dispatch, state }, id) => {
//   return new Promise((resolve, reject) => {
//     window.axios.delete(`/api/settings/currencies/${id}`).then((response) => {
//       resolve(response)
//     }).catch((err) => {
//       reject(err)
//     })
//   })
// }

// export const selectCurrency = ({ commit, dispatch, state }) => {
//   return new Promise((resolve, reject) => {
//     let data = {
//       currency: state.currencyId
//     }
//     window.axios.delete(`/api/settings/currency_setting`, data).then((response) => {
//       resolve(response)
//     }).catch((err) => {
//       reject(err)
//     })
//   })
// }

export const setDefaultCurrency = ({ commit, dispatch, state }, data) => {
  commit(types.SET_DEFAULT_CURRENCY, { default_currency: data })
}

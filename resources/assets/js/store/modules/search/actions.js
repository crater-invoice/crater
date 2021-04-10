import * as types from './mutation-types'

export const searchUsers = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/search`, { params })
      .then((response) => {
        commit(types.SET_CUSTOMER_LISTS, response.data.customers.data)
        commit(types.SET_USER_LISTS, response.data.users.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

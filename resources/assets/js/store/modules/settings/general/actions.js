import * as types from './mutation-types'

export const submitData = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post('/api/settings/general', data).then((response) => {
      // commit(types.SET_CATEGORIES, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const loadData = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios.get('/api/settings/general').then((response) => {
      commit(types.SET_INITIAL_DATA, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const setItemDiscount = ({ commit, dispatch, state }) => {
  commit(types.SET_ITEM_DISCOUNT)
}

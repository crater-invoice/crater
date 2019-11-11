// import * as types from './mutation-types'

export const loadData = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/settings/profile`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const editUser = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.put('/api/settings/profile', data).then((response) => {
      // commit(types.UPDATE_USER, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

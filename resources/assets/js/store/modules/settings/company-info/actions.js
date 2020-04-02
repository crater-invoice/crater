// import * as types from './mutation-types'

export const loadData = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/settings/company`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const editCompany = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post('/api/settings/company', data).then((response) => {
      // commit(types.UPDATE_ITEM, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

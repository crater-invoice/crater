import * as types from './mutation-types'

export const loadData = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/settings/general`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const editPreferences = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.put('/api/settings/general', data).then((response) => {
      // commit(types.UPDATE_USER, response.data)
      commit(types.SET_MOMENT_DATE_FORMAT, data.moment_date_format)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

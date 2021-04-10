import * as types from './mutation-types'

export const fetchBackups = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/backups`, { params })
      .then((response) => {
        commit(types.SET_BACKUPS, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const createBackup = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/backups`, data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const removeBackup = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/backups/${params.disk}`, { params })
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

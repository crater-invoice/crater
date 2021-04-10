import { reject } from 'lodash'
import * as types from './mutation-types'

export const updateCurrentUser = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    axios
      .put('/api/v1/me', data)
      .then((response) => {
        commit(types.UPDATE_CURRENT_USER, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchCurrentUser = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/me`, params)
      .then((response) => {
        commit(types.BOOTSTRAP_CURRENT_USER, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const uploadAvatar = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/me/upload-avatar', data)
      .then((response) => {
        commit(types.UPDATE_USER_AVATAR, response.data.user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchUserSettings = ({ commit, dispatch, state }, settings) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get('/api/v1/me/settings', {
        params: {
          settings,
        },
      })
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateUserSettings = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put('/api/v1/me/settings', data)
      .then((response) => {
        commit(types.SET_DEFAULT_LANGUAGE, data.settings.language)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

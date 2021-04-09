import * as types from './mutation-types'
import * as userTypes from '../user/mutation-types'
import * as rootTypes from '../../mutation-types'
import router from '@/router.js'

export const login = ({ commit }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.get('/sanctum/csrf-cookie').then((response) => {
      window.axios
        .post('/login', data)
        .then((response) => {
          commit(types.SET_LOGOUT, false)
          commit('user/' + userTypes.RESET_CURRENT_USER, null, { root: true })
          commit(rootTypes.UPDATE_APP_LOADING_STATUS, false, { root: true })

          resolve(response)
        })
        .catch((err) => {
          commit(types.AUTH_ERROR, err.response)
          reject(err)
        })
    })
  })
}

export const setLogoutFalse = ({ state, commit }) => {
  commit(types.SET_LOGOUT, false)
}

export const logout = ({ state, commit, dispatch }) => {
  return new Promise((resolve, reject) => {
    if (state.isLoggedOut) {
      resolve()
      return true
    }
    commit(types.SET_LOGOUT, true)

    window.axios
      .get('/auth/logout')
      .then(() => {
        router.push('/login')
        dispatch(
          'notification/showNotification',
          {
            type: 'success',
            message: 'Logged out successfully.',
          },
          { root: true }
        )
      })
      .catch((err) => {
        router.push('/login')
        reject(err)
      })
  })
}

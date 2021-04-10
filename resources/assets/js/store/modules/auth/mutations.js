import * as types from './mutation-types'

export default {
  [types.AUTH_SUCCESS](state, token) {
    state.token = token
    state.status = 'success'
  },

  [types.SET_LOGOUT](state, data) {
    state.isLoggedOut = data
  },

  [types.AUTH_ERROR](state, errorResponse) {
    state.token = null
    state.status = 'error'
  },

  [types.REFRESH_SUCCESS](state, token) {
    state.token = token
    state.status = 'success'
  },
}

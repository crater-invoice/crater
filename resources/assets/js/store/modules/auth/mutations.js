import * as types from './mutation-types'

export default {
  [types.AUTH_SUCCESS] (state, token) {
    state.token = token
    state.status = 'success'
  },

  [types.AUTH_LOGOUT] (state) {
    state.token = null
  },

  [types.AUTH_ERROR] (state, errorResponse) {
    state.token = null
    state.status = 'error'
  },

  [types.REFRESH_SUCCESS] (state, token) {
    state.token = token
    state.status = 'success'
  }
}

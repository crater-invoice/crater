import * as types from './mutation-types'

export default {
  [types.SET_USER] (state, data) {
    state.user = data
  },

  [types.UPDATE_USER] (state, data) {
    state.user = data
  },

  [types.UPDATE_USER_AVATAR] (state, data) {
    state.user.avatar = data.avatar
  }
}

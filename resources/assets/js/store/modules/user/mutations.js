import * as types from './mutation-types'

export default {
  [types.RESET_CURRENT_USER](state, user) {
    state.currentUser = null
  },

  [types.BOOTSTRAP_CURRENT_USER](state, user) {
    state.currentUser = user
  },

  [types.UPDATE_CURRENT_USER](state, user) {
    state.currentUser = user
  },

  [types.UPDATE_USER_AVATAR](state, data) {
    if (state.currentUser) {
      state.currentUser.avatar = data.avatar
    }
  },

  [types.SET_DEFAULT_LANGUAGE](state, data) {
    window.i18n.locale = data
  },
}

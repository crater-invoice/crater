import * as types from './mutation-types'

export default {
  [types.SHOW_NOTIFICATION](state, data) {
    state.active = data
  },

  [types.HIDE_NOTIFICATION](state, data) {
    state.active = data
  },

  [types.SET_TITLE](state, data) {
    state.title = data
  },

  [types.SET_AUTO_HIDE](state, data) {
    state.autoHide = data
  },

  [types.SET_MESSAGE](state, data) {
    state.message = data
  },

  [types.SET_NOTIFICATION_TYPE](state, data) {
    state.type = data
  },

  [types.RESET_DATA](state) {
    state.active = false
    state.description = ''
    state.title = ''
  },
}

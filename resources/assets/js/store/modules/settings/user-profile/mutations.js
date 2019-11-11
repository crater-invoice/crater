import * as types from './mutation-types'

export default {
  [types.SET_USER] (state, data) {
    state.user = data.user
  },

  [types.UPDATE_USER] (state, data) {
    state.user = data
  }
}

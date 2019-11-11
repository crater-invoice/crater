import * as types from './mutation-types'

export default {
  [types.SET_COMPANY] (state, data) {
    state.company = data.company
  },

  [types.UPDATE_COMPANY] (state, data) {
    state.company = data
  }
}

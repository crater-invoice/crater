import * as types from './mutation-types'

export default {
  [types.SET_MOMENT_DATE_FORMAT] (state, data) {
    state.momentDateFormat = data
  }
}

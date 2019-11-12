import * as types from './mutation-types'

export default {
  [types.SET_MOMENT_DATE_FORMAT] (state, data) {
    state.momentDateFormat = data
  },
  [types.SET_LANGUAGE_FORMAT] (state, data) {
    window.i18n.locale = data
  }
}

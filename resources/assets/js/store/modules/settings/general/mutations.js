import * as types from './mutation-types'

export default {
  [types.SET_INITIAL_DATA] (state, data) {
    state.currencies = data.currencies
    state.time_zones = data.time_zones
    state.languages = data.languages
    state.date_formats = data.date_formats
  },

  [types.SET_ITEM_DISCOUNT] (state) {
    state.item_discount = true
  }
}

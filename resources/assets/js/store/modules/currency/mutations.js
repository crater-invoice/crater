import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_CURRENCIES] (state, data) {
    state.currencies = data.currencies
    state.currencyId = data.currencyId
  },

  [types.SET_DEFAULT_CURRENCY] (state, data) {
    state.defaultCurrency = data.default_currency
  },

  [types.ADD_CURRENCY] (state, data) {
    state.currencies.push(data)
  }
}

import * as types from './mutation-types'

export default {
  [types.SET_INVOICES] (state, data) {
    state.invoices = data.invoices
  }
}

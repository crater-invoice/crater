import * as types from './mutation-types'

export default {
  [types.SET_ESTIMATES] (state, data) {
    state.estimates = data.estimates
  }
}

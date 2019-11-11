import * as types from './mutation-types'

export default {
  [types.UPDATE_APP_LOADING_STATUS]: (state, data) => {
    state.isAppLoaded = data
  }
}

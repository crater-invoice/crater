import * as types from './mutation-types'

export default {
  [types.SHOW_MODAL](state, data) {
    state.active = data
  },

  [types.HIDE_MODAL](state, data) {
    state.active = data
  },

  [types.SET_TITLE](state, data) {
    state.title = data
  },

  [types.SET_COMPONENT_NAME](state, data) {
    state.componentName = data
  },

  [types.SET_ID](state, data) {
    state.id = data
  },

  [types.SET_DATA](state, data) {
    state.data = data
  },

  [types.SET_SIZE](state, size) {
    state.size = size
  },

  [types.SET_REFRESH_DATA](state, refreshData) {
    state.refreshData = refreshData
  },

  [types.RESET_DATA](state) {
    state.content = ''
    state.title = ''
    state.componentName = ''
    state.id = ''
    state.data = null
    state.refreshData = null
  },

  [types.SET_VARIANT](state, data) {
    state.variant = data
  },
}

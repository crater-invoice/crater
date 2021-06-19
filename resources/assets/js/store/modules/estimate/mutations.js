import * as types from './mutation-types'

export default {
  [types.SET_ESTIMATES](state, data) {
    state.estimates = data
  },

  [types.SET_TOTAL_ESTIMATES](state, totalEstimates) {
    state.totalEstimates = totalEstimates
  },

  [types.ADD_ESTIMATE](state, data) {
    state.estimates = [...state.estimates, data]
  },

  [types.DELETE_ESTIMATE](state, id) {
    let index = state.estimates.findIndex((estimate) => estimate.id === id)
    state.estimates.splice(index, 1)
  },

  [types.SET_SELECTED_ESTIMATES](state, data) {
    state.selectedEstimates = data
  },

  [types.DELETE_MULTIPLE_ESTIMATES](state, selectedEstimates) {
    selectedEstimates.forEach((estimate) => {
      let index = state.estimates.findIndex((_est) => _est.id === estimate.id)
      state.estimates.splice(index, 1)
    })

    state.selectedEstimates = []
  },

  [types.UPDATE_ESTIMATE](state, data) {
    let pos = state.estimates.findIndex(
      (estimate) => estimate.id === data.estimate.id
    )

    state.estimates[pos] = data.estimate
  },

  [types.UPDATE_ESTIMATE_STATUS](state, data) {
    let pos = state.estimates.findIndex((estimate) => estimate.id === data.id)
    if (state.estimates[pos]) {
      // state.estimates[pos] = { ...state.estimates[pos], status: data.status }
      state.estimates[pos].status = data.status
    }
  },

  [types.RESET_SELECTED_ESTIMATES](state, data) {
    state.selectedEstimates = []
    state.selectAllField = false
  },

  [types.SET_TEMPLATE_NAME](state, templateName) {
    state.estimateTemplateName = templateName
  },

  [types.SELECT_CUSTOMER](state, data) {
    state.selectedCustomer = data
  },

  [types.RESET_SELECTED_CUSTOMER](state, data) {
    state.selectedCustomer = null
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },

  [types.VIEW_ESTIMATE](state, estimate) {
    state.selectedEstimate = estimate
  },

  [types.SET_SELECTED_NOTE](state, data) {
    state.selectedNote = data
  },

  [types.RESET_SELECTED_NOTE](state, data) {
    state.selectedNote = null
  },
}

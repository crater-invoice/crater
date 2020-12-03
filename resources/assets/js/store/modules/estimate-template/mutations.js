import * as types from './mutation-types'

export default {
  [types.SET_ESTIMATE_TEMPLATES](state, templates) {
    state.estimateTemplates = templates
  },
}

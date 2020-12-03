import * as types from './mutation-types'

export default {
  [types.SET_TAX_TYPES](state, taxTypes) {
    state.taxTypes = taxTypes
  },

  [types.ADD_TAX_TYPE](state, data) {
    state.taxTypes.push(data.taxType)
  },

  [types.UPDATE_TAX_TYPE](state, data) {
    let pos = state.taxTypes.findIndex(
      (taxType) => taxType.id === data.taxType.id
    )
    Object.assign(state.taxTypes[pos], { ...data.taxType })
  },

  [types.DELETE_TAX_TYPE](state, id) {
    let pos = state.taxTypes.findIndex((taxType) => taxType.id === id)
    state.taxTypes.splice(pos, 1)
  },
}

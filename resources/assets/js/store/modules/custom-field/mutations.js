import * as types from './mutation-types'

export default {
  [types.SET_CUSTOM_FIELDS](state, fields) {
    state.customFields = fields
  },

  [types.ADD_CUSTOM_FIELDS](state, field) {
    field = {
      ...field,
      options: field.options.map((option) => {
        return { name: option ? option : '' }
      }),
    }

    state.customFields = [...state.customFields, field]
  },

  [types.UPDATE_CUSTOM_FIELDS](state, field) {
    field = {
      ...field,
      options: field.options.map((option) => {
        return { name: option ? option : '' }
      }),
    }

    let pos = state.customFields.findIndex((_f) => _f.id === field.id)

    if (state.customFields[pos]) {
      state.customFields[pos] = field
    }
  },

  [types.DELETE_CUSTOM_FIELDS](state, id) {
    let index = state.customFields.findIndex((field) => field.id === id)
    state.customFields.splice(index, 1)
  },

  [types.SET_REQUEST_STATE](state, flag) {
    state.isRequestOngoing = flag
  },
}

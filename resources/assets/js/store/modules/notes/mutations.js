import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_NOTES](state, notes) {
    state.notes = notes
  },

  [types.ADD_NOTE](state, data) {
    state.notes.push(data.note)
  },

  [types.UPDATE_NOTE](state, data) {
    let pos = state.notes.findIndex((note) => note.id === data.note.id)

    state.notes[pos] = data.note
  },

  [types.DELETE_NOTE](state, id) {
    let index = state.notes.findIndex((note) => note.id === id)
    state.notes.splice(index, 1)
  },
}

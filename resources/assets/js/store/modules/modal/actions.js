import * as types from './mutation-types'

export const openModal = ({ commit, dispatch, state }, payload) => {
  commit(types.SET_COMPONENT_NAME, payload.componentName)
  commit(types.SHOW_MODAL, true)

  if (payload.id) {
    commit(types.SET_ID, payload.id)
  }
  commit(types.SET_TITLE, payload.title)

  if (payload.data) {
    commit(types.SET_DATA, payload.data)
  }

  if (payload.refreshData) {
    commit(types.SET_REFRESH_DATA, payload.refreshData)
  }

  if (payload.variant) {
    commit(types.SET_VARIANT, payload.variant)
  }

  if (payload.size) {
    commit(types.SET_SIZE, payload.size)
  }
}

export const closeModal = ({ commit, dispatch, state }) => {
  commit(types.RESET_DATA)
  commit(types.HIDE_MODAL, false)
}

export const resetModalData = ({ commit, dispatch, state }) => {
  commit(types.RESET_DATA)
}

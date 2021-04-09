import * as types from './mutation-types'

export const showNotification = ({ commit, dispatch, state }, payload) => {
  commit(types.SHOW_NOTIFICATION, true)

  if (payload.type) {
    commit(types.SET_NOTIFICATION_TYPE, payload.type)
  }
  if (payload.title) {
    commit(types.SET_TITLE, payload.title)
  }
  if (payload.autoHide) {
    commit(types.SET_AUTO_HIDE, payload.autoHide)
  }
  if (payload.message) {
    commit(types.SET_MESSAGE, payload.message)
  }
}

export const hideNotification = ({ commit, dispatch, state }) => {
  commit(types.RESET_DATA)
  commit(types.HIDE_NOTIFICATION, false)
}

export const resetNotificationData = ({ commit, dispatch, state }) => {
  commit(types.RESET_DATA)
}

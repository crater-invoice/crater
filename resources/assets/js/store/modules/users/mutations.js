import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_USERS](state, users) {
    state.users = users
  },

  [types.SET_TOTAL_USERS](state, totalUsers) {
    state.totalUsers = totalUsers
  },

  [types.ADD_USER](state, data) {
    state.users.push(data.user)
  },

  [types.UPDATE_USER](state, data) {
    let pos = state.users.findIndex((user) => user.id === data.user.id)

    state.users[pos] = data.user
  },

  [types.DELETE_USER](state, id) {
    let index = state.users.findIndex((user) => user.id === id[0])
    state.users.splice(index, 1)
  },

  [types.DELETE_MULTIPLE_USERS](state, selectedUsers) {
    selectedUsers.forEach((user) => {
      let index = state.users.findIndex((_user) => _user.id === user.id)
      state.users.splice(index, 1)
    })

    state.selectedUsers = []
  },

  [types.SET_SELECTED_USERS](state, data) {
    state.selectedUsers = data
  },

  [types.SET_SELECT_ALL_STATE](state, data) {
    state.selectAllField = data
  },
}

import * as types from './mutation-types'
import * as searchTypes from '../search/mutation-types'

export const fetchUsers = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/users`, { params })
      .then((response) => {
        commit(types.BOOTSTRAP_USERS, response.data.users.data)
        commit(types.SET_TOTAL_USERS, response.data.users.total)
        commit(
          'search/' + searchTypes.SET_USER_LISTS,
          response.data.users.data,
          { root: true }
        )
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchUser = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/users/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addUser = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/users', data)
      .then((response) => {
        commit(types.ADD_USER, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateUser = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/users/${data.id}`, data)
      .then((response) => {
        if (response.data.success) {
          commit(types.UPDATE_USER, response.data)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteUser = ({ commit, dispatch, state }, user) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/users/delete`, {
        users: user,
      })
      .then((response) => {
        commit(types.DELETE_USER, user)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteMultipleUsers = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/users/delete`, { users: state.selectedUsers })
      .then((response) => {
        commit(types.DELETE_MULTIPLE_USERS, state.selectedUsers)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const setSelectAllState = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECT_ALL_STATE, data)
}

export const selectAllUsers = ({ commit, dispatch, state }) => {
  if (state.selectedUsers.length === state.users.length) {
    commit(types.SET_SELECTED_USERS, [])
    commit(types.SET_SELECT_ALL_STATE, false)
  } else {
    let allUserIds = state.users.map((user) => user.id)
    commit(types.SET_SELECTED_USERS, allUserIds)
    commit(types.SET_SELECT_ALL_STATE, true)
  }
}

export const selectedUser = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_USERS, data)
  if (state.selectedUsers.length === state.users.length) {
    commit(types.SET_SELECT_ALL_STATE, true)
  } else {
    commit(types.SET_SELECT_ALL_STATE, false)
  }
}

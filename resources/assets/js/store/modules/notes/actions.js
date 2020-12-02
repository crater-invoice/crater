import * as types from './mutation-types'

export const fetchNotes = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/notes`, { params })
      .then((response) => {
        commit(types.BOOTSTRAP_NOTES, response.data.notes.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchNote = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/notes/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addNote = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/notes', data)
      .then((response) => {
        commit(types.ADD_NOTE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateNote = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/notes/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_NOTE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteNote = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/notes/${id}`)
      .then((response) => {
        commit(types.DELETE_NOTE, id)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

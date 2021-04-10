import * as types from './mutation-types'

export const fetchCustomFields = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/custom-fields`, { params })
      .then((response) => {
        commit(types.SET_CUSTOM_FIELDS, response.data.customFields.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchNoteCustomFields = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    if (state.isRequestOngoing) {
      resolve({ requestOnGoing: true })
      return true
    }
    commit(types.SET_REQUEST_STATE, true)
    window.axios
      .get(`/api/v1/custom-fields`, { params })
      .then((response) => {
        commit(types.SET_CUSTOM_FIELDS, response.data.customFields.data)
        commit(types.SET_REQUEST_STATE, false)
        resolve(response)
      })
      .catch((err) => {
        commit(types.SET_REQUEST_STATE, false)
        reject(err)
      })
  })
}

export const fetchCustomField = ({ commit, dispatch }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/custom-fields/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addCustomField = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post(`/api/v1/custom-fields`, params)
      .then((response) => {
        commit(types.ADD_CUSTOM_FIELDS, response.data.customField)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateCustomField = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/custom-fields/${params.id}`, params)
      .then((response) => {
        commit(types.UPDATE_CUSTOM_FIELDS, params)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteCustomFields = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/custom-fields/${id}`)
      .then((response) => {
        commit(types.DELETE_CUSTOM_FIELDS, id)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

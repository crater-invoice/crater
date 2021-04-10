import * as types from './mutation-types'

export const fetchTaxTypes = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-types`, { params })
      .then((response) => {
        commit(types.SET_TAX_TYPES, response.data.taxTypes.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchTaxTypesList = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-types?limit=500`)
      .then((response) => {
        commit(types.SET_TAX_TYPES, response.data.taxTypes)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const addTaxType = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/tax-types', data)
      .then((response) => {
        commit(types.ADD_TAX_TYPE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchTaxType = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/tax-types/${id}`)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateTaxType = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put(`/api/v1/tax-types/${data.id}`, data)
      .then((response) => {
        commit(types.UPDATE_TAX_TYPE, response.data)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const deleteTaxType = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios
      .delete(`/api/v1/tax-types/${id}`)
      .then((response) => {
        if (response.data.success) {
          commit(types.DELETE_TAX_TYPE, id)
        }
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

import * as types from './mutation-types'

export const addTaxType = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.post('/api/tax-types', data).then((response) => {
      commit(types.ADD_TAX_TYPE, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const fetchTaxType = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.get(`/api/tax-types/${id}/edit`).then((response) => {
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const updateTaxType = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios.put(`/api/tax-types/${data.id}`, data).then((response) => {
      commit(types.UPDATE_TAX_TYPE, response.data)
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

export const deleteTaxType = ({ commit, dispatch, state }, id) => {
  return new Promise((resolve, reject) => {
    window.axios.delete(`/api/tax-types/${id}`).then((response) => {
      if (response.data.success) {
        commit(types.DELETE_TAX_TYPE, id)
      }
      resolve(response)
    }).catch((err) => {
      reject(err)
    })
  })
}

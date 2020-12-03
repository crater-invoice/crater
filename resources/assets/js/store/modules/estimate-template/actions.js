import * as types from './mutation-types'

export const fetchEstimateTemplates = ({ commit, dispatch, state }, params) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get(`/api/v1/estimates/templates`, { params })
      .then((response) => {
        commit(types.SET_ESTIMATE_TEMPLATES, response.data.templates)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

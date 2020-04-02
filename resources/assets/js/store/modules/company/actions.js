import * as types from './mutation-types'

export const setSelectedCompany = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_COMPANY, data)
}

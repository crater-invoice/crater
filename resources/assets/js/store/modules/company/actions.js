import * as types from './mutation-types'
import Ls from '@/services/ls'

export const setSelectedCompany = ({ commit, dispatch, state }, data) => {
  Ls.set('selectedCompany', data.id)
  commit(types.SET_SELECTED_COMPANY, data)
}

import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'
import Ls from '@/services/ls'

const initialState = {
  token: Ls.get('auth.token'),
  status: '',
  validateTokenError: '',
  validateTokenSuccess: ''
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations
}

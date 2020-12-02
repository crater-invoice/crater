import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  users: [],
  totalUsers: 0,
  selectAllField: false,
  selectedUsers: [],
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

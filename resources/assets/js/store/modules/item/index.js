import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  items: [],
  totalItems: 0,
  selectAllField: false,
  selectedItems: [],
  itemUnits: []
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations
}

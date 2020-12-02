import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  customers: [],
  totalCustomers: 0,
  selectAllField: false,
  selectedCustomers: [],
  selectedViewCustomer: {},
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

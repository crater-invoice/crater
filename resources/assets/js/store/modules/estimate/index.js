import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  estimates: [],
  estimateTemplateId: 1,
  selectAllField: false,
  selectedEstimates: [],
  totalEstimates: 0,
  selectedCustomer: null,
  selectedEstimate: null,
  selectedNote: null,
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

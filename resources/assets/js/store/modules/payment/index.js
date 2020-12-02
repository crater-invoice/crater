import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  payments: [],
  totalPayments: 0,
  selectAllField: false,
  selectedPayments: [],
  paymentModes: [],
  selectedNote: null,
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  companies: [],

  selectedCompany: null,

  company: null,

  momentDateFormat: null,

  carbonDateFormat: null,

  item_discount: false,

  defaultFiscalYear: null,

  defaultTimeZone: null,

  defaultCurrency: null,
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

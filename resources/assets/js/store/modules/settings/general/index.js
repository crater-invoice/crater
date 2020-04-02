import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  currencies: null,
  time_zones: null,
  languages: null,
  date_formats: null,
  item_discount: false
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations
}

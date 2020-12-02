import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  active: false,
  content: '',
  title: '',
  componentName: '',
  id: '',
  size: 'md',
  data: null,
  refreshData: null,
  variant: '',
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}

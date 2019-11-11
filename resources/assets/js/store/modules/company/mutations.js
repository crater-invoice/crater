import * as types from './mutation-types'

export default {
  [types.BOOTSTRAP_COMPANIES] (state, companies) {
    state.companies = companies
    state.selectedCompany = companies[0]
  },
  [types.SET_SELECTED_COMPANY] (state, company) {
    state.selectedCompany = company
  }
}

import * as types from './mutation-types'
import Ls from '@/services/ls'

export default {
  [types.SET_SELECTED_COMPANY](state, company) {
    Ls.set('selectedCompany', company.id)
    state.selectedCompany = company
  },

  [types.SET_MOMENT_DATE_FORMAT](state, data) {
    state.momentDateFormat = data
  },

  [types.SET_CARBON_DATE_FORMAT](state, data) {
    state.carbonDateFormat = data
  },

  [types.SET_ITEM_DISCOUNT](state) {
    state.item_discount = true
  },

  [types.SET_DEFAULT_FISCAL_YEAR](state, data) {
    state.defaultFiscalYear = data
  },

  [types.SET_DEFAULT_TIME_ZONE](state, data) {
    state.defaultTimeZone = data
  },

  [types.SET_DEFAULT_CURRENCY](state, data) {
    state.defaultCurrency = data.default_currency
  },
}

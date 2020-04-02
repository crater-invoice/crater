import Vue from 'vue'
import Vuex from 'vuex'

import * as getters from './getters'
import mutations from './mutations'
import actions from './actions'

import auth from './modules/auth'
import user from './modules/user'
import category from './modules/category'
import customer from './modules/customer'
import company from './modules/company'
import companyInfo from './modules/settings/company-info'
import dashboard from './modules/dashboard'
import estimate from './modules/estimate'
import expense from './modules/expense'
import invoice from './modules/invoice'
import userProfile from './modules/settings/user-profile'
import payment from './modules/payment'
import preferences from './modules/settings/preferences'
import item from './modules/item'
import modal from './modules/modal'
import currency from './modules/currency'
import general from './modules/settings/general'
import taxType from './modules/tax-type'
import profitLossReport from './modules/reports/profit-loss'
import salesReport from './modules/reports/sales'
import ExpensesReport from './modules/reports/expense'
import TaxReport from './modules/reports/tax'

Vue.use(Vuex)

const initialState = {
  isAppLoaded: false
}

export default new Vuex.Store({
  strict: true,
  state: initialState,
  getters,
  mutations,
  actions,

  modules: {
    auth,
    user,
    category,
    company,
    companyInfo,
    customer,
    dashboard,
    estimate,
    item,
    invoice,
    expense,
    modal,
    userProfile,
    currency,
    payment,
    preferences,
    general,
    taxType,
    profitLossReport,
    salesReport,
    ExpensesReport,
    TaxReport
  }
})

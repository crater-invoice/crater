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
import dashboard from './modules/dashboard'
import estimate from './modules/estimate'
import expense from './modules/expense'
import invoice from './modules/invoice'
import payment from './modules/payment'
import item from './modules/item'
import modal from './modules/modal'
import customFields from './modules/custom-field'
import taxType from './modules/tax-type'
import users from './modules/users'
import backup from './modules/backup'
import disks from './modules/disk'
import estimateTemplate from './modules/estimate-template'
import invoiceTemplate from './modules/invoice-template'
import search from './modules/search'
import notes from './modules/notes'
import notification from './modules/notification'

Vue.use(Vuex)

const initialState = {
  languages: [],

  timeZones: [],

  dateFormats: [],

  fiscalYears: [],

  currencies: [],

  countries: [],

  isAppLoaded: false,

  isSidebarOpen: false,
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
    customer,
    dashboard,
    estimate,
    item,
    invoice,
    expense,
    modal,
    customFields,
    payment,
    taxType,
    users,
    backup,
    disks,
    estimateTemplate,
    invoiceTemplate,
    search,
    notes,
    notification,
  },
})

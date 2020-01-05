import VueRouter from 'vue-router'
import Vuex from 'vuex'
import Ls from './services/ls'
import store from './store/index.js'
import Vue from 'vue'
import Vuelidate from 'vuelidate'
import VDropdown from './components/dropdown/VDropdown.vue'
import VDropdownItem from './components/dropdown/VDropdownItem.vue'
import VDropdownDivider from './components/dropdown/VDropdownDivider.vue'
import DotIcon from './components/icon/DotIcon.vue'
import CustomerModal from './components/base/modal/CustomerModal.vue'
import TaxTypeModal from './components/base/modal/TaxTypeModal.vue'
import CategoryModal from './components/base/modal/CategoryModal.vue'
import money from 'v-money'
import VTooltip from 'v-tooltip'

/**
 * Global css plugins
 */
import 'vue-tabs-component/docs/resources/tabs-component.css'

Vue.use(Vuelidate)

window._ = require('lodash')
/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue')

/**
 * Font Awesome
 */
require('../plugins/vue-font-awesome/index')

/**
 * Custom Directives
 */
require('./helpers/directives')

/**
 * Base Components
 */
require('./components/base')

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

window.axios = require('axios')
window.Ls = Ls
global.$ = global.jQuery = require('jquery')

window.axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
}

/**
 * Interceptors
 */

window.axios.interceptors.request.use(function (config) {
  // Do something before request is sent
  const AUTH_TOKEN = Ls.get('auth.token')
  const companyId = Ls.get('selectedCompany')

  if (AUTH_TOKEN) {
    config.headers.common['Authorization'] = `Bearer ${AUTH_TOKEN}`
  }

  if (companyId) {
    config.headers.common['company'] = companyId
  }

  return config
}, function (error) {
  // Do something with request error
  return Promise.reject(error)
})

/**
 * Global Axios Response Interceptor
 */

global.axios.interceptors.response.use(undefined, function (err) {
  // Do something with request error
  return new Promise((resolve, reject) => {
    console.log(err.response)
    if (err.response.data.error === 'invalid_credentials') {
      window.toastr['error']('Invalid Credentials')
    }
    if (err.response.data && (err.response.statusText === 'Unauthorized' || err.response.data === ' Unauthorized.')) {
      store.dispatch('auth/logout', true)
    } else {
      throw err
    }
  })
})

/**
 * Global plugins
 */
window.toastr = require('toastr')

Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VTooltip)

// register directive v-money and component <money>
Vue.use(money, {precision: 2})

Vue.component('v-dropdown', VDropdown)
Vue.component('v-dropdown-item', VDropdownItem)
Vue.component('v-dropdown-divider', VDropdownDivider)

Vue.component('dot-icon', DotIcon)
Vue.component('customer-modal', CustomerModal)
Vue.component('tax-type-modal', TaxTypeModal)
Vue.component('category-modal', CategoryModal)

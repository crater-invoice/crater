import VueRouter from 'vue-router'
import Vuex from 'vuex'
import Ls from './services/ls'
import store from './store/index.js'
import Vue from 'vue'
import Vuelidate from 'vuelidate'
import money from 'v-money'
import VTooltip from 'v-tooltip'
import Transitions from 'vue2-transitions'
import SpaceWind from '@bytefury/spacewind'
import swal from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

/**
 * Theme
 */
import theme from './components/theme'

/**
 * Global css plugins
 */

Vue.use(SpaceWind, { theme })

Vue.use(Vuelidate)

Vue.use(swal, {
  customClass: {
    container:
      'fixed z-50 inset-0 overflow-y-auto bg-black bg-opacity-25 flex justify-center min-h-screen items-center sm:p-0 swal2-container',
    popup:
      'flex items-center flex-col justify-center align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6',
    header: 'swal2-header',
    title: 'swal2-title',
    closeButton: '',
    icon: 'swal2-icon',
    image: '',
    content: 'swal2-content',
    input: '',
    inputLabel: '',
    validationMessage: '',
    actions: 'swal2-actions',
    confirmButton:
      'w-full inline-flex py-2 px-4 text-sm leading-5 rounded items-center justify-center text-white font-normal transition duration-150 ease-in-out border border-transparent focus:outline-none bg-primary-500 hover:bg-opacity-75 whitespace-nowrap',
    denyButton: '',
    cancelButton:
      'w-full inline-flex py-2 px-4 text-sm leading-5 rounded justify-center items-center focus:outline-none font-normal transition ease-in-out duration-150 border border-transparent border border-solid border-primary-500 text-primary-500 hover:bg-primary-200 shadow-inner whitespace-nowrap',
    loader: '',
    footer: '',
  },
  buttonsStyling: false,
})

Vue.use(Transitions)

window._ = require('lodash')

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
window.axios.defaults.withCredentials = true
window.Ls = Ls

window.axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest',
}

/**
 * Interceptors
 */

window.axios.interceptors.request.use(
  function (config) {
    if (store.getters['auth/isLoggedOut']) {
      let source = window.axios.CancelToken.source()
      config.cancelToken = source.token
      setTimeout(() => {
        store.dispatch('auth/setLogoutFalse')
      }, 200)

      return config
    }
    // Do something before request is sent
    const companyId = Ls.get('selectedCompany')

    if (companyId) {
      config.headers.common['company'] = companyId
    }

    return config
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error)
  }
)

/**
 * Global Axios Response Interceptor
 */
global.axios.interceptors.response.use(undefined, function (err) {
  // Do something with request error
  if (store.getters['auth/isLoggedOut']) {
    return true
  }
  if (!err.response) {
    store.dispatch('notification/showNotification', {
      type: 'error',
      message:
        'Please check your internet connection or wait until servers are back online.',
    })
  } else {
    if (
      err.response.data &&
      err.config.url !== '/api/v1/auth/check' &&
      (err.response.statusText === 'Unauthorized' ||
        err.response.data === ' Unauthorized.')
    ) {
      console.log(err.response)
      // Unauthorized and log out
      store.dispatch('notification/showNotification', {
        type: 'error',
        message: err.response.data.message
          ? err.response.data.message
          : 'Unauthorized',
      })
      store.dispatch('auth/logout', true)
    } else if (err.response.data.errors) {
      // Show a notification per error
      const errors = JSON.parse(JSON.stringify(err.response.data.errors))
      for (const i in errors) {
        store.dispatch('notification/showNotification', {
          type: 'error',
          message: errors[i],
        })
      }
    } else {
      // Unknown error
      store.dispatch('notification/showNotification', {
        type: 'error',
        message: err.response.data.message
          ? err.response.data.message
          : err.response.data || 'Unknown error occurred',
      })
    }
  }
  return Promise.reject(err)
})

/**
 * Global plugins
 */
Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VTooltip)

// register directive v-money and component <money>
Vue.use(money, { precision: 2 })

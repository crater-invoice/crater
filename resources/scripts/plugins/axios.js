import axios from 'axios'
import Ls from '@/scripts/services/ls.js'

window.Ls = Ls
window.axios = axios
axios.defaults.withCredentials = true

axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest',
}

/**
 * Interceptors
 */

axios.interceptors.request.use(function (config) {
  // Pass selected company to header on all requests
  const companyId = Ls.get('selectedCompany')

  if (companyId) {
    config.headers.common['company'] = companyId
  }

  return config
})

import * as types from './mutation-types'
import * as userTypes from './modules/user/mutation-types'
import * as companyTypes from './modules/company/mutation-types'

export default {
  bootstrap({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/bootstrap')
        .then((response) => {
          commit('user/' + userTypes.BOOTSTRAP_CURRENT_USER, response.data.user)

          commit(
            'company/' + companyTypes.SET_SELECTED_COMPANY,
            response.data.company
          )

          commit('company/' + companyTypes.SET_DEFAULT_CURRENCY, response.data)

          commit(
            'user/' + userTypes.SET_DEFAULT_LANGUAGE,
            response.data.default_language
          )

          commit(
            'company/' + companyTypes.SET_MOMENT_DATE_FORMAT,
            response.data.moment_date_format
          )

          commit(
            'company/' + companyTypes.SET_CARBON_DATE_FORMAT,
            response.data.carbon_date_format
          )

          commit(
            'company/' + companyTypes.SET_DEFAULT_FISCAL_YEAR,
            response.data.fiscal_year
          )

          commit(
            'company/' + companyTypes.SET_DEFAULT_TIME_ZONE,
            response.data.time_zone
          )

          commit(types.SET_CURRENCIES, response.data.currencies)

          commit(types.SET_COUNTRIES, response.data.countries)

          commit(types.UPDATE_APP_LOADING_STATUS, true)

          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  fetchLanguages({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/languages')
        .then((response) => {
          commit(types.SET_LANGUAGES, response.data.languages)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  fetchCurrencies({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/currencies')
        .then((response) => {
          commit(types.SET_CURRENCIES, response.data.currencies)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  fetchDateFormats({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/date/formats')
        .then((response) => {
          commit(types.SET_DATE_FORMATS, response.data.date_formats)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  fetchFiscalYears({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/fiscal/years')
        .then((response) => {
          commit(types.SET_FISCAL_YEARS, response.data.fiscal_years)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  fetchTimeZones({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/timezones')
        .then((response) => {
          commit(types.SET_TIMEZONES, response.data.time_zones)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  fetchCountries({ commit, dispatch, state }) {
    return new Promise((resolve, reject) => {
      window.axios
        .get('/api/v1/countries')
        .then((response) => {
          commit(types.SET_COUNTRIES, response.data.countries)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  toggleSidebar({ commit }) {
    commit(types.TOGGLE_SIDEBAR)
  },
}

import * as types from './mutation-types'

export default {
  [types.UPDATE_APP_LOADING_STATUS]: (state, data) => {
    state.isAppLoaded = data
  },

  [types.SET_LANGUAGES](state, languages) {
    state.languages = languages
  },

  [types.SET_CURRENCIES](state, currencies) {
    state.currencies = currencies
  },

  [types.SET_TIMEZONES](state, timeZones) {
    state.timeZones = timeZones
  },

  [types.SET_DATE_FORMATS](state, dateFormats) {
    state.dateFormats = dateFormats
  },

  [types.SET_FISCAL_YEARS](state, fiscalYears) {
    state.fiscalYears = fiscalYears
  },

  [types.SET_COUNTRIES](state, countries) {
    state.countries = countries
  },

  [types.TOGGLE_SIDEBAR](state) {
    state.isSidebarOpen = !state.isSidebarOpen
  },
}

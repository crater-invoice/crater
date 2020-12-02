import * as types from './mutation-types'

export const setSelectedCompany = ({ commit, dispatch, state }, data) => {
  commit(types.SET_SELECTED_COMPANY, data)
}

export const updateCompany = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .put('/api/v1/company', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateCompanyLogo = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/company/upload-logo', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchCompanySettings = ({ commit, dispatch, state }, settings) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get('/api/v1/company/settings', {
        params: {
          settings,
        },
      })
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateCompanySettings = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/company/settings', data)
      .then((response) => {
        commit(types.SET_CARBON_DATE_FORMAT, data.settings.carbon_date_format)
        commit(types.SET_MOMENT_DATE_FORMAT, data.settings.moment_date_format)
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const setItemDiscount = ({ commit, dispatch, state }) => {
  commit(types.SET_ITEM_DISCOUNT)
}

export const fetchMailDrivers = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get('/api/v1/mail/drivers')
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const fetchMailConfig = ({ commit, dispatch, state }) => {
  return new Promise((resolve, reject) => {
    window.axios
      .get('/api/v1/mail/config')
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const updateMailConfig = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/mail/config', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const sendTestMail = ({ commit, dispatch, state }, data) => {
  return new Promise((resolve, reject) => {
    window.axios
      .post('/api/v1/mail/test', data)
      .then((response) => {
        resolve(response)
      })
      .catch((err) => {
        reject(err)
      })
  })
}

export const setDefaultCurrency = ({ commit, dispatch, state }, data) => {
  commit(types.SET_DEFAULT_CURRENCY, { default_currency: data })
}

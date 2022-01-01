import axios from 'axios'
import { defineStore } from 'pinia'
import { useCompanyStore } from './company'
import { handleError } from '@/scripts/helpers/error-handling'

export const useInstallationStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n
  const companyStore = useCompanyStore()

  return defineStoreFunc({
    id: 'installation',

    state: () => ({
      currentDataBaseData: {
        database_connection: 'mysql',
        database_hostname: '127.0.0.1',
        database_port: '3306',
        database_name: null,
        database_username: null,
        database_password: null,
        app_url: window.location.origin,
      },
    }),

    actions: {
      fetchInstallationRequirements() {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/installation/requirements`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchInstallationStep() {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/installation/wizard-step`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addInstallationStep(data) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/installation/wizard-step`, data)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchInstallationPermissions() {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/installation/permissions`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchInstallationDatabase(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/installation/database/config`, { params })
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addInstallationDatabase(data) {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/installation/database/config`, data)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addInstallationFinish() {
        return new Promise((resolve, reject) => {
          axios
            .post(`/api/v1/installation/finish`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      setInstallationDomain(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/installation/set-domain`, data)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      installationLogin() {
        return new Promise((resolve, reject) => {
          axios.get('/sanctum/csrf-cookie').then((response) => {
            if (response) {
              axios
                .post('/api/v1/installation/login')
                .then((response) => {
                  companyStore.setSelectedCompany(response.data.company)
                  resolve(response)
                })
                .catch((err) => {
                  handleError(err)
                  reject(err)
                })
            }
          })
        })
      },

      checkAutheticated() {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/auth/check`)
            .then((response) => {
              resolve(response)
            })
            .catch((err) => {
              reject(err)
            })
        })
      },
    },
  })()
}

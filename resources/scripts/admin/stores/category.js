import axios from 'axios'
import { defineStore } from 'pinia'
import { useNotificationStore } from '@/scripts/stores/notification'
import { handleError } from '@/scripts/helpers/error-handling'

export const useCategoryStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'category',

    state: () => ({
      categories: [],
      currentCategory: {
        id: null,
        name: '',
        description: '',
      },
      editCategory: null
    }),

    getters: {
      isEdit: (state) => (state.currentCategory.id ? true : false),
    },

    actions: {
      fetchCategories(params) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/categories`, { params })
            .then((response) => {
              this.categories = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      fetchCategory(id) {
        return new Promise((resolve, reject) => {
          axios
            .get(`/api/v1/categories/${id}`)
            .then((response) => {
              this.currentCategory = response.data.data
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      addCategory(data) {
        return new Promise((resolve, reject) => {
          axios
            .post('/api/v1/categories', data)
            .then((response) => {
              this.categories.push(response.data.data)
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.expense_category.created_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      updateCategory(data) {
        return new Promise((resolve, reject) => {
          axios
            .put(`/api/v1/categories/${data.id}`, data)
            .then((response) => {
              if (response.data) {
                let pos = this.categories.findIndex(
                  (category) => category.id === response.data.data.id
                )
                this.categories[pos] = data.categories
                const notificationStore = useNotificationStore()
                notificationStore.showNotification({
                  type: 'success',
                  message: global.t(
                    'settings.expense_category.updated_message'
                  ),
                })
              }
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              reject(err)
            })
        })
      },

      deleteCategory(id) {
        return new Promise((resolve) => {
          axios
            .delete(`/api/v1/categories/${id}`)
            .then((response) => {
              let index = this.categories.findIndex(
                (category) => category.id === id
              )
              this.categories.splice(index, 1)
              const notificationStore = useNotificationStore()
              notificationStore.showNotification({
                type: 'success',
                message: global.t('settings.expense_category.deleted_message'),
              })
              resolve(response)
            })
            .catch((err) => {
              handleError(err)
              console.error(err)
            })
        })
      },
    },
  })()
}

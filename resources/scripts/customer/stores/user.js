import { handleError } from '@/scripts/customer/helpers/error-handling'
const { defineStore } = window.pinia
import { useNotificationStore } from '@/scripts/stores/notification'
import stubs from '@/scripts/customer/stubs/address'
import axios from 'axios'
import { useGlobalStore } from '@/scripts/customer/stores/global'

export const useUserStore = defineStore({
  id: 'customerUserStore',

  state: () => ({
    customers: [],
    userForm: {
      avatar: null,
      name: '',
      email: '',
      password: '',
      company: '',
      confirm_password: '',
      billing: {
        ...stubs,
      },
      shipping: {
        ...stubs,
      },
    },
  }),

  actions: {
    copyAddress() {
      this.userForm.shipping = {
        ...this.userForm.billing,
        type: 'shipping',
      }
    },

    fetchCurrentUser() {
      const globalStore = useGlobalStore()
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/${globalStore.companySlug}/customer/me`)
          .then((response) => {
            Object.assign(this.userForm, response.data.data)
            resolve(response)
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },

    updateCurrentUser({ data, message }) {
      const globalStore = useGlobalStore()
      return new Promise((resolve, reject) => {
        axios
          .post(`/api/v1/${globalStore.companySlug}/customer/profile`, data)
          .then((response) => {
            this.userForm = response.data.data
            globalStore.currentUser = response.data.data
            resolve(response)

            if (message) {
              const notificationStore = useNotificationStore(true)
              notificationStore.showNotification({
                type: 'success',
                message: message,
              })
            }
          })
          .catch((err) => {
            handleError(err)
            reject(err)
          })
      })
    },
  },
})

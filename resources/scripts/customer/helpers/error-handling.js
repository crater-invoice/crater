import { useAuthStore } from '@/scripts/customer/stores/auth'
import { useNotificationStore } from '@/scripts/stores/notification'

export const handleError = (err) => {
  const authStore = useAuthStore()
  const notificationStore = useNotificationStore()

  if (!err.response) {
    notificationStore.showNotification({
      type: 'error',
      message:
        'Please check your internet connection or wait until servers are back online.',
    })
  } else {
    if (
      err.response.data &&
      (err.response.statusText === 'Unauthorized' ||
        err.response.data === ' Unauthorized.')
    ) {
      // Unauthorized and log out
      const msg = err.response.data.message
        ? err.response.data.message
        : 'Unauthorized'

      showToaster(msg)

      authStore.logout()
    } else if (err.response.data.errors) {
      // Show a notification per error
      const errors = JSON.parse(JSON.stringify(err.response.data.errors))
      for (const i in errors) {
        showError(errors[i][0])
      }
    } else if (err.response.data.error) {
      showError(err.response.data.error)
    } else {
      showError(err.response.data.message)
    }
  }
}

export const showError = (error) => {
  switch (error) {
    case 'These credentials do not match our records.':
      showToaster('errors.login_invalid_credentials')
      break

    case 'The email has already been taken.':
      showToaster('validation.email_already_taken')
      break

    case 'invalid_credentials':
      showToaster('errors.invalid_credentials')
      break

    case 'Email could not be sent to this email address.':
      showToaster('errors.email_could_not_be_sent')
      break

    case 'not_allowed':
      showToaster('errors.not_allowed')
      break

    default:
      showToaster(error, false)
      break
  }
}

export const showToaster = (msg, t = true) => {
  const { global } = window.i18n
  const notificationStore = useNotificationStore()

  notificationStore.showNotification({
    type: 'error',
    message: t ? global.t(msg) : msg,
  })
}

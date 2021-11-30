import { defineStore } from 'pinia'

export const useDialogStore = (useWindow = false) => {
  const defineStoreFunc = useWindow ? window.pinia.defineStore : defineStore
  const { global } = window.i18n

  return defineStoreFunc({
    id: 'dialog',
    state: () => ({
      active: false,
      title: '',
      message: '',
      size: 'md',
      data: null,
      variant: 'danger', // primary || danger
      yesLabel: global.t('settings.custom_fields.yes'),
      noLabel: global.t('settings.custom_fields.no'),
      noLabel: 'No',
      resolve: null,
      hideNoButton: false,
    }),
    actions: {
      openDialog(data) {
        this.active = true
        this.title = data.title
        this.message = data.message
        this.size = data.size
        this.data = data.data
        this.variant = data.variant
        this.yesLabel = data.yesLabel
        this.noLabel = data.noLabel
        this.hideNoButton = data.hideNoButton

        return new Promise((resolve, reject) => {
          this.resolve = resolve
        })
      },
      closeDialog() {
        this.active = false

        setTimeout(() => {
          this.title = ''
          this.message = ''
          this.data = null
        }, 300)
      },
    },
  })()
}

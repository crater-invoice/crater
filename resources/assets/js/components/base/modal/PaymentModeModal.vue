<template>
  <form action="" @submit.prevent="submitPaymentMode">
    <div class="p-8 sm:p-6">
      <sw-input-group
        :label="$t('settings.customization.payments.mode_name')"
        :error="nameError"
        variant="horizontal"
        required
      >
        <sw-input
          ref="name"
          :invalid="$v.formData.name.$error"
          v-model="formData.name"
          type="text"
          @input="$v.formData.name.$touch()"
        />
      </sw-input-group>
    </div>
    <div class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid">
      <sw-button
        class="mr-3"
        variant="primary-outline"
        type="button"
        @click="closePaymentModeModal"
      >
        {{ $t('general.cancel') }}
      </sw-button>
      <sw-button :loading="isLoading" variant="primary" type="submit">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ !isEdit ? $t('general.save') : $t('general.update') }}
      </sw-button>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, minLength } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        id: null,
        name: null,
      },
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalDataID',
      'modalData',
      'modalActive',
      'refreshData',
    ]),
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(2),
      },
    },
  },
  async mounted() {
    this.$refs.name.focus = true
    if (this.modalDataID) {
      this.isEdit = true
      this.setData()
    }
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('payment', ['addPaymentMode', 'updatePaymentMode']),
    ...mapActions('notification', ['showNotification']),
    resetFormData() {
      this.formData = {
        id: null,
        name: null,
      }
      this.$v.formData.$reset()
    },
    async submitPaymentMode() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let response

      if (this.isEdit) {
        response = await this.updatePaymentMode(this.formData)
        if (response.data) {
          this.showNotification({
            type: 'success',
            message: this.$t(
              'settings.customization.payments.payment_mode_updated'
            ),
          })
          this.refreshData ? this.refreshData() : ''
          this.closePaymentModeModal()
          return true
        }
        this.showNotification({
          type: 'error',
          message: response.data.error,
        })
      } else {
        try {
          response = await this.addPaymentMode(this.formData)
          if (response.data) {
            this.isLoading = false
            this.showNotification({
              type: 'success',
              message: this.$t(
                'settings.customization.payments.payment_mode_added'
              ),
            })
            this.refreshData ? this.refreshData() : ''
            this.closePaymentModeModal()
            return true
          }
          this.showNotification({
            type: 'error',
            message: response.data.error,
          })
        } catch (err) {
          this.isLoading = false
        }
      }
    },
    async setData() {
      this.formData = {
        id: this.modalData.id,
        name: this.modalData.name,
      }
    },
    closePaymentModeModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>

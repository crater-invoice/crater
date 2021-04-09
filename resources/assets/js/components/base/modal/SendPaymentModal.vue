<template>
  <div>
    <form action="" @submit.prevent="sendPaymentData">
      <div class="px-8 py-8 sm:p-6">
        <sw-input-group
          :label="$t('general.from')"
          :error="fromError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.from"
            :invalid="$v.formData.from.$error"
            type="text"
            @input="$v.formData.from.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('general.to')"
          :error="toError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.to"
            :invalid="$v.formData.to.$error"
            type="text"
            @input="$v.formData.to.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('general.subject')"
          :error="subjectError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-input
            v-model="formData.subject"
            :invalid="$v.formData.subject.$error"
            type="text"
            @input="$v.formData.subject.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('general.body')"
          :error="bodyError"
          class="mb-4"
          variant="vertical"
          required
        >
          <sw-editor
            v-model="formData.body"
            :set-editor="formData.body"
            :invalid="$v.formData.body.$error"
            @input="$v.formData.body.$touch()"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          class="mr-3"
          variant="primary-outline"
          type="button"
          @click="closeSendPaymentModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
        >
          <paper-airplane-icon v-if="!isLoading" class="h-5 mr-2" />
          {{ $t('general.send') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/solid'
const { required, email } = require('vuelidate/lib/validators')
const _ = require('lodash')

export default {
  components: {
    PaperAirplaneIcon,
  },
  data() {
    return {
      isLoading: false,
      formData: {
        from: null,
        to: null,
        subject: null,
        body: null,
      },
    }
  },

  validations: {
    formData: {
      from: {
        required,
        email,
      },
      to: {
        required,
        email,
      },
      subject: {
        required,
      },
      body: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),

    fromError() {
      if (!this.$v.formData.from.$error) {
        return ''
      }

      if (!this.$v.formData.from.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.from.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    toError() {
      if (!this.$v.formData.to.$error) {
        return ''
      }

      if (!this.$v.formData.to.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.to.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    subjectError() {
      if (!this.$v.formData.subject.$error) {
        return ''
      }

      if (!this.$v.formData.subject.required) {
        return this.$tc('validation.required')
      }
    },

    bodyError() {
      if (!this.$v.formData.body.$error) {
        return ''
      }

      if (!this.$v.formData.body.required) {
        return this.$tc('validation.required')
      }
    },
  },

  mounted() {
    this.setInitialData()
  },

  methods: {
    ...mapActions('modal', ['closeModal']),

    ...mapActions('payment', ['sendEmail']),

    ...mapActions('company', ['fetchCompanySettings', 'fetchMailConfig']),

    ...mapActions('notification', ['showNotification']),

    async setInitialData() {
      let admin = await this.fetchMailConfig()

      if (this.modalData) {
        this.formData.from = admin.data.from_mail
        this.formData.to = this.modalData.user.email
      }

      let res = await this.fetchCompanySettings(['payment_mail_body'])

      this.formData.body = res.data.payment_mail_body
    },

    resetFormData() {
      this.formData = {
        from: null,
        to: null,
        subject: null,
        body: null,
      }
    },

    async sendPaymentData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('payments.confirm_send_payment'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            class="w-6 h-6"
            focusable="false"
            data-prefix="fas"
            data-icon="check-circle"
            class="svg-inline--fa fa-check-circle fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#55547A"
              d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        try {
          if (result.value) {
            let data = {
              ...this.formData,
              id: this.modalDataID,
              status: 'SENT',
            }
            this.isLoading = true
            let res = await this.sendEmail(data)

            this.closeModal()
            if (res.data.success) {
              this.isLoading = false
              this.showNotification({
                type: 'success',
                message: this.$tc('payments.send_payment_successfully'),
              })
              return true
            }
            if (res.data.error === 'payments.user_email_does_not_exist') {
              this.showNotification({
                type: 'error',
                message: this.$tc('payments.user_email_does_not_exist'),
              })
              return false
            }
          }
        } catch (error) {
          this.isLoading = false
          this.showNotification({
            type: 'error',
            message: this.$tc('payments.something_went_wrong'),
          })
        }
      })
    },
    closeSendPaymentModal() {
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>

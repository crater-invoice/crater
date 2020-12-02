<template>
  <div>
    <form action="" @submit.prevent="sendInvoiceData">
      <div class="gap-4 px-8 py-8 sm:p-6">
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
          <base-custom-input
            v-model="formData.body"
            :fields="InvoiceMailFields"
            :invalid="$v.formData.body.$error"
            @input="$v.formData.body.$touch()"
            class="mt-2"
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
          @click="closeSendInvoiceModal"
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
      InvoiceMailFields: [
        'customer',
        'customerCustom',
        'invoice',
        'invoiceCustom',
        'company',
      ],
      formData: {
        from: null,
        to: null,
        subject: 'New Invoice',
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

    ...mapGetters('user', ['currentUser']),

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

      if (!this.$v.formData.from.email) {
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

    ...mapActions('invoice', ['sendEmail']),

    ...mapActions('company', ['fetchCompanySettings', 'fetchMailConfig']),

    async setInitialData() {
      let admin = await this.fetchMailConfig()

      if (this.modalData) {
        this.formData.from = admin.data.from_mail
        this.formData.to = this.modalData.user.email
      }

      let res = await this.fetchCompanySettings(['invoice_mail_body'])

      this.formData.body = res.data.invoice_mail_body
    },
    resetFormData() {
      this.formData = {
        from: null,
        to: null,
        subject: null,
        body: null,
      }
    },
    async sendInvoiceData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_send_invoice'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        try {
          if (value) {
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
              window.toastr['success'](
                this.$tc('invoices.send_invoice_successfully')
              )
              return true
            }
            if (res.data.error === 'invoices.user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('invoices.user_email_does_not_exist')
              )
              return false
            }
          }
        } catch (error) {
          this.isLoading = false
          window.toastr['error'](this.$tc('invoices.something_went_wrong'))
        }
      })
    },
    closeSendInvoiceModal() {
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>

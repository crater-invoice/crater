<template>
  <div class="mail-config-modal">
    <form action="" @submit.prevent="onTestMailSend">
      <div class="p-4 md:p-8">
        <sw-input-group
          :label="$t('general.to')"
          :error="emailError"
          class="mt-3"
          variant="horizontal"
          required
        >
          <sw-input
            ref="to"
            :invalid="$v.formData.to.$error"
            v-model="formData.to"
            type="text"
            @input="$v.formData.to.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('general.subject')"
          :error="subjectError"
          class="mt-3"
          variant="horizontal"
          required
        >
          <sw-input
            :invalid="$v.formData.subject.$error"
            v-model="formData.subject"
            type="text"
            @input="$v.formData.subject.$touch()"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('general.message')"
          :error="messageError"
          class="mt-3"
          variant="horizontal"
          required
        >
          <sw-textarea
            v-model="formData.message"
            :invalid="$v.formData.message.$error"
            rows="4"
            cols="50"
            @input="$v.formData.message.$touch()"
          />
        </sw-input-group>
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <sw-button
          variant="primary-outline"
          class="mr-3"
          @click="closeTaxModal"
        >
          {{ $t('general.cancel') }}
        </sw-button>
        <sw-button :loading="isLoading" variant="primary" type="submit">
          <paper-airplane-icon v-if="!isLoading" class="mr-2" />
          {{ !isEdit ? $t('general.send') : $t('general.update') }}
        </sw-button>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/outline'
const {
  required,
  minLength,
  email,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    PaperAirplaneIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        to: null,
        subject: null,
        message: null,
      },
    }
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    emailError() {
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

      if (!this.$v.formData.subject.maxLength) {
        return this.$tc('validation.subject_maxlength')
      }
    },
    messageError() {
      if (!this.$v.formData.message.$error) {
        return ''
      }
      if (!this.$v.formData.message.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.message.maxLength) {
        return this.$tc('validation.message_maxlength')
      }
    },
  },
  validations: {
    formData: {
      to: {
        required,
        email,
      },
      subject: {
        required,
        maxLength: maxLength(100),
      },
      message: {
        required,
        maxLength: maxLength(255),
      },
    },
  },
  async mounted() {
    this.$refs.to.focus = true
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('company', ['sendTestMail']),
    ...mapActions('notification', ['showNotification']),
    resetFormData() {
      this.formData = {
        to: null,
        subject: null,
        message: null,
      }
      this.$v.formData.$reset()
    },
    async onTestMailSend() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response = await this.sendTestMail(this.formData)

      if (response.data) {
        if (response.data.success) {
          this.showNotification({
            type: 'success',
            message: this.$tc('general.send_mail_successfully'),
          })
          this.closeTaxModal()
          this.isLoading = false
          return true
        }
        this.showNotification({
          type: 'error',
          message: this.$tc('validation.something_went_wrong'),
        })
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      this.showNotification({
        type: 'error',
        message: response.data.error,
      })
    },
    closeTaxModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
    },
  },
}
</script>

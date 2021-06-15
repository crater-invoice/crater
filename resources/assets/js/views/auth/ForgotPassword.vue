<template>
  <form id="loginForm" @submit.prevent="validateBeforeSubmit">
    <div class="mb-4">
      <sw-input
        :invalid="$v.formData.email.$error"
        v-model.lazy="formData.email"
        :disabled="isSent"
        :placeholder="$t('login.enter_email')"
        focus
        name="email"
        @blur="$v.formData.email.$touch()"
      />
      <div v-if="$v.formData.email.$error">
        <span v-if="!$v.formData.email.required" class="text-sm text-danger">
          {{ $t('validation.required') }}
        </span>
        <span v-if="!$v.formData.email.email" class="text-sm text-danger">
          {{ $t('validation.email_incorrect') }}
        </span>
      </div>
    </div>
    <sw-button
      :loading="isLoading"
      :disabled="isLoading"
      type="submit"
      variant="primary"
    >
      <div v-if="!isSent">
        {{ $t('validation.send_reset_link') }}
      </div>
      <div v-else>
        {{ $t('validation.not_yet') }}
      </div>
    </sw-button>

    <div class="mt-4 mb-4 text-sm">
      <router-link
        to="/login"
        class="text-sm text-primary-400 hover:text-gray-700"
      >
        {{ $t('general.back_to_login') }}
      </router-link>
    </div>
  </form>
</template>

<script type="text/babel">
const { required, email } = require('vuelidate/lib/validators')
import { mapActions } from 'vuex'
export default {
  data() {
    return {
      formData: {
        email: '',
      },
      isSent: false,
      isLoading: false,
    }
  },
  validations: {
    formData: {
      email: {
        email,
        required,
      },
    },
  },
  methods: {
    ...mapActions('notification', ['showNotification']),
    async validateBeforeSubmit(e) {
      this.$v.formData.$touch()
      if (!this.$v.formData.$invalid) {
        try {
          this.isLoading = true
          let res = await axios.post(
            '/api/v1/auth/password/email',
            this.formData
          )

          if (res.data) {
            this.showNotification({
              type: 'success',
              message: 'Mail sent successfully!',
            })
          }

          this.isSent = true
          this.isLoading = false
        } catch (err) {
          this.isLoading = false
        }
      }
    },
  },
}
</script>

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
      v-if="!isSent"
      :disabled="isLoading"
      type="submit"
      variant="primary"
    >
      {{ $t('validation.send_reset_link') }}
    </sw-button>
    <sw-button v-else :disabled="isLoading" variant="primary" type="submit">
      {{ $t('validation.not_yet') }}
    </sw-button>

    <div class="mt-4 mb-4 text-sm">
      <router-link to="/login">
        {{ $t('general.back_to_login') }}
      </router-link>
    </div>
  </form>
</template>

<script type="text/babel">
import { async } from 'q'
import { mapActions } from 'vuex'
const { required, email } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      formData: {
        email: '',
      },
      isSent: false,
      isLoading: false,
      isRegisteredUser: false,
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
    ...mapActions('auth', ['checkMail']),
    async validateBeforeSubmit(e) {
      this.$v.formData.$touch()
      let { data } = await this.checkMail()
      if (data === false) {
        toastr['error'](this.$t('validation.email_does_not_exist'))
        return
      }
      if (!this.$v.formData.$invalid) {
        try {
          this.isLoading = true
          let res = await axios.post(
            '/api/v1/auth/password/email',
            this.formData
          )

          if (res.data) {
            toastr['success']('Mail sent successfuly!', 'Success')
          }

          this.isSent = true
          this.isLoading = false
        } catch (err) {
          if (err.response && err.response.status === 403) {
            toastr['error'](err.response.data, 'Error')
          }
        }
      }
    },
    // async checkMail() {
    //   let response = await window.axios.post(
    //     '/api/v1/is-registered',
    //     this.formData
    //   )
    //   return response.data
    // },
  },
}
</script>

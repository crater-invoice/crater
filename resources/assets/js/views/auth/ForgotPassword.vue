<template>
  <form
    id="loginForm"
    @submit.prevent="validateBeforeSubmit"
  >

    <div :class="{'form-group' : true }">
      <base-input
        :invalid="$v.formData.email.$error"
        v-model.lazy="formData.email"
        :disabled="isSent"
        :placeholder="$t('login.enter_email')"
        focus
        name="email"
        @blur="$v.formData.email.$touch()"
      />
      <div v-if="$v.formData.email.$error">
        <span v-if="!$v.formData.email.required" class="help-block text-danger">
          {{ $t('validation.required') }}
        </span>
        <span v-if="!$v.formData.email.email" class="help-block text-danger">
          {{ $t('validation.email_incorrect') }}
        </span>
      </div>
    </div>
    <base-button v-if="!isSent" :loading="isLoading" :disabled="isLoading" type="submit" color="theme">
      {{ $t('validation.send_reset_link') }}
    </base-button>
    <base-button v-else :loading="isLoading" :disabled="isLoading" color="theme" type="submit">
      {{ $t('validation.not_yet') }}
    </base-button>

    <div class="other-actions mb-4">
      <router-link to="/login">
        {{ $t('general.back_to_login') }}
      </router-link>
    </div>
  </form>
</template>

<script type="text/babel">
import { validationMixin } from 'vuelidate'
import { async } from 'q'
const { required, email } = require('vuelidate/lib/validators')

export default {
  mixins: [validationMixin],
  data () {
    return {
      formData: {
        email: ''
      },
      isSent: false,
      isLoading: false,
      isRegisteredUser: false
    }
  },
  validations: {
    formData: {
      email: {
        email,
        required
      }
    }
  },
  methods: {

    async validateBeforeSubmit (e) {
      this.$v.formData.$touch()

      if (await this.checkMail() === false) {
        toastr['error'](this.$t('validation.email_does_not_exist'))
        return
      }
      if (!this.$v.formData.$invalid) {
        try {
          this.isLoading = true
          let res = await axios.post('/api/auth/password/email', this.formData)

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
    async checkMail () {
      let response = await window.axios.post('/api/is-registered', this.formData)
      return response.data
    }
  }
}
</script>

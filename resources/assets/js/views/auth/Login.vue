<template>
  <form id="loginForm" @submit.prevent="validateBeforeSubmit">
    <sw-input-group
      :label="$t('login.email')"
      :error="emailError"
      class="mb-4"
      required
    >
      <sw-input
        :invalid="$v.loginData.email.$error"
        :placeholder="$t(login.login_placeholder)"
        v-model="loginData.email"
        focus
        type="email"
        name="email"
        @input="$v.loginData.email.$touch()"
      />
    </sw-input-group>

    <sw-input-group
      :label="$t('login.password')"
      :error="passwordError"
      class="mb-4"
      required
    >
      <sw-input
        v-model="loginData.password"
        :invalid="$v.loginData.password.$error"
        :type="getInputType"
        name="password"
        @input="$v.loginData.password.$touch()"
      >
        <template v-slot:rightIcon>
          <eye-off-icon
            v-if="isShowPassword"
            class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
            @click="isShowPassword = !isShowPassword"
          />
          <eye-icon
            v-else
            class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
            @click="isShowPassword = !isShowPassword"
          />
        </template>
      </sw-input>
    </sw-input-group>

    <div class="mt-5 mb-8">
      <div class="mb-4">
        <router-link
          to="forgot-password"
          class="text-sm text-primary-400 hover:text-gray-700"
        >
          {{ $t('login.forgot_password') }}
        </router-link>
      </div>
    </div>

    <sw-button
      :loading="isLoading"
      :disabled="isLoading"
      type="submit"
      variant="primary"
    >
      {{ $t('login.login') }}
    </sw-button>
  </form>
</template>

<script type="text/babel">
import { mapActions } from 'vuex'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
import IconFacebook from '../../components/icon/facebook'
import IconTwitter from '../../components/icon/twitter'
import IconGoogle from '../../components/icon/google'
const { required, email, minLength } = require('vuelidate/lib/validators')

export default {
  components: {
    IconFacebook,
    IconTwitter,
    IconGoogle,
    EyeIcon,
    EyeOffIcon,
  },
  data() {
    return {
      loginData: {
        email: '',
        password: '',
        remember: '',
      },
      submitted: false,
      isLoading: false,
      isShowPassword: false,
    }
  },
  validations: {
    loginData: {
      email: {
        required,
        email,
      },
      password: {
        required,
        minLength: minLength(8),
      },
    },
  },
  computed: {
    emailError() {
      if (!this.$v.loginData.email.$error) {
        return ''
      }
      if (!this.$v.loginData.email.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.loginData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    passwordError() {
      if (!this.$v.loginData.password.$error) {
        return ''
      }
      if (!this.$v.loginData.password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.loginData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.loginData.password.$params.minLength.min,
          { count: this.$v.loginData.password.$params.minLength.min }
        )
      }
    },

    getInputType() {
      if (this.isShowPassword) {
        return 'text'
      }
      return 'password'
    },
  },
  methods: {
    ...mapActions('auth', ['login']),
    ...mapActions('notification', ['showNotification']),
    async validateBeforeSubmit() {
      axios.defaults.withCredentials = true

      this.$v.loginData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      try {
        await this.login(this.loginData)
        this.$router.push('/admin/dashboard')
        this.showNotification({
          type: 'success',
          message: 'Logged in successfully.',
        })
        this.isLoading = false
      } catch (error) {
        this.isLoading = false
      }
    },
  },
}
</script>

<template>
  <form
    id="loginForm"
    @submit.prevent="validateBeforeSubmit"
  >
    <div :class="{'form-group' : true }">
      <p class="input-label">{{ $t('login.email') }} <span class="text-danger"> * </span></p>
      <base-input
        :invalid="$v.loginData.email.$error"
        v-model="loginData.email"
        :placeholder="$t(login.login_placeholder)"
        focus
        type="email"
        name="email"
        @input="$v.loginData.email.$touch()"
      />
      <div v-if="$v.loginData.email.$error">
        <span v-if="!$v.loginData.email.required" class="text-danger">
          {{ $tc('validation.required') }}
        </span>
        <span v-if="!$v.loginData.email.email" class="text-danger">
          {{ $tc('validation.email_incorrect') }}
        </span>
      </div>
    </div>
    <div class="form-group">
      <p class="input-label">{{ $t('login.password') }} <span class="text-danger"> * </span></p>
      <base-input
        v-model="loginData.password"
        :invalid="$v.loginData.password.$error"
        type="password"
        name="password"
        show-password
        @input="$v.loginData.password.$touch()"
      />
      <div v-if="$v.loginData.password.$error">
        <span v-if="!$v.loginData.password.required" class="text-danger">{{ $tc('validation.required') }}</span>
        <span v-if="!$v.loginData.password.minLength" class="text-danger"> {{ $tc('validation.password_min_length', $v.loginData.password.$params.minLength.min, {count: $v.loginData.password.$params.minLength.min}) }} </span>
      </div>
    </div>
    <div class="other-actions row">
      <div class="col-sm-12 text-sm-left mb-4">
        <router-link to="forgot-password" class="forgot-link">
          {{ $t('login.forgot_password') }}
        </router-link>
      </div>
    </div>

    <base-button type="submit" color="theme">{{ $t('login.login') }}</base-button>

    <!-- <div class="social-links">

      <span class="link-text">{{ $t('login.or_signIn_with') }}</span>

      <div class="social-logo">
        <icon-facebook class="icon"/>
        <icon-twitter class="icon"/>
        <icon-google class="icon"/>
      </div>

    </div> -->

  </form>
</template>

<script type="text/babel">
import { mapActions } from 'vuex'

import IconFacebook from '../../components/icon/facebook'
import IconTwitter from '../../components/icon/twitter'
import IconGoogle from '../../components/icon/google'
import { validationMixin } from 'vuelidate'
const { required, email, minLength } = require('vuelidate/lib/validators')

export default {

  components: {
    IconFacebook,
    IconTwitter,
    IconGoogle
  },
  mixins: [validationMixin],
  data () {
    return {
      loginData: {
        email: '',
        password: '',
        remember: ''
      },
      submitted: false
    }
  },
  validations: {
    loginData: {
      email: {
        required,
        email
      },
      password: {
        required,
        minLength: minLength(5)
      }
    }
  },
  methods: {
    ...mapActions('auth', [
      'login'
    ]),
    async validateBeforeSubmit () {
      this.$v.loginData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      this.login(this.loginData).then((res) => {
        this.$router.push('/admin/dashboard')
        this.isLoading = false
      }).catch(() => {
        this.isLoading = false
      })
    }
  }
}
</script>

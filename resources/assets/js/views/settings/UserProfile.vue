<template>
  <div class="setting-main-container">
    <form action="" @submit.prevent="updateUserData">
      <div class="card setting-card">
        <div class="page-header">
          <h3 class="page-title">{{ $t('settings.account_settings.account_settings') }}</h3>
          <p class="page-sub-title">
            {{ $t('settings.account_settings.section_description') }}
          </p>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.account_settings.name') }}</label>
            <base-input
              v-model="formData.name"
              :invalid="$v.formData.name.$error"
              :placeholder="$t('settings.user_profile.name')"
              @input="$v.formData.name.$touch()"
            />
            <div v-if="$v.formData.name.$error">
              <span v-if="!$v.formData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.account_settings.email') }}</label>
            <base-input
              v-model="formData.email"
              :invalid="$v.formData.email.$error"
              :placeholder="$t('settings.user_profile.email')"
              @input="$v.formData.email.$touch()"
            />
            <div v-if="$v.formData.email.$error">
              <span v-if="!$v.formData.email.required" class="text-danger">{{ $tc('validation.required') }}</span>
              <span v-if="!$v.formData.email.email" class="text-danger">{{ $tc('validation.email_incorrect') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.account_settings.password') }}</label>
            <base-input
              v-model="formData.password"
              :invalid="$v.formData.password.$error"
              :placeholder="$t('settings.user_profile.password')"
              type="password"
              @input="$v.formData.password.$touch()"
            />
            <div v-if="$v.formData.password.$error">
              <span v-if="!$v.formData.password.minLength" class="text-danger"> {{ $tc('validation.password_min_length', $v.formData.password.$params.minLength.min, {count: $v.formData.password.$params.minLength.min}) }} </span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.account_settings.confirm_password') }}</label>
            <base-input
              v-model="formData.confirm_password"
              :invalid="$v.formData.confirm_password.$error"
              :placeholder="$t('settings.user_profile.confirm_password')"
              type="password"
              @input="$v.formData.confirm_password.$touch()"
            />
            <div v-if="$v.formData.confirm_password.$error">
              <span v-if="!$v.formData.confirm_password.sameAsPassword" class="text-danger">{{ $tc('validation.password_incorrect') }}</span>
            </div>
          </div>
        </div>
        <div class="row  mb-4">
          <div class="col-md-12 input-group">
            <base-button
              :loading="isLoading"
              :disabled="isLoading"
              icon="save"
              color="theme"
              type="submit"
            >
              {{ $tc('settings.account_settings.save') }}
            </base-button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
import { validationMixin } from 'vuelidate'
import { mapActions } from 'vuex'
const { required, requiredIf, sameAs, email, minLength } = require('vuelidate/lib/validators')

export default {
  mixins: [validationMixin],
  data () {
    return {
      isLoading: false,
      formData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null
      }
    }
  },
  validations: {
    formData: {
      name: {
        required
      },
      email: {
        required,
        email
      },
      password: {
        minLength: minLength(5)
      },
      confirm_password: {
        required: requiredIf('isRequired'),
        sameAsPassword: sameAs('password')
      }
    }
  },
  computed: {
    isRequired () {
      if (this.formData.password === null || this.formData.password === undefined || this.formData.password === '') {
        return false
      }
      return true
    }
  },
  mounted () {
    this.setInitialData()
  },
  methods: {
    ...mapActions('userProfile', [
      'loadData',
      'editUser'
    ]),
    async setInitialData () {
      let response = await this.loadData()
      this.formData.name = response.data.name
      this.formData.email = response.data.email
    },
    async updateUserData () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let data = {
        name: this.formData.name,
        email: this.formData.email
      }
      if (this.formData.password != null && this.formData.password != undefined && this.formData.password != '') {
        data = { ...data, password: this.formData.password }
      }
      let response = await this.editUser(data)
      if (response.data.success) {
        this.isLoading = false
        window.toastr['success'](this.$t('settings.account_settings.updated_message'))
        return true
      }
      window.toastr['error'](response.data.error)
      return true
    }
  }
}
</script>

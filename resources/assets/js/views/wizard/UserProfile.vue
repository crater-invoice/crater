<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.account_info') }}</p>
      <p class="form-desc">{{ $t('wizard.account_info_desc') }}</p>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.name') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.name.$error"
            v-model.trim="profileData.name"
            type="text"
            name="name"
            @input="$v.profileData.name.$touch()"
          />
          <div v-if="$v.profileData.name.$error">
            <span v-if="!$v.profileData.name.required" class="text-danger">{{ $tc('validation.required') }}</span>
            <span v-if="!$v.profileData.name.minLength" class="text-danger"> {{ $tc('validation.name_min_length', $v.profileData.name.$params.minLength.min, { count: $v.profileData.name.$params.minLength.min }) }} </span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.email') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.email.$error"
            v-model.trim="profileData.email"
            type="text"
            name="email"
            @input="$v.profileData.email.$touch()"
          />
          <div v-if="$v.profileData.email.$error">
            <span v-if="!$v.profileData.email.required" class="text-danger">{{ $tc('validation.required') }}</span>
            <span v-if="!$v.profileData.email.email" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.password') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.password.$error"
            v-model.trim="profileData.password"
            type="password"
            name="password"
            @input="$v.profileData.password.$touch()"
          />
          <div v-if="$v.profileData.password.$error">
            <span v-if="!$v.profileData.password.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.confirm_password') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.profileData.confirm_password.$error"
            v-model.trim="profileData.confirm_password"
            type="password"
            name="confirm_password"
            @input="$v.profileData.confirm_password.$touch()"
          />
          <div v-if="$v.profileData.confirm_password.$error">
            <span v-if="!$v.profileData.confirm_password.sameAsPassword" class="text-danger">{{ $tc('validation.password_incorrect') }}</span>
          </div>
        </div>
      </div>
      <base-button
        :loading="loading"
        class="pull-right mt-4"
        icon="save"
        color="theme"
        type="submit"
      >
        {{ $t('wizard.save_cont') }}
      </base-button>
    </form>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
import Ls from '../../services/ls'
const { required, requiredIf, sameAs, minLength, email } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  data () {
    return {
      profileData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null
      },
      loading: false
    }
  },
  validations: {
    profileData: {
      name: {
        required,
        minLength: minLength(3)
      },
      email: {
        email,
        required
      },
      password: {
        required
      },
      confirm_password: {
        required: requiredIf('isRequired'),
        sameAsPassword: sameAs('password')
      }
    }
  },
  computed: {
    isRequired () {
      if (this.profileData.password === null || this.profileData.password === undefined || this.profileData.password === '') {
        return false
      }
      return true
    }
  },
  methods: {
    async next () {
      this.$v.profileData.$touch()
      if (this.$v.profileData.$invalid) {
        return true
      }
      this.loading = true
      let response = await window.axios.post('/api/admin/onboarding/profile', this.profileData)
      if (response.data) {
        this.$emit('next')
        this.loading = false
      }
      return true
    }
  }
}
</script>

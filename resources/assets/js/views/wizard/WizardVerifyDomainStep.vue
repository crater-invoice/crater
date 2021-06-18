<template>
  <sw-wizard-step
    :title="$t('wizard.verify_domain.title')"
    :description="$t('wizard.verify_domain.desc')"
  >
    <div class="w-full md:w-2/3">
      <sw-input-group
        :label="$t('wizard.verify_domain.app_domain')"
        :error="domainError"
        required
      >
        <sw-input
          :invalid="$v.formData.app_domain.$error"
          v-model.trim="formData.app_domain"
          type="text"
          name="name"
          placeholder="crater.com"
          @input="$v.formData.app_domain.$touch()"
        />
      </sw-input-group>
    </div>

    <p class="mt-4 mb-0 text-sm text-gray-600">Notes:</p>
    <ul class="w-full text-gray-600 list-disc list-inside">
      <li class="text-sm leading-8">
        App domain should not contain
        <b class="inline-block px-1 bg-gray-100 rounded-sm">https://</b> or
        <b class="inline-block px-1 bg-gray-100 rounded-sm">http</b> in front of
        the domain.
      </li>
      <li class="text-sm leading-8">
        If you're accessing the website on a different port, please mention the
        port. For example:
        <b class="inline-block px-1 bg-gray-100">localhost:8080</b>
      </li>
    </ul>

    <sw-button
      :loading="isLoading"
      :disabled="isLoading"
      class="mt-8"
      variant="primary"
      @click="verifyDomain"
    >
      {{ $t('wizard.verify_domain.verify_now') }}
    </sw-button>
  </sw-wizard-step>
</template>

<script>
import { ArrowRightIcon } from '@vue-hero-icons/solid'
const { required } = require('vuelidate/lib/validators')
import { mapActions } from 'vuex'

export default {
  components: {
    ArrowRightIcon,
  },
  data() {
    return {
      formData: {
        app_domain: window.location.origin.replace(/(^\w+:|^)\/\//, ''),
      },
      isLoading: false,
      isShow: true,
    }
  },

  validations: {
    formData: {
      app_domain: {
        required,
        isUrl(val) {
          return this.$utils.checkValidDomainUrl(val)
        },
      },
    },
  },

  computed: {
    hasNext() {
      return false
    },
    domainError() {
      if (!this.$v.formData.app_domain.$error) {
        return ''
      }

      if (!this.$v.formData.app_domain.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.app_domain.isUrl) {
        return this.$tc('validation.invalid_domain_url')
      }
    },
  },

  methods: {
    ...mapActions('notification', ['showNotification']),
    listToggle() {
      this.isShow = !this.isShow
    },

    async verifyDomain() {
      this.$v.formData.$touch()

      if (this.$v.formData.$invalid) {
        return true
      }

      this.isLoading = true
      try {
        await window.axios.put('api/v1/onboarding/set-domain', this.formData)

        await window.axios.get('/sanctum/csrf-cookie')

        await window.axios.post('/api/v1/onboarding/login')

        let driverRes = await window.axios.get('/api/v1/auth/check')

        if (driverRes.data) {
          await this.$emit('next', 4)
        }
        this.isLoading = false
      } catch (e) {
        this.showNotification({
          type: 'error',
          message: this.$t('wizard.errors.domain_verification_failed'),
        })
        this.isLoading = false
      }
    },
  },
}
</script>

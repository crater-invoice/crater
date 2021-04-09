<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.notification.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.notification.description') }}
        </p>
      </template>
      <form action="" @submit.prevent="saveEmail()">
        <div class="grid-cols-2 col-span-1">
          <sw-input-group
            :label="$t('settings.notification.email')"
            :error="notificationEmailError"
            class="my-2"
            required
          >
            <sw-input
              :invalid="$v.notification_email.$error"
              v-model.trim="notification_email"
              :placeholder="$tc('settings.notification.please_enter_email')"
              type="text"
              name="notification_email"
              icon="envelope"
              @input="$v.notification_email.$touch()"
            />
          </sw-input-group>

          <sw-button
            :disabled="isLoading"
            :loading="isLoading"
            variant="primary"
            type="submit"
            class="my-6"
          >
            <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
            {{ $tc('settings.notification.save') }}
          </sw-button>
        </div>
      </form>

      <sw-divider class="mt-1 mb-6" />

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="notify_invoice_viewed"
            class="absolute"
            style="top: -20px"
            @change="setInvoiceViewd"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.notification.invoice_viewed') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('settings.notification.invoice_viewed_desc') }}
          </p>
        </div>
      </div>
      <div class="flex mb-2">
        <div class="relative w-12">
          <sw-switch
            v-model="notify_estimate_viewed"
            class="absolute"
            style="top: -20px"
            @change="setEstimateViewd"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.notification.estimate_viewed') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('settings.notification.estimate_viewed_desc') }}
          </p>
        </div>
      </div>
    </sw-card>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
const { required, email } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      notification_email: null,
      notify_invoice_viewed: null,
      notify_estimate_viewed: null,
      isRequestOnGoing: false,
    }
  },
  validations: {
    notification_email: {
      required,
      email,
    },
  },
  computed: {
    notificationEmailError() {
      if (!this.$v.notification_email.$error) {
        return ''
      }

      if (!this.$v.notification_email.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.notification_email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),
    ...mapActions('notification', ['showNotification']),

    async fetchData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCompanySettings([
        'notify_invoice_viewed',
        'notify_estimate_viewed',
        'notification_email',
      ])

      if (response.data) {
        this.notification_email = response.data.notification_email

        response.data.notify_invoice_viewed === 'YES'
          ? (this.notify_invoice_viewed = true)
          : (this.notify_invoice_viewed = false)

        response.data.notify_estimate_viewed === 'YES'
          ? (this.notify_estimate_viewed = true)
          : (this.notify_estimate_viewed = false)
      }
      this.isRequestOnGoing = false
    },

    async saveEmail() {
      this.$v.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        settings: {
          notification_email: this.notification_email,
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        this.isLoading = false

        this.showNotification({
          type: 'success',
          message: this.$tc('settings.notification.email_save_message'),
        })
      }
    },

    async setInvoiceViewd(val) {
      this.$v.$touch()

      if (this.$v.$invalid) {
        this.notify_invoice_viewed = !this.notify_invoice_viewed
        return true
      }

      let data = {
        settings: {
          notify_invoice_viewed: this.notify_invoice_viewed ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data.success) {
        this.showNotification({
          type: 'success',
          message: this.$tc('general.setting_updated'),
        })
      }
    },

    async setEstimateViewd(val) {
      this.$v.$touch()

      if (this.$v.$invalid) {
        this.notify_estimate_viewed = !this.notify_estimate_viewed
        return true
      }

      let data = {
        settings: {
          notify_estimate_viewed: this.notify_estimate_viewed ? 'YES' : 'NO',
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        this.showNotification({
          type: 'success',
          message: this.$tc('general.setting_updated'),
        })
      }
    },
  },
}
</script>

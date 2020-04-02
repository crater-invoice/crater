<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $t('settings.notification.title') }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.notification.description') }}
        </p>
      </div>
      <form action="" @submit.prevent="saveEmail()">
        <div class="form-group">
          <label class="form-label">{{ $t('settings.notification.email') }}</label><span class="text-danger"> *</span>
          <base-input
            :invalid="$v.notification_email.$error"
            v-model.trim="notification_email"
            :placeholder="$tc('settings.notification.please_enter_email')"
            type="text"
            name="notification_email"
            icon="envelope"
            input-class="col-md-6"
            @input="$v.notification_email.$touch()"
          />
          <div v-if="$v.notification_email.$error">
            <span v-if="!$v.notification_email.required" class="text-danger">{{ $tc('validation.required') }}</span>
            <span v-if="!$v.notification_email.email" class="text-danger"> {{ $tc('validation.email_incorrect') }} </span>
          </div>
          <base-button
            :loading="isLoading"
            :disabled="isLoading"
            class="mt-4"
            icon="save"
            color="theme"
            type="submit"
          > {{ $tc('settings.notification.save') }} </base-button>
        </div>
      </form>
      <hr>
      <div class="flex-box mt-3 mb-4">
        <div class="left">
          <base-switch v-model="notify_invoice_viewed" class="btn-switch" @change="setInvoiceViewd"/>
        </div>
        <div class="right ml-15">
          <p class="box-title">  {{ $t('settings.notification.invoice_viewed') }} </p>
          <p class="box-desc">  {{ $t('settings.notification.invoice_viewed_desc') }} </p>
        </div>
      </div>
      <div class="flex-box mb-2">
        <div class="left">
          <base-switch v-model="notify_estimate_viewed" class="btn-switch" @change="setEstimateViewd"/>
        </div>
        <div class="right ml-15">
          <p class="box-title">  {{ $t('settings.notification.estimate_viewed') }} </p>
          <p class="box-desc">  {{ $t('settings.notification.estimate_viewed_desc') }} </p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { validationMixin } from 'vuelidate'
const { required, email } = require('vuelidate/lib/validators')

export default {

  mixins: [validationMixin],
  data () {
    return {
      isLoading: false,
      notification_email: null,
      notify_invoice_viewed: null,
      notify_estimate_viewed: null
    }
  },
  validations: {
    notification_email: {
      required,
      email
    }
  },
  mounted () {
    this.fetchData()
  },
  methods: {
    async fetchData () {
      let response1 = await axios.get('/api/settings/get-setting?key=notify_invoice_viewed')
      if (response1.data) {
        let data = response1.data
        data.notify_invoice_viewed === 'YES' ?
          this.notify_invoice_viewed = true :
          this.notify_invoice_viewed = null
      }
      let response2 = await axios.get('/api/settings/get-setting?key=notify_estimate_viewed')
      if (response2.data) {
        let data = response2.data
        data.notify_estimate_viewed === 'YES' ?
          this.notify_estimate_viewed = true :
          this.notify_estimate_viewed = null
      }
      let response3 = await axios.get('/api/settings/get-setting?key=notification_email')
      if (response3.data) {
        this.notification_email = response3.data.notification_email
      }
    },
    async saveEmail () {
      this.$v.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let data = {
        key: 'notification_email',
        value: this.notification_email
      }
      let response = await axios.put('/api/settings/update-setting', data)
      if (response.data.success) {
        this.isLoading = false
        window.toastr['success'](this.$tc('settings.notification.email_save_message'))
      }
    },
    async setInvoiceViewd (val) {
      this.$v.$touch()
      if (this.$v.$invalid) {
        this.notify_invoice_viewed = !this.notify_invoice_viewed
        return true
      }
      let data = {
        key: 'notify_invoice_viewed',
        value: this.notify_invoice_viewed ? 'YES' : 'NO'
      }

      let response = await axios.put('/api/settings/update-setting', data)
      if (response.data.success) {
        window.toastr['success'](this.$tc('general.setting_updated'))
      }
    },
    async setEstimateViewd (val) {
      this.$v.$touch()
      if (this.$v.$invalid) {
        this.notify_estimate_viewed = !this.notify_estimate_viewed
        return true
      }
      let data = {
        key: 'notify_estimate_viewed',
        value: this.notify_estimate_viewed ? 'YES' : 'NO'
      }
      let response = await axios.put('/api/settings/update-setting', data)
      if (response.data) {
        window.toastr['success'](this.$tc('general.setting_updated'))
      }
    }
  }
}
</script>

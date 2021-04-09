<template>
  <sw-wizard-step
    :title="$t('wizard.preferences')"
    :description="$t('wizard.preferences_desc')"
  >
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <form action="" @submit.prevent="next">
      <div>
        <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
          <sw-input-group
            :label="$t('wizard.currency')"
            :error="currencyError"
            required
          >
            <sw-select
              v-model="settingData.currency"
              :class="{ error: $v.settingData.currency.$error }"
              :options="currencies"
              :custom-label="currencyNameWithCode"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('settings.currencies.select_currency')"
              track-by="id"
              label="name"
              @input="$v.settingData.currency.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('settings.preferences.default_language')"
            :error="languageError"
            required
          >
            <sw-select
              v-model="settingData.language"
              :class="{ error: $v.settingData.language.$error }"
              :options="languages"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('settings.preferences.select_language')"
              label="name"
              @input="$v.settingData.language.$touch()"
            />
          </sw-input-group>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
          <sw-input-group
            :label="$t('wizard.date_format')"
            :error="dateFormatError"
            required
          >
            <sw-select
              v-model="settingData.dateFormat"
              :class="{ error: $v.settingData.dateFormat.$error }"
              :options="dateFormats"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('settings.preferences.select_date_format')"
              label="display_date"
              @input="$v.settingData.dateFormat.$touch()"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('wizard.time_zone')"
            :error="timeZoneError"
            required
          >
            <sw-select
              v-model="settingData.timeZone"
              :class="{ error: $v.settingData.timeZone.$error }"
              :options="timeZones"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('settings.preferences.select_time_zone')"
              label="key"
              @input="$v.settingData.timeZone.$touch()"
            />
          </sw-input-group>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
          <sw-input-group
            :label="$t('wizard.fiscal_year')"
            :error="fiscalYearError"
            required
          >
            <sw-select
              v-model="settingData.fiscalYear"
              :class="{ error: $v.settingData.fiscalYear.$error }"
              :options="fiscalYears"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('settings.preferences.select_financial_year')"
              label="key"
              @input="$v.settingData.fiscalYear.$touch()"
            />
          </sw-input-group>
        </div>
        <sw-button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          type="submit"
          class="mt-4"
        >
          <save-icon v-if="!isLoading" class="mr-2" />
          {{ $t('wizard.save_cont') }}
        </sw-button>
      </div>
    </form>
  </sw-wizard-step>
</template>

<script>
import Ls from '../../services/ls'
import { mapActions, mapGetters } from 'vuex'

const { required, minLength, email } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      settingData: {
        language: null,
        currency: null,
        timeZone: null,
        dateFormat: null,
        fiscalYear: null,
      },
      isLoading: false,
      isFetching: false,
      step: 1,
    }
  },
  validations: {
    settingData: {
      currency: {
        required,
      },
      language: {
        required,
      },
      dateFormat: {
        required,
      },
      timeZone: {
        required,
      },
      fiscalYear: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters([
      'languages',
      'currencies',
      'timeZones',
      'dateFormats',
      'fiscalYears',
    ]),

    ...mapGetters('company', ['defaultFiscalYear', 'defaultTimeZone']),
    currencyError() {
      if (!this.$v.settingData.currency.$error) {
        return ''
      }
      if (!this.$v.settingData.currency.required) {
        return this.$tc('validation.required')
      }
    },
    languageError() {
      if (!this.$v.settingData.language.$error) {
        return ''
      }

      if (!this.$v.settingData.language.required) {
        return this.$tc('validation.required')
      }
    },
    dateFormatError() {
      if (!this.$v.settingData.dateFormat.$error) {
        return ''
      }
      if (!this.$v.settingData.dateFormat.required) {
        return this.$tc('validation.required')
      }
    },
    timeZoneError() {
      if (!this.$v.settingData.timeZone.$error) {
        return ''
      }
      if (!this.$v.settingData.timeZone.required) {
        return this.$tc('validation.required')
      }
    },
    fiscalYearError() {
      if (!this.$v.settingData.fiscalYear.$error) {
        return ''
      }
      if (!this.$v.settingData.fiscalYear.required) {
        return this.$tc('validation.required')
      }
    },
  },
  mounted() {
    // this.getOnboardingData()
    this.setInitialData()
  },
  methods: {
    ...mapActions('company', ['updateCompanySettings', 'setSelectedCompany']),
    ...mapActions('notification', ['showNotification']),
    ...mapActions([
      'fetchLanguages',
      'fetchCurrencies',
      'fetchFiscalYears',
      'fetchDateFormats',
      'fetchTimeZones',
    ]),
    async setInitialData() {
      this.isFetching = true
      await this.fetchCurrencies()
      await this.fetchDateFormats()
      await this.fetchLanguages()
      await this.fetchFiscalYears()
      await this.fetchTimeZones()
      await this.fetchLanguages()

      this.settingData.currency = this.currencies.find(
        (currency) => currency.id === 1
      )
      this.settingData.language = this.languages.find(
        (language) => language.code === 'en'
      )
      this.settingData.dateFormat = this.dateFormats.find(
        (dateFormat) => dateFormat.carbon_format_value == 'd M Y'
      )

      this.settingData.timeZone = this.timeZones.find(
        (timeZone) => timeZone.value === 'UTC'
      )

      this.settingData.fiscalYear = this.fiscalYears.find(
        (fiscalYear) => fiscalYear.value === '1-12'
      )
      this.isFetching = false
    },
    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },
    async next() {
      this.$v.settingData.$touch()

      if (this.$v.settingData.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        settings: {
          currency: this.settingData.currency.id,
          time_zone: this.settingData.timeZone.value,
          language: this.settingData.language.code,
          fiscal_year: this.settingData.fiscalYear.value,
          carbon_date_format: this.settingData.dateFormat.carbon_format_value,
          moment_date_format: this.settingData.dateFormat.moment_format_value,
        },
      }

      let response = await this.updateCompanySettings(data)

      if (response.data) {
        this.isLoading = false
        this.updateUserSettings()
        Ls.set('auth.token', response.data.token)
      }
    },
    async updateUserSettings() {
      let data = {
        settings: {
          language: this.settingData.language.code,
        },
      }

      let response = await axios.put('/api/v1/me/settings', data)

      if (response.data) {
        this.$emit('next', 'COMPLETED')
        this.showNotification({
          type: 'success',
          message: 'Login Successful',
        })
        this.$router.push('/admin/dashboard')
      }
    },
  },
}
</script>

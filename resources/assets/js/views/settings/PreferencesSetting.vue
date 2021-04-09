<template>
  <form action="" class="relative" @submit.prevent="updatePreferencesData">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card variant="setting-card">
      <template slot="header">
        <h6 class="sw-section-title">
          {{ $t('settings.menu_title.preferences') }}
        </h6>

        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.preferences.general_settings') }}
        </p>
      </template>

      <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2">
        <sw-input-group
          :label="$tc('settings.preferences.currency')"
          :error="currencyError"
          required
        >
          <sw-select
            v-model="formData.currency"
            :options="currencies"
            :custom-label="currencyNameWithCode"
            :class="{ error: $v.formData.currency.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$tc('settings.currencies.select_currency')"
            class="mt-2"
            label="name"
            track-by="id"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.preferences.default_language')"
          :error="languageError"
          required
        >
          <sw-select
            v-model="formData.language"
            :options="languages"
            :class="{ error: $v.formData.language.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$tc('settings.preferences.select_language')"
            class="mt-2"
            label="name"
            track-by="code"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.preferences.time_zone')"
          :error="timeZoneError"
          required
        >
          <sw-select
            v-model="formData.timeZone"
            :options="timeZones"
            :class="{ error: $v.formData.timeZone.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$tc('settings.preferences.select_time_zone')"
            class="mt-2"
            label="key"
            track-by="key"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.preferences.date_format')"
          :error="dateFormatError"
          required
        >
          <sw-select
            v-model="formData.dateFormat"
            :options="dateFormats"
            :class="{ error: $v.formData.dateFormat.$error }"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$tc('settings.preferences.select_date_format')"
            class="mt-2"
            label="display_date"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('settings.preferences.fiscal_year')"
          :error="fiscalYearError"
          class="mb-2"
          required
        >
          <sw-select
            v-model="formData.fiscalYear"
            :options="fiscalYears"
            :class="{ error: $v.formData.fiscalYear.$error }"
            :show-labels="false"
            :allow-empty="false"
            :searchable="true"
            :placeholder="$tc('settings.preferences.select_financial_year')"
            label="key"
            track-by="value"
          />
        </sw-input-group>
      </div>

      <sw-button
        :disabled="isLoading"
        :loading="isLoading"
        class="mt-6"
        variant="primary"
        type="submit"
      >
        <save-icon v-if="!isLoading" class="mr-2 -ml-1" />
        {{ $tc('settings.company_info.save') }}
      </sw-button>

      <sw-divider class="mt-6 mb-8" />

      <div class="flex">
        <div class="relative w-12">
          <sw-switch
            v-model="discount_per_item"
            class="absolute"
            style="top: -18px"
            @change="setDiscount"
          />
        </div>

        <div class="ml-15">
          <p class="p-0 mb-1 text-base leading-snug text-black">
            {{ $t('settings.preferences.discount_per_item') }}
          </p>
          <p
            class="p-0 m-0 text-xs leading-tight text-gray-500"
            style="max-width: 480px"
          >
            {{ $t('settings.preferences.discount_setting_description') }}
          </p>
        </div>
      </div>
    </sw-card>
  </form>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      formData: {
        language: null,
        currency: null,
        timeZone: null,
        dateFormat: null,
        fiscalYear: null,
      },
      isRequestOnGoing: false,
      discount_per_item: null,
    }
  },

  validations: {
    formData: {
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
      'currencies',
      'timeZones',
      'dateFormats',
      'fiscalYears',
      'languages',
    ]),

    ...mapGetters('company', ['defaultFiscalYear', 'defaultTimeZone']),

    currencyError() {
      if (!this.$v.formData.currency.$error) {
        return ''
      }
      if (!this.$v.formData.currency.required) {
        return this.$tc('validation.required')
      }
    },

    languageError() {
      if (!this.$v.formData.language.$error) {
        return ''
      }
      if (!this.$v.formData.language.required) {
        return this.$tc('validation.required')
      }
    },

    timeZoneError() {
      if (!this.$v.formData.timeZone.$error) {
        return ''
      }
      if (!this.$v.formData.timeZone.required) {
        return this.$tc('validation.required')
      }
    },

    fiscalYearError() {
      if (!this.$v.formData.fiscalYear.$error) {
        return ''
      }
      if (!this.$v.formData.fiscalYear.required) {
        return this.$tc('settings.company_info.errors.required')
      }
    },

    dateFormatError() {
      if (!this.$v.formData.dateFormat.$error) {
        return ''
      }

      if (!this.$v.formData.dateFormat.required) {
        return this.$tc('validation.required')
      }
    },
  },

  async mounted() {
    this.setInitialData()
  },

  methods: {
    ...mapActions('company', [
      'setDefaultCurrency',
      'fetchCompanySettings',
      'updateCompanySettings',
    ]),

    ...mapActions([
      'fetchLanguages',
      'fetchFiscalYears',
      'fetchDateFormats',
      'fetchTimeZones',
    ]),

    ...mapActions('notification', ['showNotification']),

    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },

    async setInitialData() {
      this.isRequestOnGoing = true

      await this.fetchDateFormats()
      await this.fetchLanguages()
      await this.fetchFiscalYears()
      await this.fetchTimeZones()

      let response = await this.fetchCompanySettings([
        'currency',
        'time_zone',
        'language',
        'fiscal_year',
        'carbon_date_format',
        'moment_date_format',
        'discount_per_item',
      ])

      if (response.data) {
        response.data.discount_per_item === 'YES'
          ? (this.discount_per_item = true)
          : (this.discount_per_item = false)

        this.formData.currency = this.currencies.find(
          (currency) => currency.id == response.data.currency
        )

        this.formData.language = this.languages.find(
          (language) => language.code == response.data.language
        )

        this.formData.timeZone = this.timeZones.find(
          (timeZone) => timeZone.value == this.defaultTimeZone
        )

        this.formData.fiscalYear = this.fiscalYears.find(
          (fiscalYear) => fiscalYear.value == this.defaultFiscalYear
        )

        this.formData.dateFormat = this.dateFormats.find(
          (dateFormat) =>
            dateFormat.carbon_format_value == response.data.carbon_date_format
        )
      }

      this.isRequestOnGoing = false
    },

    async updatePreferencesData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let data = {
        settings: {
          currency: this.formData.currency.id,
          time_zone: this.formData.timeZone.value,
          fiscal_year: this.formData.fiscalYear.value,
          language: this.formData.language.code,
          carbon_date_format: this.formData.dateFormat.carbon_format_value,
          moment_date_format: this.formData.dateFormat.moment_format_value,
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        this.isLoading = false
        // window.i18n.locale = this.formData.language.code
        this.setDefaultCurrency(this.formData.currency)
        this.showNotification({
          type: 'success',
          message: this.$t('settings.preferences.updated_message'),
        })
        return true
      }
      this.showNotification({
        type: 'error',
        message: response.data.error,
      })
      return true
    },

    async setDiscount() {
      let data = {
        settings: {
          discount_per_item: this.discount_per_item ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data.success) {
        this.showNotification({
          type: 'success',
          message: this.$t('general.setting_updated'),
        })
      }
    },
  },
}
</script>

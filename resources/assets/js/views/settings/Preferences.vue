<template>
  <div class="setting-main-container">
    <div class="card setting-card">
      <div class="page-header">
        <h3 class="page-title">{{ $tc('settings.preferences.preference',2) }}</h3>
        <p class="page-sub-title">
          {{ $t('settings.preferences.general_settings') }}
        </p>
      </div>
      <form action="" @submit.prevent="updatePreferencesData">
        <div class="row">
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.preferences.currency') }}</label><span class="text-danger"> * </span>
            <base-select
              v-model="formData.currency"
              :options="currencies"
              :custom-label="currencyNameWithCode"
              :class="{'error': $v.formData.currency.$error }"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$tc('settings.currencies.select_currency')"
              label="name"
              track-by="id"
            />
            <div v-if="$v.formData.currency.$error">
              <span v-if="!$v.formData.currency.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.preferences.language') }}</label><span class="text-danger"> * </span>
            <base-select
              v-model="formData.language"
              :options="languages"
              :class="{'error': $v.formData.language.$error }"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$tc('settings.preferences.select_language')"
              label="name"
              track-by="code"
            />
            <div v-if="$v.formData.language.$error">
              <span v-if="!$v.formData.language.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.preferences.time_zone') }}</label><span class="text-danger"> * </span>
            <base-select
              v-model="formData.timeZone"
              :options="timeZones"
              :class="{'error': $v.formData.timeZone.$error }"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$tc('settings.preferences.select_time_zone')"
              label="key"
              track-by="key"
            />
            <div v-if="$v.formData.timeZone.$error">
              <span v-if="!$v.formData.timeZone.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.preferences.date_format') }}</label><span class="text-danger"> * </span>
            <base-select
              v-model="formData.dateFormat"
              :options="dateFormats"
              :class="{'error': $v.formData.dateFormat.$error }"
              :searchable="true"
              :show-labels="false"
              :allow-empty="false"
              :placeholder="$tc('settings.preferences.select_date_formate')"
              label="display_date"
            />
            <div v-if="$v.formData.dateFormat.$error">
              <span v-if="!$v.formData.dateFormat.required" class="text-danger">{{ $tc('validation.required') }}</span>
            </div>
          </div>
          <div class="col-md-6 mb-4 form-group">
            <label class="input-label">{{ $tc('settings.preferences.fiscal_year') }}</label><span class="text-danger"> * </span>
            <base-select
              v-model="formData.fiscalYear"
              :options="fiscalYears"
              :class="{'error': $v.formData.fiscalYear.$error }"
              :show-labels="false"
              :allow-empty="false"
              :searchable="true"
              :placeholder="$tc('settings.preferences.select_financial_year')"
              label="key"
              track-by="value"
            />
            <div v-if="$v.formData.fiscalYear.$error">
              <span v-if="!$v.formData.fiscalYear.required" class="text-danger">{{ $tc('settings.company_info.errors.required') }}</span>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12 input-group">
            <base-button
              :loading="isLoading"
              :disabled="isLoading"
              icon="save"
              color="theme"
              type="submit"
            >
              {{ $tc('settings.company_info.save') }}
            </base-button>
          </div>
        </div>
      </form>
      <hr>
      <div class="page-header mt-3">
        <h3 class="page-title">{{ $t('settings.preferences.discount_setting') }}</h3>
        <div class="flex-box">
          <div class="left">
            <base-switch
              v-model="discount_per_item"
              class="btn-switch"
              @change="setDiscount"
            />
          </div>
          <div class="right ml-15">
            <p class="box-title">  {{ $t('settings.preferences.discount_per_item') }} </p>
            <p class="box-desc">  {{ $t('settings.preferences.discount_setting_description') }} </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
import { mapActions } from 'vuex'
const { required } = require('vuelidate/lib/validators')

export default {
  components: { MultiSelect },
  mixins: [validationMixin],
  data () {
    return {
      isLoading: false,
      formData: {
        language: null,
        currency: null,
        timeZone: null,
        dateFormat: null,
        fiscalYear: null
      },
      discount_per_item: null,
      languages: [],
      currencies: [],
      timeZones: [],
      dateFormats: [],
      fiscalYears: []
    }
  },
  validations: {
    formData: {
      currency: {
        required
      },
      language: {
        required
      },
      dateFormat: {
        required
      },
      timeZone: {
        required
      },
      fiscalYear: {
        required
      }
    }
  },
  mounted () {
    this.setInitialData()
    this.getDiscountSettings()
  },
  methods: {
    currencyNameWithCode ({name, code}) {
      return `${code} - ${name}`
    },
    ...mapActions('currency', [
      'setDefaultCurrency'
    ]),
    ...mapActions('preferences', [
      'loadData',
      'editPreferences'
    ]),
    async setInitialData () {
      let response = await this.loadData()
      this.languages = [...response.data.languages]
      this.currencies = response.data.currencies
      this.dateFormats = response.data.date_formats
      this.timeZones = response.data.time_zones
      this.fiscalYears = [...response.data.fiscal_years]
      this.formData.currency = response.data.currencies.find(currency => currency.id == response.data.selectedCurrency)
      this.formData.language = response.data.languages.find(language => language.code == response.data.selectedLanguage)
      this.formData.timeZone = response.data.time_zones.find(timeZone => timeZone.value == response.data.time_zone)
      this.formData.fiscalYear = response.data.fiscal_years.find(fiscalYear => fiscalYear.value == response.data.fiscal_year)
      this.formData.dateFormat = response.data.date_formats.find(dateFormat => dateFormat.carbon_format_value == response.data.carbon_date_format)
    },
    async updatePreferencesData () {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true
      let data = {
        currency: this.formData.currency.id,
        time_zone: this.formData.timeZone.value,
        fiscal_year: this.formData.fiscalYear.value,
        language: this.formData.language.code,
        carbon_date_format: this.formData.dateFormat.carbon_format_value,
        moment_date_format: this.formData.dateFormat.moment_format_value
      }
      let response = await this.editPreferences(data)
      if (response.data.success) {
        this.isLoading = false
        window.i18n.locale = this.formData.language.code
        this.setDefaultCurrency(this.formData.currency)
        window.toastr['success'](this.$t('settings.preferences.updated_message'))
        return true
      }
      window.toastr['error'](response.data.error)
      return true
    },
    async getDiscountSettings () {
      let response = await axios.get('/api/settings/get-setting?key=discount_per_item')
      if (response.data) {
        response.data.discount_per_item === 'YES' ?
          this.discount_per_item = true :
          this.discount_per_item = false
      }
    },
    async setDiscount () {
      let data = {
        key: 'discount_per_item',
        value: this.discount_per_item ? 'YES' : 'NO'
      }
      let response = await axios.put('/api/settings/update-setting', data)
      if (response.data.success) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    }
  }
}
</script>

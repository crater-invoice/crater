<template>
  <div class="card-body">
    <form action="" @submit.prevent="next()">
      <p class="form-title">{{ $t('wizard.preferences') }}</p>
      <p class="form-desc">{{ $t('wizard.preferences_desc') }}</p>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.currency') }}</label>
          <span class="text-danger"> *</span>
          <base-select
            v-model="settingData.currency"
            :class="{'error': $v.settingData.currency.$error }"
            :options="currencies"
            :custom-label="currencyNameWithCode"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.currencies.select_currency')"
            track-by="id"
            label="name"
            @input="$v.settingData.currency.$touch()"
          />
          <div v-if="$v.settingData.currency.$error">
            <span v-if="!$v.settingData.currency.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.language') }}</label><span class="text-danger"> *</span>
          <base-select
            v-model="settingData.language"
            :class="{'error': $v.settingData.language.$error }"
            :options="languages"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.preferences.select_language')"
            label="name"
            @input="$v.settingData.language.$touch()"
          />
          <div v-if="$v.settingData.language.$error">
            <span v-if="!$v.settingData.language.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.date_format') }}</label><span class="text-danger"> *</span>
          <base-select
            v-model="settingData.dateFormat"
            :class="{'error': $v.settingData.dateFormat.$error }"
            :options="dateFormats"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.preferences.select_date_formate')"
            label="display_date"
            @input="$v.settingData.dateFormat.$touch()"
          />
          <div v-if="$v.settingData.dateFormat.$error">
            <span v-if="!$v.settingData.dateFormat.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.time_zone') }}</label><span class="text-danger"> *</span>
          <base-select
            v-model="settingData.timeZone"
            :class="{'error': $v.settingData.timeZone.$error }"
            :options="timeZones"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.preferences.select_date_formate')"
            label="key"
            @input="$v.settingData.timeZone.$touch()"
          />
          <div v-if="$v.settingData.timeZone.$error">
            <span v-if="!$v.settingData.timeZone.required" class="text-danger">{{ $tc('validation.required') }}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label class="form-label">{{ $t('wizard.fiscal_year') }}</label><span class="text-danger"> *</span>
          <base-select
            v-model="settingData.fiscalYear"
            :class="{'error': $v.settingData.fiscalYear.$error }"
            :options="fiscalYears"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.preferences.select_financial_year')"
            label="key"
            @input="$v.settingData.fiscalYear.$touch()"
          />
          <div v-if="$v.settingData.fiscalYear.$error">
            <span v-if="!$v.settingData.fiscalYear.required" class="text-danger">{{ $tc('customers.errors.required') }}</span>
          </div>
        </div>
      </div>
      <base-button :loading="loading" class="pull-right" icon="save" color="theme" type="submit">
        {{ $t('wizard.save_cont') }}
      </base-button>
    </form>
  </div>
</template>
<script>
import MultiSelect from 'vue-multiselect'
import { validationMixin } from 'vuelidate'
import Ls from '../../services/ls'
import { mapActions } from 'vuex'
const { required, minLength, email } = require('vuelidate/lib/validators')

export default {
  components: {
    MultiSelect
  },
  mixins: [validationMixin],
  data () {
    return {
      settingData: {
        language: null,
        currency: null,
        timeZone: null,
        dateFormat: null,
        fiscalYear: null
      },
      loading: false,
      step: 1,
      languages: [],
      currencies: [],
      timeZones: [],
      dateFormats: [],
      fiscalYears: []
    }
  },
  validations: {
    settingData: {
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
    this.getOnboardingData()
  },
  methods: {
    currencyNameWithCode ({name, code}) {
      return `${code} - ${name}`
    },
    ...mapActions('auth', [
      'loginOnBoardingUser'
    ]),
    async getOnboardingData () {
      let response = await window.axios.get('/api/admin/onboarding')
      if (response.data) {
        if (response.data.profile_complete === 'COMPLETED') {
          this.$router.push('/admin/dashboard')

          return
        }

        let dbStep = parseInt(response.data.profile_complete)

        if (dbStep) {
          this.step = dbStep + 1
        }

        this.languages = response.data.languages
        this.currencies = response.data.currencies
        this.dateFormats = response.data.date_formats
        this.timeZones = response.data.time_zones
        this.fiscalYears = response.data.fiscal_years

        this.settingData.currency = this.currencies.find(currency => currency.id === 1)
        this.settingData.language = this.languages.find(language => language.code === 'en')
        this.settingData.dateFormat = response.data.date_formats.find(dateFormat => dateFormat.carbon_format_value == 'd M Y')
        this.settingData.timeZone = this.timeZones.find(timeZone => timeZone.value === 'UTC')
        this.settingData.fiscalYear = this.fiscalYears.find(fiscalYear => fiscalYear.value === '1-12')
      }
    },
    async next () {
      this.$v.settingData.$touch()

      if (this.$v.settingData.$invalid) {
        return true
      }

      this.loading = true

      let data = {
        currency: this.settingData.currency.id,
        time_zone: this.settingData.timeZone.value,
        language: this.settingData.language.code,
        fiscal_year: this.settingData.fiscalYear.value,
        carbon_date_format: this.settingData.dateFormat.carbon_format_value,
        moment_date_format: this.settingData.dateFormat.moment_format_value
      }

      let response = await window.axios.post('/api/admin/onboarding/settings', data)

      if (response.data) {
        // this.$emit('next')
        this.loading = false
        Ls.set('auth.token', response.data.token)
        this.loginOnBoardingUser(response.data.token)
        window.toastr['success']('Login Successful')
        this.$router.push('/admin/dashboard')
      }
    }
  }
}
</script>

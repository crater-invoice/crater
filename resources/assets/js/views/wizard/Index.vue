<template>
  <div class="wizard">
    <div class="step-indicator">
      <img
        id="logo-crater"
        src="/assets/img/crater-logo.png"
        alt="Crater Logo"
        class="logo-main"
      >
      <div class="indicator-line">
        <div class="center">
          <div class="steps" :class="{'active': step === 1, 'completed': step > 1}">
            <font-awesome-icon v-if="step > 1" icon="check" class="icon-check"/>
          </div>
          <div class="steps" :class="{'active': step === 2, 'completed': step > 2}">
            <font-awesome-icon v-if="step > 2" icon="check" class="icon-check"/>
          </div>
          <div class="steps" :class="{'active': step === 3, 'completed': step > 3}">
            <font-awesome-icon v-if="step > 3" icon="check" class="icon-check"/>
          </div>
          <div class="steps" :class="{'active': step === 4, 'completed': step > 4}">
            <font-awesome-icon v-if="step > 4" icon="check" class="icon-check"/>
          </div>
          <div class="steps" :class="{'active': step === 5, 'completed': step > 5}">
            <font-awesome-icon v-if="step > 5" icon="check" class="icon-check"/>
          </div>
          <div class="steps" :class="{'active': step === 6, 'completed': step > 6}">
            <font-awesome-icon v-if="step > 6" icon="check" class="icon-check"/>
          </div>
          <div class="steps" :class="{'active': step === 7, 'completed': step > 7}">
            <font-awesome-icon v-if="step > 7" icon="check" class="icon-check"/>
          </div>
        </div>
      </div>
    </div>
    <div class="form-content">
      <div class="card wizard-card">
        <component
          :is="tab"
          @next="setTab"
        />
      </div>
    </div>
  </div>
</template>
<script>
import SystemRequirement from './SystemRequirement'
import Permission from './Permission'
import Database from './Database'
import EmailConfiguration from './EmailConfiguration'
import UserProfile from './UserProfile'
import CompanyInfo from './CompanyInfo'
import Settings from './Settings'

export default {
  components: {
    step_1: SystemRequirement,
    step_2: Permission,
    step_3: Database,
    step_4: EmailConfiguration,
    step_5: UserProfile,
    step_6: CompanyInfo,
    step_7: Settings
  },
  data () {
    return {
      loading: false,
      tab: 'step_1',
      step: 1
    }
  },
  created () {
    this.getOnboardingData()
  },
  methods: {
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
          this.tab = `step_${dbStep + 1}`
        }

        this.languages = response.data.languages
        this.currencies = response.data.currencies
        this.dateFormats = response.data.date_formats
        this.timeZones = response.data.time_zones

        // this.settingData.currency = this.currencies.find(currency => currency.id === 1)
        // this.settingData.language = this.languages.find(language => language.code === 'en')
        // this.settingData.dateFormat = this.dateFormats.find(dateFormat => dateFormat.value === 'd M Y')
      }
    },
    setTab (data) {
      this.step++

      if (this.step <= 7) {
        this.tab = 'step_' + this.step
      } else {
        // window.location.reload()
      }
    }
  }
}
</script>

<template>
  <div class="row">
    <div class="col-md-4 reports-tab-container">
      <div class="row">
        <div class="col-md-8">
          <label class="report-label">{{ $t('reports.taxes.date_range') }}</label>
          <base-select
            v-model="selectedRange"
            :options="dateRange"
            :allow-empty="false"
            :show-labels="false"
            @input="onChangeDateRange"
          />
          <span v-if="$v.range.$error && !$v.range.required" class="text-danger"> {{ $t('validation.required') }} </span>
        </div>
      </div>
      <div class="row report-fields-container">
        <div class="col-md-6 report-field-container">
          <label class="report-label">{{ $t('reports.taxes.from_date') }}</label>
          <base-date-picker
            v-model="formData.from_date"
            :invalid="$v.formData.from_date.$error"
            :calendar-button="true"
            calendar-button-icon="calendar"
            @change="$v.formData.from_date.$touch()"
          />
          <span v-if="$v.formData.from_date.$error && !$v.formData.from_date.required" class="text-danger"> {{ $t('validation.required') }} </span>
        </div>
        <div class="col-md-6 report-field-container">
          <label class="report-label">{{ $t('reports.taxes.to_date') }}</label>
          <base-date-picker
            v-model="formData.to_date"
            :invalid="$v.formData.to_date.$error"
            :calendar-button="true"
            calendar-button-icon="calendar"
            @change="$v.formData.to_date.$touch()"
          />
          <span v-if="$v.formData.to_date.$error && !$v.formData.to_date.required" class="text-danger"> {{ $t('validation.required') }} </span>
        </div>
      </div>
      <div class="row report-submit-button-container">
        <div class="col-md-6">
          <base-button outline color="theme" class="report-button" @click="getReports()">
            {{ $t('reports.update_report') }}
          </base-button>
        </div>
      </div>
    </div>
    <div class="col-sm-8 reports-tab-container">
      <iframe :src="getReportUrl" class="reports-frame-style"/>
      <a class="base-button btn btn-primary btn-lg report-view-button" @click="viewReportsPDF">
        <font-awesome-icon icon="file-pdf" class="vue-icon icon-left svg-inline--fa fa-download fa-w-16 mr-2" /> <span>{{ $t('reports.view_pdf') }}</span>
      </a>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import moment from 'moment'
import { validationMixin } from 'vuelidate'
const { required } = require('vuelidate/lib/validators')

export default {
  mixins: [validationMixin],
  data () {
    return {
      dateRange: [
        'Today',
        'This Week',
        'This Month',
        'This Quarter',
        'This Year',
        'Previous Week',
        'Previous Month',
        'Previous Quarter',
        'Previous Year',
        'Custom'
      ],
      selectedRange: 'This Month',
      range: new Date(),
      formData: {
        from_date: moment().startOf('month').toString(),
        to_date: moment().endOf('month').toString()
      },
      url: null,
      siteURL: null
    }
  },
  validations: {
    range: {
      required
    },
    formData: {
      from_date: {
        required
      },
      to_date: {
        required
      }
    }
  },
  computed: {
    ...mapGetters('company', [
      'getSelectedCompany'
    ]),
    getReportUrl () {
      return this.url
    }
  },
  watch: {
    range (newRange) {
      this.formData.from_date = moment(newRange).startOf('year').toString()
      this.formData.to_date = moment(newRange).endOf('year').toString()
    }
  },
  mounted () {
    this.siteURL = `/reports/tax-summary/${this.getSelectedCompany.unique_hash}`
    this.url = `${this.siteURL}?from_date=${moment(this.formData.from_date).format('DD/MM/YYYY')}&to_date=${moment(this.formData.to_date).format('DD/MM/YYYY')}`
  },
  methods: {
    getThisDate (type, time) {
      return moment()[type](time).toString()
    },
    getPreDate (type, time) {
      return moment().subtract(1, time)[type](time).toString()
    },
    onChangeDateRange () {
      switch (this.selectedRange) {
        case 'Today':
          this.formData.from_date = moment().toString()
          this.formData.to_date = moment().toString()
          break

        case 'This Week':
          this.formData.from_date = this.getThisDate('startOf', 'isoWeek')
          this.formData.to_date = this.getThisDate('endOf', 'isoWeek')
          break

        case 'This Month':
          this.formData.from_date = this.getThisDate('startOf', 'month')
          this.formData.to_date = this.getThisDate('endOf', 'month')
          break

        case 'This Quarter':
          this.formData.from_date = this.getThisDate('startOf', 'quarter')
          this.formData.to_date = this.getThisDate('endOf', 'quarter')
          break

        case 'This Year':
          this.formData.from_date = this.getThisDate('startOf', 'year')
          this.formData.to_date = this.getThisDate('endOf', 'year')
          break

        case 'Previous Week':
          this.formData.from_date = this.getPreDate('startOf', 'isoWeek')
          this.formData.to_date = this.getPreDate('endOf', 'isoWeek')
          break

        case 'Previous Month':
          this.formData.from_date = this.getPreDate('startOf', 'month')
          this.formData.to_date = this.getPreDate('endOf', 'month')
          break

        case 'Previous Quarter':
          this.formData.from_date = this.getPreDate('startOf', 'quarter')
          this.formData.to_date = this.getPreDate('endOf', 'quarter')
          break

        case 'Previous Year':
          this.formData.from_date = this.getPreDate('startOf', 'year')
          this.formData.to_date = this.getPreDate('endOf', 'year')
          break

        default:
          break
      }
    },
    setRangeToCustom () {
      this.selectedRange = 'Custom'
    },
    async viewReportsPDF () {
      let data = await this.getReports()
      window.open(this.getReportUrl, '_blank')
      return data
    },
    async getReports (isDownload = false) {
      this.$v.range.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }

      this.url = `${this.siteURL}?from_date=${moment(this.formData.from_date).format('DD/MM/YYYY')}&to_date=${moment(this.formData.to_date).format('DD/MM/YYYY')}`
      return true
    },
    downloadReport () {
      if (!this.getReports()) {
        return false
      }
      if (navigator.appVersion.indexOf('Mac') !== -1) {
        this.url += '&download=true'
      } else {
        window.open(this.url + '&download=true')
      }
      setTimeout(() => {
        this.url = `${this.siteURL}?from_date=${moment(this.formData.from_date).format('DD/MM/YYYY')}&to_date=${moment(this.formData.to_date).format('DD/MM/YYYY')}`
      }, 200)
    }
  }
}
</script>

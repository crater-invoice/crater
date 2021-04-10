<template>
  <div class="grid gap-8 md:grid-cols-12">
    <div class="col-span-8 mt-12 md:col-span-4">
      <div class="grid grid-cols-12">
        <sw-input-group
          :label="$t('reports.expenses.date_range')"
          :error="dateRangeError"
          class="col-span-12 md:col-span-8"
        >
          <sw-select
            v-model="selectedRange"
            :options="dateRange"
            :allow-empty="false"
            :show-labels="false"
            class="mt-2"
            @input="onChangeDateRange"
          />
        </sw-input-group>
      </div>

      <div class="grid grid-cols-1 mt-6 md:gap-10 md:grid-cols-2">
        <sw-input-group
          :label="$t('reports.expenses.from_date')"
          :error="fromDateError"
        >
          <base-date-picker
            v-model="formData.from_date"
            :invalid="$v.formData.from_date.$error"
            :calendar-button="true"
            calendar-button-icon="calendar"
            class="mt-2"
            @input="$v.formData.from_date.$touch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('reports.expenses.to_date')"
          :error="toDateError"
          class="mt-5 md:mt-0"
        >
          <base-date-picker
            v-model="formData.to_date"
            :invalid="$v.formData.to_date.$error"
            :calendar-button="true"
            calendar-button-icon="calendar"
            class="mt-2"
            @input="$v.formData.to_date.$touch()"
          />
        </sw-input-group>
      </div>

      <sw-button
        variant="primary-outline"
        class="content-center hidden mt-0 w-md md:flex md:mt-8"
        @click="getReports()"
      >
        {{ $t('reports.update_report') }}
      </sw-button>
    </div>

    <div class="col-span-8 mt-0 md:mt-12">
      <iframe
        :src="getReportUrl"
        class="hidden w-full h-screen border-gray-100 border-solid rounded md:flex"
      />

      <a
        class="flex items-center justify-center h-10 px-5 py-1 text-sm font-medium leading-none text-center text-white whitespace-nowrap rounded md:hidden bg-primary-500"
        @click="viewReportsPDF"
      >
        <document-text-icon />

        <span>{{ $t('reports.view_pdf') }}</span>
      </a>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import moment from 'moment'
import { DocumentTextIcon } from '@vue-hero-icons/solid'

const { required } = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentTextIcon,
  },

  data() {
    return {
      range: new Date(),
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
        'Custom',
      ],

      selectedRange: 'This Month',
      formData: {
        from_date: moment().startOf('month').toString(),
        to_date: moment().endOf('month').toString(),
      },
      url: null,
      siteURL: null,
    }
  },

  validations: {
    range: {
      required,
    },
    formData: {
      from_date: {
        required,
      },
      to_date: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters('company', ['getSelectedCompany']),
    getReportUrl() {
      return this.url
    },

    dateRangeError() {
      if (!this.$v.range.$error) {
        return ''
      }

      if (!this.$v.range.required) {
        return this.$t('validation.required')
      }
    },

    fromDateError() {
      if (!this.$v.formData.from_date.$error) {
        return ''
      }

      if (!this.$v.formData.from_date.required) {
        return this.$t('validation.required')
      }
    },

    toDateError() {
      if (!this.$v.formData.to_date.$error) {
        return ''
      }

      if (!this.$v.formData.to_date.required) {
        return this.$t('validation.required')
      }
    },

    dateRangeUrl() {
      return `${this.siteURL}?from_date=${moment(
        this.formData.from_date
      ).format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format(
        'YYYY-MM-DD'
      )}`
    },
  },

  watch: {
    range(newRange) {
      this.formData.from_date = moment(newRange).startOf('year').toString()
      this.formData.to_date = moment(newRange).endOf('year').toString()
    },
  },

  mounted() {
    this.siteURL = `/reports/expenses/${this.getSelectedCompany.unique_hash}`
    this.url = this.dateRangeUrl
  },

  methods: {
    getThisDate(type, time) {
      return moment()[type](time).toString()
    },
    getPreDate(type, time) {
      return moment().subtract(1, time)[type](time).toString()
    },
    onChangeDateRange() {
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

    setRangeToCustom() {
      this.selectedRange = 'Custom'
    },

    async viewReportsPDF() {
      let data = await this.getReports()
      window.open(this.getReportUrl, '_blank')
      return data
    },

    async getReports(isDownload = false) {
      this.$v.range.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.url = this.dateRangeUrl
      return true
    },

    downloadReport() {
      if (!this.getReports()) {
        return false
      }

      window.open(this.getReportUrl + '&download=true')

      setTimeout(() => {
        this.url = this.dateRangeUrl
      }, 200)
    },
  },
}
</script>

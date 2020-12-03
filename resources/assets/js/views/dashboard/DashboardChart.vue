<template>
  <div class="grid grid-cols-10 mt-8 bg-white rounded shadow">
    <!-- Chart -->
    <div
      class="grid grid-cols-1 col-span-10 px-4 py-5 lg:col-span-7 xl:col-span-8 sm:p-6"
    >
      <div class="flex justify-between mt-1 mb-6">
        <h6 class="flex items-center sw-section-title">
          <chart-square-bar-icon class="h-5 text-primary-400" />{{ $t('dashboard.monthly_chart.title') }}
        </h6>
        <div class="w-40 h-10" style="z-index: 0">
          <sw-select
            v-model="selectedYear"
            :options="years"
            :allow-empty="false"
            :show-labels="false"
            :placeholder="$t('dashboard.select_year')"
          />
        </div>
      </div>
      <line-chart
        v-if="isLoaded"
        :format-money="$utils.formatMoney"
        :format-graph-money="$utils.formatGraphMoney"
        :invoices="getChartInvoices"
        :expenses="getChartExpenses"
        :receipts="getReceiptTotals"
        :income="getNetProfits"
        :labels="getChartMonths"
        class="sm:w-full"
      />
    </div>

    <!-- Chart Labels -->
    <div
      class="grid grid-cols-1 grid-cols-3 col-span-10 text-center border-t border-l border-gray-200 border-solid lg:border-t-0 lg:text-right lg:col-span-3 xl:col-span-2 lg:grid-cols-1"
    >
      <div class="p-6">
        <span class="text-xs leading-5 lg:text-sm">
          {{ $t('dashboard.chart_info.total_sales') }}
        </span>
        <br />
        <span
          v-if="isLoaded"
          class="block mt-1 text-xl font-semibold leading-8 lg:text-2xl"
        >
          <div v-html="$utils.formatMoney(getTotalSales, defaultCurrency)" />
        </span>
      </div>
      <div class="p-6">
        <span class="text-xs leading-5 lg:text-sm">
          {{ $t('dashboard.chart_info.total_receipts') }}
        </span>
        <br />
        <span
          v-if="isLoaded"
          class="block mt-1 text-xl font-semibold leading-8 lg:text-2xl"
          style="color: #00c99c"
        >
          <div v-html="$utils.formatMoney(getTotalReceipts, defaultCurrency)" />
        </span>
      </div>
      <div class="p-6">
        <span class="text-xs leading-5 lg:text-sm">
          {{ $t('dashboard.chart_info.total_expense') }}
        </span>
        <br />
        <span
          v-if="isLoaded"
          class="block mt-1 text-xl font-semibold leading-8 lg:text-2xl"
          style="color: #fb7178"
        >
          <div v-html="$utils.formatMoney(getTotalExpenses, defaultCurrency)" />
        </span>
      </div>
      <div
        class="col-span-3 p-6 border-t border-gray-200 border-solid lg:col-span-1"
      >
        <span class="text-xs leading-5 lg:text-sm">
          {{ $t('dashboard.chart_info.net_income') }}
        </span>
        <br />
        <span
          class="block mt-1 text-xl font-semibold leading-8 lg:text-2xl"
          style="color: #5851d8"
        >
          <div v-html="$utils.formatMoney(getNetProfit, defaultCurrency)" />
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import LineChart from '../../components/chartjs/LineChart'
import { ChartSquareBarIcon } from "@vue-hero-icons/outline"

export default {
  components: {
    LineChart,
    ChartSquareBarIcon
  },
  data() {
    return {
      ...this.$store.state.dashboard,
      isLoaded: false,
      years: ['This year', 'Previous year'],
      selectedYear: 'This year',
    }
  },
  computed: {
    ...mapGetters('user', {
      user: 'currentUser',
    }),
    ...mapGetters('dashboard', [
      'getChartMonths',
      'getChartInvoices',
      'getChartExpenses',
      'getNetProfits',
      'getReceiptTotals',
      'getTotalSales',
      'getTotalReceipts',
      'getTotalExpenses',
      'getNetProfit',
    ]),
    ...mapGetters('company', ['defaultCurrency']),
  },
  watch: {
    selectedYear(val) {
      if (val === 'Previous year') {
        let params = { previous_year: true }
        this.loadData(params)
      } else {
        this.loadData()
      }
    },
  },
  created() {
    this.loadData()
  },
  methods: {
    ...mapActions('dashboard', ['loadData']),

    async loadData(params) {
      await this.$store.dispatch('dashboard/loadData', params)
      this.isLoaded = true
    },
  },
}
</script>

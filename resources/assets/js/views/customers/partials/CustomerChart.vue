<template>
  <sw-card v-if="chartData" class="flex flex-col mt-6">
    <div class="grid grid-cols-12">
      <div class="col-span-12 xl:col-span-9 xxl:col-span-10">
        <div class="flex justify-between mt-1 mb-6">
          <h6 class="flex items-center sw-section-title">
            <chart-square-bar-icon class="h-5 text-primary-400" />{{
              $t('dashboard.monthly_chart.title')
            }}
          </h6>
          <div class="w-40 h-10">
            <sw-select
              v-model="selectedYear"
              :options="years"
              :allow-empty="false"
              :show-labels="false"
              :placeholder="$t('dashboard.select_year')"
              @select="onChangeYear"
            />
          </div>
        </div>
        <line-chart
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
      <div
        class="grid col-span-12 mt-6 text-center xl:mt-0 sm:grid-cols-4 xl:text-right xl:col-span-3 xl:grid-cols-1 xxl:col-span-2"
      >
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_sales') }}
          </span>
          <br />
          <span class="block mt-1 text-xl font-semibold leading-8">
            <div v-html="getFormattedSalesTotal" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_receipts') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #00c99c"
          >
            <div v-html="getFormattedTotalReceipts" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_expense') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #fb7178"
          >
            <div v-html="getFormattedTotalExpenses" />
          </span>
        </div>
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.net_income') }}
          </span>
          <br />
          <span
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #5851d8"
          >
            <div v-html="getFormattedTotalNetProfit" />
          </span>
        </div>
      </div>
    </div>

    <!-- basic info -->
    <customer-info />
  </sw-card>
</template>

<script>
import CustomerInfo from './CustomerInfo'
import LineChart from '../../../components/chartjs/LineChart'
import { mapActions, mapGetters } from 'vuex'
import { ChartSquareBarIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    LineChart,
    CustomerInfo,
    ChartSquareBarIcon,
  },
  data() {
    return {
      id: null,
      customers: [],
      isLoaded: false,
      chartData: null,
      years: ['This year', 'Previous year'],
      selectedYear: 'This year',
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency']),
    getChartInvoices() {
      if (this.chartData && this.chartData.invoiceTotals) {
        return this.chartData.invoiceTotals
      }
      return []
    },
    getChartExpenses() {
      if (this.chartData && this.chartData.expenseTotals) {
        return this.chartData.expenseTotals
      }
      return []
    },
    getReceiptTotals() {
      if (this.chartData && this.chartData.receiptTotals) {
        return this.chartData.receiptTotals
      }
      return []
    },
    getNetProfits() {
      if (this.chartData && this.chartData.netProfits) {
        return this.chartData.netProfits
      }
      return []
    },
    getChartMonths() {
      if (this.chartData && this.chartData.months) {
        return this.chartData.months
      }
      return []
    },
    getFormattedSalesTotal() {
      if (this.chartData && this.chartData.salesTotal) {
        return this.$utils.formatMoney(
          this.chartData.salesTotal,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalReceipts() {
      if (this.chartData && this.chartData.totalReceipts) {
        return this.$utils.formatMoney(
          this.chartData.totalReceipts,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalExpenses() {
      if (this.chartData && this.chartData.totalExpenses) {
        return this.$utils.formatMoney(
          this.chartData.totalExpenses,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalNetProfit() {
      if (this.chartData && this.chartData.netProfit) {
        return this.$utils.formatMoney(
          this.chartData.netProfit,
          this.defaultCurrency
        )
      }
      return 0
    },
  },
  watch: {
    $route(to, from) {
      this.loadCustomer()
      this.selectedYear = 'This year'
    },
  },
  created() {
    this.loadCustomer()
  },
  methods: {
    ...mapActions('customer', ['fetchViewCustomer']),

    async loadCustomer() {
      this.isLoaded = false
      let response = await this.fetchViewCustomer({ id: this.$route.params.id })
      if (response.data) {
        this.chartData = response.data.chartData
      }
      this.isLoaded = false
    },
    async onChangeYear(data) {
      if (data == 'Previous year') {
        let response = await this.fetchViewCustomer({
          id: this.$route.params.id,
          previous_year: true,
        })
        if (response.data) {
          this.chartData = response.data.chartData
        }
        return true
      }
      let response = await this.fetchViewCustomer({ id: this.$route.params.id })
      if (response.data) {
        this.chartData = response.data.chartData
      }
      return true
    },
  },
}
</script>

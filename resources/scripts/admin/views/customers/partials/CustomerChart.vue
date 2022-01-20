<template>
  <BaseCard class="flex flex-col mt-6">
    <ChartPlaceholder v-if="customerStore.isFetchingViewData" />

    <div v-else class="grid grid-cols-12">
      <div class="col-span-12 xl:col-span-9 xxl:col-span-10">
        <div class="flex justify-between mt-1 mb-6">
          <h6 class="flex items-center">
            <BaseIcon name="ChartSquareBarIcon" class="h-5 text-primary-400" />
            {{ $t('dashboard.monthly_chart.title') }}
          </h6>

          <div class="w-40 h-10">
            <BaseMultiselect
              v-model="selectedYear"
              :options="years"
              :allow-empty="false"
              :show-labels="false"
              :placeholder="$t('dashboard.select_year')"
              :can-deselect="false"
              @select="onChangeYear"
            />
          </div>
        </div>

        <LineChart
          v-if="isLoading"
          :invoices="getChartInvoices"
          :expenses="getChartExpenses"
          :receipts="getReceiptTotals"
          :income="getNetProfits"
          :labels="getChartMonths"
          class="sm:w-full"
        />
      </div>

      <div
        class="
          grid
          col-span-12
          mt-6
          text-center
          xl:mt-0
          sm:grid-cols-4
          xl:text-right xl:col-span-3 xl:grid-cols-1
          xxl:col-span-2
        "
      >
        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_sales') }}
          </span>
          <br />
          <span
            v-if="isLoading"
            class="block mt-1 text-xl font-semibold leading-8"
          >
            <BaseFormatMoney
              :amount="chartData.salesTotal"
              :currency="data.currency"
            />
          </span>
        </div>

        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_receipts') }}
          </span>
          <br />

          <span
            v-if="isLoading"
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #00c99c"
          >
            <BaseFormatMoney
              :amount="chartData.totalExpenses"
              :currency="data.currency"
            />
          </span>
        </div>

        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.total_expense') }}
          </span>
          <br />
          <span
            v-if="isLoading"
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #fb7178"
          >
            <BaseFormatMoney
              :amount="chartData.totalExpenses"
              :currency="data.currency"
            />
          </span>
        </div>

        <div class="px-6 py-2">
          <span class="text-xs leading-5 lg:text-sm">
            {{ $t('dashboard.chart_info.net_income') }}
          </span>
          <br />
          <span
            v-if="isLoading"
            class="block mt-1 text-xl font-semibold leading-8"
            style="color: #5851d8"
          >
            <BaseFormatMoney
              :amount="chartData.netProfit"
              :currency="data.currency"
            />
          </span>
        </div>
      </div>
    </div>

    <CustomerInfo />
  </BaseCard>
</template>

<script setup>
import CustomerInfo from './CustomerInfo.vue'
import LineChart from '@/scripts/admin/components/charts/LineChart.vue'
import { ref, computed, watch, reactive, inject } from 'vue'
import { useCustomerStore } from '@/scripts/admin/stores/customer'
import { useRoute } from 'vue-router'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import ChartPlaceholder from './CustomerChartPlaceholder.vue'

const companyStore = useCompanyStore()
const customerStore = useCustomerStore()
const utils = inject('utils')

const route = useRoute()

let isLoading = ref(false)
let chartData = reactive({})
let data = reactive({})
let years = reactive(['This year', 'Previous year'])
let selectedYear = ref('This year')

const getChartExpenses = computed(() => {
  if (chartData.expenseTotals) {
    return chartData.expenseTotals
  }
  return []
})

const getNetProfits = computed(() => {
  if (chartData.netProfits) {
    return chartData.netProfits
  }
  return []
})

const getChartMonths = computed(() => {
  if (chartData && chartData.months) {
    return chartData.months
  }
  return []
})

const getReceiptTotals = computed(() => {
  if (chartData.receiptTotals) {
    return chartData.receiptTotals
  }
  return []
})

const getChartInvoices = computed(() => {
  if (chartData.invoiceTotals) {
    return chartData.invoiceTotals
  }

  return []
})

watch(
  route,
  () => {
    if (route.params.id) {
      loadCustomer()
    }
    selectedYear.value = 'This year'
  },
  { immediate: true }
)

async function loadCustomer() {
  isLoading.value = false
  let response = await customerStore.fetchViewCustomer({
    id: route.params.id,
  })

  if (response.data) {
    Object.assign(chartData, response.data.meta.chartData)
    Object.assign(data, response.data.data)
  }

  isLoading.value = true
}

async function onChangeYear(data) {
  let params = {
    id: route.params.id,
  }

  data === 'Previous year'
    ? (params.previous_year = true)
    : (params.this_year = true)

  let response = await customerStore.fetchViewCustomer(params)

  if (response.data.meta.chartData) {
    Object.assign(chartData, response.data.meta.chartData)
  }

  return true
}
</script>

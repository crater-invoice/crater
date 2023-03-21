<template>
  <div class="graph-container h-[300px]">
    <canvas id="graph" ref="graph" />
  </div>
</template>

<script setup>
import Chart from 'chart.js'
import {
  ref,
  reactive,
  computed,
  onMounted,
  watchEffect,
  inject,
  watch,
} from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useGlobalStore } from '@/scripts/admin/stores/global'

const utils = inject('utils')

const props = defineProps({
  labels: {
    type: Array,
    require: true,
    default: Array,
  },
  values: {
    type: Array,
    require: true,
    default: Array,
  },
  invoices: {
    type: Array,
    require: true,
    default: Array,
  },
  expenses: {
    type: Array,
    require: true,
    default: Array,
  },
  receipts: {
    type: Array,
    require: true,
    default: Array,
  },
  income: {
    type: Array,
    require: true,
    default: Array,
  },
})

const isDarkModeOn = document.documentElement.classList.contains('dark')
let myLineChart = null
const graph = ref(null)
const companyStore = useCompanyStore()
const globalStore = useGlobalStore()
const defaultCurrency = computed(() => {
  return companyStore.selectedCompanyCurrency
})

watchEffect(() => {
  if (props.labels) {
    if (myLineChart) {
      myLineChart.reset()
      update()
    }
  }
})

watch(
  () => globalStore.isDarkModeOn,
  () => {
    myLineChart.reset()
    updateColors()
  }
)

onMounted(() => {
  let context = graph.value.getContext('2d')
  let options = reactive({
    responsive: true,
    maintainAspectRatio: false,
    tooltips: {
      enabled: true,
      callbacks: {
        label: function (tooltipItem, data) {
          return utils.formatMoney(
            Math.round(tooltipItem.value * 100),
            defaultCurrency.value
          )
        },
      },
    },
    legend: {
      display: false,
    },
  })

  const salesColor = globalStore.isDarkModeOn ? '#ffffff' : '#040405'

  let data = reactive({
    labels: props.labels,
    datasets: [
      {
        label: 'Sales',
        fill: false,
        lineTension: 0.3,
        backgroundColor: 'rgba(230, 254, 249)',
        borderColor: salesColor,
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: salesColor,
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointHoverRadius: 5,
        pointHoverBackgroundColor: salesColor,
        pointHoverBorderColor: 'rgba(220,220,220,1)',
        pointHoverBorderWidth: 2,
        pointRadius: 4,
        pointHitRadius: 10,
        data: props.invoices.map((invoice) => invoice / 100),
      },
      {
        label: 'Receipts',
        fill: false,
        lineTension: 0.3,
        backgroundColor: 'rgba(230, 254, 249)',
        borderColor: 'rgb(2, 201, 156)',
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: 'rgb(2, 201, 156)',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointHoverRadius: 5,
        pointHoverBackgroundColor: 'rgb(2, 201, 156)',
        pointHoverBorderColor: 'rgba(220,220,220,1)',
        pointHoverBorderWidth: 2,
        pointRadius: 4,
        pointHitRadius: 10,
        data: props.receipts.map((receipt) => receipt / 100),
      },
      {
        label: 'Expenses',
        fill: false,
        lineTension: 0.3,
        backgroundColor: 'rgba(245, 235, 242)',
        borderColor: 'rgb(255,0,0)',
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: 'rgb(255,0,0)',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointHoverRadius: 5,
        pointHoverBackgroundColor: 'rgb(255,0,0)',
        pointHoverBorderColor: 'rgba(220,220,220,1)',
        pointHoverBorderWidth: 2,
        pointRadius: 4,
        pointHitRadius: 10,
        data: props.expenses.map((expense) => expense / 100),
      },
      {
        label: 'Net Income',
        fill: false,
        lineTension: 0.3,
        backgroundColor: 'rgba(236, 235, 249)',
        borderColor: 'rgba(88, 81, 216, 1)',
        borderCapStyle: 'butt',
        borderDash: [],
        borderDashOffset: 0.0,
        borderJoinStyle: 'miter',
        pointBorderColor: 'rgba(88, 81, 216, 1)',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointHoverRadius: 5,
        pointHoverBackgroundColor: 'rgba(88, 81, 216, 1)',
        pointHoverBorderColor: 'rgba(220,220,220,1)',
        pointHoverBorderWidth: 2,
        pointRadius: 4,
        pointHitRadius: 10,
        data: props.income.map((_i) => _i / 100),
      },
    ],
  })

  myLineChart = new Chart(context, {
    type: 'line',
    data: data,
    options: options,
  })
})

function update() {
  myLineChart.data.labels = props.labels
  myLineChart.data.datasets[0].data = props.invoices.map(
    (invoice) => invoice / 100
  )
  myLineChart.data.datasets[1].data = props.receipts.map(
    (receipt) => receipt / 100
  )
  myLineChart.data.datasets[2].data = props.expenses.map(
    (expense) => expense / 100
  )
  myLineChart.data.datasets[3].data = props.income.map((_i) => _i / 100)
  myLineChart.update({
    lazy: true,
  })
}

function updateColors() {
  const newColor = globalStore.isDarkModeOn ? '#ffffff' : '#040405'

  myLineChart.data.datasets[0].borderColor = newColor
  myLineChart.data.datasets[0].pointBorderColor = newColor
  myLineChart.data.datasets[0].pointHoverBackgroundColor = newColor
}
</script>

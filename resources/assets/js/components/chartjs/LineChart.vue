<template>
  <div class="graph-container">
    <canvas id="graph" ref="graph" />
  </div>
</template>

<script>
import Chart from 'chart.js'
import { mapGetters } from 'vuex'

export default {
  props: {
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
    formatMoney: {
      type: Function,
      require: false,
      default: Function,
    },
    FormatGraphMoney: {
      type: Function,
      require: false,
      default: Function,
    },
  },

  computed: {
    ...mapGetters('company', ['defaultCurrency']),
  },

  watch: {
    labels(val) {
      this.update()
    },
  },

  mounted() {
    let self = this
    let context = this.$refs.graph.getContext('2d')
    let options = {
      responsive: true,
      maintainAspectRatio: false,
      tooltips: {
        enabled: true,
        callbacks: {
          label: function (tooltipItem, data) {
            return self.FormatGraphMoney(
              Math.round(tooltipItem.value * 100),
              self.defaultCurrency
            )
          },
        },
      },
      legend: {
        display: false,
      },
    }
    let data = {
      labels: this.labels,
      datasets: [
        {
          label: 'Sales',
          fill: false,
          lineTension: 0.3,
          backgroundColor: 'rgba(230, 254, 249)',
          borderColor: '#040405',
          borderCapStyle: 'butt',
          borderDash: [],
          borderDashOffset: 0.0,
          borderJoinStyle: 'miter',
          pointBorderColor: '#040405',
          pointBackgroundColor: '#fff',
          pointBorderWidth: 1,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#040405',
          pointHoverBorderColor: 'rgba(220,220,220,1)',
          pointHoverBorderWidth: 2,
          pointRadius: 4,
          pointHitRadius: 10,
          data: this.invoices.map((invoice) => invoice / 100),
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
          data: this.receipts.map((receipt) => receipt / 100),
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
          data: this.expenses.map((expense) => expense / 100),
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
          data: this.income.map((_i) => _i / 100),
        },
      ],
    }

    this.myLineChart = new Chart(context, {
      type: 'line',
      data: data,
      options: options,
    })
  },

  methods: {
    update() {
      this.myLineChart.data.labels = this.labels
      this.myLineChart.data.datasets[0].data = this.invoices.map(
        (invoice) => invoice / 100
      )
      this.myLineChart.data.datasets[1].data = this.receipts.map(
        (receipt) => receipt / 100
      )
      this.myLineChart.data.datasets[2].data = this.expenses.map(
        (expense) => expense / 100
      )
      this.myLineChart.data.datasets[3].data = this.income.map((_i) => _i / 100)
      this.myLineChart.update({
        lazy: true,
      })
    },

    beforeDestroy() {
      this.myLineChart.destroy()
    },
  },
}
</script>

<style scoped>
.graph-container {
  height: 300px;
}
</style>

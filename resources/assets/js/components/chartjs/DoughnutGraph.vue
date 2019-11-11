<template>
  <div class="graph-container">
    <canvas
      id="graph"
      ref="graph"/>
  </div>
</template>

<script>
import Chart from 'chart.js'

export default {
  props: {
    labels: {
      type: Array,
      require: true,
      default: Array
    },
    values: {
      type: Array,
      require: true,
      default: Array
    },
    bgColors: {
      type: Array,
      require: true,
      default: Array
    },
    hoverBgColors: {
      type: Array,
      require: true,
      default: Array
    }
  },

  mounted () {
    let context = this.$refs.graph.getContext('2d')
    let options = {
      responsive: true,
      maintainAspectRatio: false
    }

    let data = {
      labels: this.labels,
      datasets: [
        {
          data: this.values,
          backgroundColor: this.bgColors,
          hoverBackgroundColor: this.hoverBgColors
        }
      ]
    }

    this.myDoughnutChart = new Chart(context, {
      type: 'doughnut',
      data: data,
      options: options
    })
  },

  beforeDestroy () {
    this.myDoughnutChart.destroy()
  }
}
</script>

<style scoped>
.graph-container {
  height: 300px;
}
</style>

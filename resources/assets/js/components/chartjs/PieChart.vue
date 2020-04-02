<template>
  <div class="graph-container">
    <canvas
      id="graph"
      ref="graph" />
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

    this.pieChart = new Chart(context, {
      type: 'pie',
      data: data,
      options: options
    })
  },

  beforeDestroy () {
    this.pieChart.destroy()
  }
}
</script>

<style scoped>
.graph-container {
  height: 300px;
}
</style>

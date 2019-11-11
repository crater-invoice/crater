<template>
  <div class="graph-container">
    <canvas
      id="graph"
      ref="graph"
    />
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
    }
  },

  mounted () {
    let context = this.$refs.graph.getContext('2d')
    let options = {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false
      }
    }

    let data = {
      labels: this.labels,
      datasets: [
        {
          label: 'My First dataset',
          backgroundColor: 'rgba(79, 196, 127,0.2)',
          borderColor: 'rgba(79, 196, 127,1)',
          borderWidth: 1,
          hoverBackgroundColor: 'rgba(79, 196, 127,0.4)',
          hoverBorderColor: 'rgba(79, 196, 127,1)',
          data: this.values
        }
      ]
    }

    this.myBarChart = new Chart(context, {
      type: 'bar',
      data: data,
      options: options
    })
  },

  beforeDestroy () {
    this.myBarChart.destroy()
  }
}
</script>

<style scoped>
.graph-container {
  height: 300px;
}
</style>

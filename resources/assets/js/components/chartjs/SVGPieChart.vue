<template>
  <div class="graph-container easy-pie-chart">
    <svg width="100%" height="100%" viewBox="0 0 34 34" class="donut">
      <circle :stroke-width="strokeWidth" class="donut-segment" cx="17" cy="17" r="15.91549430918954" fill="transparent" :stroke="strokeColor" stroke-dasharray="100 0" />
      <circle :stroke-width="strokeWidth" :stroke="color" :stroke-dasharray="successProgress" class="donut-segment" cx="17" cy="17" r="15.91549430918954" fill="transparent" />
      <!-- <g class="chart-text">
        <text :style="'fill:' + color" x="48%" y="50%" class="chart-number" >
          {{ progress }}
        </text>
        <text :style="'fill:' + color" x="73%" y="50%" class="chart-label" >
          %
        </text>
      </g> -->
    </svg>
  </div>
</template>
<script>
export default {
  props: {
    values: {
      type: Number,
      require: true,
      default: 100
    },
    strokeWidth: {
      type: Number,
      require: false,
      default: 1.2
    },
    strokeColor: {
      type: String,
      require: true,
      default: '#eeeeee'
    },
    color: {
      type: String,
      require: true,
      default: '#007dcc'
    }
  },
  data () {
    return {
      progress: 0
    }
  },
  watch: {
    values (newvalue, oldvalue) {
      if (newvalue !== oldvalue) {
        this.setProgress()
      }
    }
  },
  computed: {
    successProgress () {
      return this.progress + ' ' + (100 - this.progress)
    },
    remainProgress () {
      return 100 - this.progress + ' ' + this.progress
    },
  },
  mounted () {
    this.setProgress()
  },
  methods: {
    setProgress () {
      let self = this
      for (let i = 0; i < this.values; i++) {
        setTimeout(function () {
          ++self.progress
        }, 15 * i)
      }
    }
  }
}
</script>
<style scoped>
.chart-text {
  font: 6px "Montserrat", Arial, sans-serif;
  fill: #000;
  -moz-transform: translateY(0.25em);
  -ms-transform: translateY(0.25em);
  -webkit-transform: translateY(0.25em);
  transform: translateY(0.5em);
}
.chart-number {
  font-size: 8px;
  line-height: 1;
  text-anchor: middle;
}
.chart-label {
  font-size: 5px;
  text-transform: uppercase;
  text-anchor: middle;
}
</style>
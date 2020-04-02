<template>
  <div class="setting-list-box">
    <div id="myApp list-box-container">
      <!-- <v-select
        :value.sync="selected"
        :options="list"
        :on-change ="setValue"
      /> -->
    </div>
  </div>
</template>
<script>
// import vSelect from 'vue-select'
export default {
  // components: {vSelect},
  props: {
    type: {
      type: String,
      default: null
    },
    Options: {
      type: [Array, Object],
      required: false,
      default () {
        return []
      }
    },
    getData: {
      type: Object,
      default () {
        return {}
      }
    },
    currentData: {
      type: [String, Number],
      default: null
    }
  },
  data () {
    return {
      selected: null,
      list: []
    }
  },
  mounted () {
    window.setTimeout(() => {
      this.setList()
      if (this.currentData !== null || this.currentData !== '') {
        this.defaultValue(this.currentData)
      }
    }, 1000)
  },
  methods: {
    setList () {
      if (this.type === 'currencies') {
        for (let i = 0; i < this.Options.length; i++) {
          this.list.push(this.Options[i].name + ' - ' + this.Options[i].code)
        }
      } else if (this.type === 'time_zones' || this.type === 'languages' || this.type === 'date_formats') {
        for (let key in this.Options) {
          this.list.push(this.Options[key])
        }
      }
    },
    setValue (val) {
      if (this.type === 'currencies') {
        for (let i = 0; i < this.Options.length; i++) {
          if (val === this.Options[i].name + ' - ' + this.Options[i].code) {
            this.getData.currency = this.Options[i].id
            break
          }
        }
      } else if (this.type === 'time_zones') {
        for (let key in this.Options) {
          if (val === this.Options[key]) {
            this.getData.time_zone = key
            break
          }
        }
      } else if (this.type === 'languages') {
        for (let key in this.Options) {
          if (val === this.Options[key]) {
            this.getData.language = key
            break
          }
        }
      } else if (this.type === 'date_formats') {
        for (let key in this.Options) {
          if (val === this.Options[key]) {
            this.getData.date_format = key
            break
          }
        }
      }
    },
    defaultValue (val) {
      if (this.type === 'currencies') {
        for (let i = 0; i < this.Options.length; i++) {
          if (Number(val) === this.Options[i].id) {
            this.selected = this.Options[i].name + ' - ' + this.Options[i].code
            break
          }
        }
      } else if (this.type === 'time_zones') {
        for (let key in this.Options) {
          if (val === key) {
            this.selected = this.Options[key]
            break
          }
        }
      } else if (this.type === 'languages') {
        for (let key in this.Options) {
          if (val === key) {
            this.selected = this.Options[key]
            break
          }
        }
      } else if (this.type === 'date_formats') {
        for (let key in this.Options) {
          if (val === key) {
            this.selected = this.Options[key]
            break
          }
        }
      }
    }
  }
}
</script>

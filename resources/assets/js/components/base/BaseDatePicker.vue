<template>
  <div>
    <sw-date-picker
      ref="BaseDatepicker"
      v-model="date"
      :config="config"
      :placeholder="placeholder"
      :disabled="disabled"
      :invalid="invalid"
      :name="name"
      :tabindex="tabindex"
      @input="onDateChange"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import SwDatePicker from '@bytefury/spacewind/src/components/SwDatePicker'
import moment from 'moment'

export default {
  components: {
    SwDatePicker,
  },
  props: {
    placeholder: {
      type: String,
      default: null,
    },
    invalid: {
      type: Boolean,
      default: false,
    },
    enableTime: {
      type: Boolean,
      default: false,
    },
    time_24hr: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: null,
    },
    value: {
      type: [String, Date],
      default: () => moment(new Date()),
    },
    tabindex: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      date: null,
      config: {
        altInput: true,
        enableTime: this.enableTime,
        time_24hr: this.time_24hr,
      },
    }
  },
  computed: {
    ...mapGetters('company', {
      carbonFormat: 'getCarbonDateFormat',
      momentFormat: 'getMomentDateFormat',
    }),
  },
  watch: {
    // value(val) {
    //   console.log(val)

    //   if (val && !this.enableTime) {
    //     this.date = moment(new Date(val), 'YYYY-MM-DD').format('YYYY-MM-DD')
    //   } else {
    //     this.date = moment(new Date(val), 'YYYY-MM-DD').format(
    //       'YYYY-MM-DD H:m:s'
    //     )
    //   }
    // },
    enableTime(val) {
      this.$set(this.config, 'enableTime', this.enableTime)
    },
    carbonFormat() {
      if (!this.enableTime) {
        this.$set(
          this.config,
          'altFormat',
          this.carbonFormat ? this.carbonFormat : 'd M Y'
        )
      } else {
        this.$set(
          this.config,
          'altFormat',
          this.carbonFormat ? `${this.carbonFormat} H:i ` : 'd M Y H:i'
        )
      }
    },
  },
  mounted() {
    this.$set(this.config, 'enableTime', this.enableTime)

    if (!this.enableTime) {
      this.$set(
        this.config,
        'altFormat',
        this.carbonFormat ? this.carbonFormat : 'd M Y'
      )
    } else {
      this.$set(
        this.config,
        'altFormat',
        this.carbonFormat ? `${this.carbonFormat} H:i ` : 'd M Y H:i'
      )
    }

    if (this.value && !this.enableTime) {
      this.date = moment(new Date(this.value), 'YYYY-MM-DD').format(
        'YYYY-MM-DD'
      )
      return true
    }

    if (this.value) {
      this.date = moment(new Date(this.value), 'YYYY-MM-DD').format(
        'YYYY-MM-DD HH:mm:SS'
      )
    }
  },
  methods: {
    onDateChange(date) {
      this.$emit('input', date)
    },
  },
}
</script>
<style lang="scss">
.flatpickr-calendar.open {
  z-index: 60 !important;
}
</style>

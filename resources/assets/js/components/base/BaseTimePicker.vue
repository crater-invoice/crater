<template>
  <sw-date-picker
    ref="BaseDatepicker"
    v-model="time"
    :config="config"
    :placeholder="placeholder"
    :disabled="disabled"
    :invalid="invalid"
    :name="name"
    :tabindex="tabindex"
    @input="onDateChange"
  />
</template>
<script>
import SwDatePicker from '@bytefury/spacewind/src/components/SwDatePicker'

export default {
  components: {
    SwDatePicker,
  },
  props: {
    invalid: {
      type: Boolean,
      default: false,
      require: false,
    },
    defaultTime: {
      type: String,
      default: null,
      require: false,
    },
    hideClearButton: {
      type: String,
      default: null,
      require: false,
    },
    value: {
      type: [String, Object],
      default: '',
    },
    placeholder: {
      type: String,
      default: 'HH:mm:ss',
    },
    tabindex: {
      type: Number,
      default: null,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      time: this.value,
      config: {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
      },
    }
  },
  watch: {
    value() {
      this.time = this.value
    },
  },
  methods: {
    onDateChange(date) {
      this.$emit('input', date)
      this.$emit('change', date)
    },
  },
}
</script>

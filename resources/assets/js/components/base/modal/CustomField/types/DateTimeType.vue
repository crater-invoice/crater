<template>
  <base-date-picker
    v-model="inputValue"
    :enable-time="true"
    @input="$emit('input', inputValue)"
  />
</template>
<script>
import moment from 'moment'
export default {
  props: {
    value: {
      type: String,
      default: null,
    },
    defaultDateTime: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      inputValue: this.value ? this.value : moment().format('YYYY-MM-DD hh:mm'),
    }
  },
  watch: {
    value(data) {
      this.inputValue = data
    },
    defaultDateTime(data) {
      this.dateTimeValue = data
    },
  },
}
</script>

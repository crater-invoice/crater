<template>
  <base-date-picker
    :calendar-button="true"
    v-model="inputValue"
    calendar-button-icon="calendar"
    @input="$emit('input', inputValue)"
  />
</template>
<script>
import moment from 'moment'
export default {
  props: {
    value: {
      type: [String, Date],
      default: null,
    },
  },
  data() {
    return {
      inputValue: moment().format('YYYY-MM-DD'),
    }
  },
  watch: {
    value(data) {
      this.inputValue = data
    },
  },
}
</script>

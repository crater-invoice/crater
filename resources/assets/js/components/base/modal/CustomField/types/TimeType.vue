<template>
  <base-time-picker
    :value="inputValue"
    v-model="inputValue"
    hide-clear-button
    @input="$emit('input', inputValue)"
  />
</template>
<script>
export default {
  props: {
    value: {
      type: [String, Object],
      default: null,
    },
  },
  data() {
    return {
      inputValue: this.value,
    }
  },
  watch: {
    value(data) {
      this.inputValue = data
    },
  },
}
</script>

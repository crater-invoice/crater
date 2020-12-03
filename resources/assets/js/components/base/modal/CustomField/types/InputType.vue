<template>
  <sw-input
    v-model="inputValue"
    type="text"
    @input="$emit('input', inputValue)"
  />
</template>
<script>
export default {
  props: {
    value: {
      type: String,
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

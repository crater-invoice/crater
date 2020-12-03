<template>
  <sw-textarea
    v-model="inputValue"
    rows="2"
    name="description"
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

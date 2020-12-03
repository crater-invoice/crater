<template>
  <sw-select
    v-model="inputValue"
    :options="inputOptions"
    :taggable="true"
    :show-labels="false"
    label="name"
    track-by="name"
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
    options: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      inputValue: this.value,
      inputOptions: this.options,
    }
  },
  watch: {
    options(data) {
      this.inputOptions = data
      this.inputValue = null
    },
  },
}
</script>

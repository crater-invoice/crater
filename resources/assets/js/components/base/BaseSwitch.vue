<template>
  <div class="base-switch">
    <input
      :id="uniqueId"
      v-model="checkValue"
      type="checkbox"
      @input="handleInput"
      @change="handleChange"
      @keyup="handleKeyupEnter"
      @blur="handleFocusOut"
    >
    <label class="switch-label" :for="uniqueId"/>
  </div>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      required: false,
      default: false
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      id: null,
      checkValue: this.value
    }
  },
  computed: {
    uniqueId () {
      return '_' + Math.random().toString(36).substr(2, 9)
    }
  },
  watch: {
    'value' () {
      this.checkValue = this.value
    }
  },
  methods: {
    handleInput (e) {
      this.$emit('input', e.target.checked)
    },
    handleChange (e) {
      this.$emit('change', this.checkValue)
    },
    handleKeyupEnter (e) {
      this.$emit('keyup', this.checkValue)
    },
    handleFocusOut (e) {
      this.$emit('blur', this.checkValue)
    }
  }
}
</script>

<style scoped>
/* .switch-label {
  margin-bottom: 3px !important
} */
</style>

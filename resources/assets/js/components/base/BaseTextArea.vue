<template>
  <textarea
    v-model="inputValue"
    :rows="rows"
    :cols="cols"
    :disabled="disabled"
    :class="['base-text-area',{'invalid': isFieldValid, 'disabled': disabled}, inputClass]"
    :placeholder="placeholder"
    class="text-area-field"
    @input="handleInput"
    @change="handleChange"
    @keyup="handleKeyupEnter"
  />
</template>

<script>
export default {
  props: {
    rows: {
      type: String,
      default: '4'
    },
    cols: {
      type: String,
      default: '10'
    },
    value: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    invalid: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    icon: {
      type: String,
      default: ''
    },
    inputClass: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      inputValue: this.value
    }
  },
  computed: {
    isFieldValid () {
      return this.invalid
    }
  },
  watch: {
    'value' () {
      this.inputValue = this.value
    }
  },
  methods: {
    handleInput (e) {
      this.$emit('input', this.inputValue)
    },
    handleChange (e) {
      this.$emit('change', this.inputValue)
    },
    handleKeyupEnter (e) {
      this.$emit('keyup', this.inputValue)
    }
  }
}
</script>

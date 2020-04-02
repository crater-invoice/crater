<template>
  <div class="base-prefix-input" @click="focusInput">
    <font-awesome-icon v-if="icon" :icon="icon" class="icon" />
    <p class="prefix-label"><span class="mr-1">{{ prefix }}</span>-</p>
    <input
      ref="basePrefixInput"
      v-model="inputValue"
      :type="type"
      class="prefix-input-field"
      @input="handleInput"
      @change="handleChange"
      @keyup="handleKeyupEnter"
      @keydown="handleKeyDownEnter"
      @blur="handleFocusOut"
    >
  </div>
</template>

<script>
export default {
  props: {
    prefix: {
      type: String,
      default: null,
      required: true
    },
    icon: {
      type: String,
      default: null
    },
    value: {
      type: [String, Number, File],
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    }
  },
  data () {
    return {
      inputValue: this.value
    }
  },
  watch: {
    'value' () {
      this.inputValue = this.value
    }
  },
  methods: {
    focusInput () {
      this.$refs.basePrefixInput.focus()
    },
    handleInput (e) {
      this.$emit('input', this.inputValue)
    },
    handleChange (e) {
      this.$emit('change', this.inputValue)
    },
    handleKeyupEnter (e) {
      this.$emit('keyup', this.inputValue)
    },
    handleKeyDownEnter (e) {
      this.$emit('keydown', e, this.inputValue)
    },
    handleFocusOut (e) {
      this.$emit('blur', this.inputValue)
    }
  }
}
</script>

<template>
  <div class="base-input">
    <font-awesome-icon v-if="icon && isAlignLeftIcon" :icon="icon" class="left-icon"/>
    <input
      ref="baseInput"
      v-model="inputValue"
      :type="toggleType"
      :disabled="disabled"
      :readonly="readOnly"
      :name="name"
      :tabindex="tabIndex"
      :class="[{'input-field-left-icon': icon && isAlignLeftIcon ,'input-field-right-icon': icon && !isAlignLeftIcon ,'invalid': isFieldValid, 'disabled': disabled, 'small-input': small}, inputClass]"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      class="input-field"
      @input="handleInput"
      @change="handleChange"
      @keyup="handleKeyupEnter"
      @keydown="handleKeyDownEnter"
      @blur="handleFocusOut"
    >
    <div v-if="showPassword && isAlignLeftIcon" style="cursor: pointer" @click="showPass = !showPass" >
      <font-awesome-icon :icon="!showPass ?'eye': 'eye-slash'" class="right-icon" />
    </div>
    <font-awesome-icon v-if="icon && !isAlignLeftIcon" :icon="icon" class="right-icon" />
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    tabIndex: {
      type: String,
      default: ''
    },
    value: {
      type: [String, Number, File],
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
    readOnly: {
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
    },
    small: {
      type: Boolean,
      default: false
    },
    alignIcon: {
      type: String,
      default: 'left'
    },
    autocomplete: {
      type: String,
      default: 'on'
    },
    showPassword: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      inputValue: this.value,
      focus: false,
      showPass: false
    }
  },
  computed: {
    isFieldValid () {
      return this.invalid
    },
    isAlignLeftIcon () {
      if (this.alignIcon === 'left') {
        return true
      }
      return false
    },
    toggleType () {
      if (this.showPass) {
        return 'text'
      }
      return this.type
    }
  },
  watch: {
    'value' () {
      this.inputValue = this.value
    },
    focus () {
      this.focusInput()
    }
  },
  mounted () {
    this.focusInput()
  },
  methods: {
    focusInput () {
      if (this.focus) {
        this.$refs.baseInput.focus()
      }
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

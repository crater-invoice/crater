<template>
  <div v-click-outside="hide">
    <sw-input
      v-focus
      v-show="edit"
      ref="editable"
      :value="valueLocal"
      class="mt-2"
      @blur.native="hide"
      @keyup.enter.native="hide"
    >
    </sw-input>
    <div v-show="!edit" class="px-3 py-2 mt-2" @click="show">
      {{ valueLocal }}
    </div>
  </div>
</template>

<script>
import ClickOutside from 'vue-click-outside'
export default {
  directives: {
    focus: {
      inserted(el) {
        el.focus()
      },
    },
    ClickOutside,
  },

  props: {
    value: {
      type: String,
      default: null,
    },
  },

  data() {
    return {
      edit: false,
      valueLocal: this.value,
    }
  },

  watch: {
    value: function () {
      this.valueLocal = this.value
    },
  },
  methods: {
    hide() {
      if (this.edit) {
        this.valueLocal = this.$refs.editable.inputValue
        this.edit = false
        this.$emit('input', this.valueLocal)
      }
    },
    show() {
      this.edit = true
    },
  },
}
</script>

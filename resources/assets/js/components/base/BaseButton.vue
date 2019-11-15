<template>
  <button
    :type="type"
    :class="btnClass"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <font-awesome-icon
      v-if="icon && !loading && !rightIcon"
      :class="iconClass"
      :icon="icon"
      class="vue-icon icon-left"
    />
    <font-awesome-icon
      v-if="loading"
      :class="iconClass"
      icon="spinner"
      class="fa-spin"
    />
    <slot />
    <font-awesome-icon
      v-if="icon && !loading && rightIcon"
      :class="iconClass"
      :icon="icon"
      class="vue-icon icon-right"
    />
  </button>
</template>

<script>

export default {
  props: {
    icon: {
      type: String,
      required: false,
      default: ''
    },
    color: {
      type: String,
      required: false,
      default: ''
    },
    round: {
      type: Boolean,
      required: false,
      default: false
    },
    outline: {
      type: Boolean,
      required: false,
      default: false
    },
    size: {
      type: String,
      required: false,
      default: 'default'
    },
    loading: {
      type: Boolean,
      required: false,
      default: false
    },
    block: {
      type: Boolean,
      required: false,
      default: false
    },
    iconButton: {
      type: Boolean,
      required: false,
      default: false
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    rightIcon: {
      type: Boolean,
      required: false,
      default: false
    },
    type: {
      type: String,
      required: false,
      default: 'button'
    }
  },
  computed: {
    btnClass () {
      if (this.isCustomStyle) {
        return ''
      }

      let btnClass = 'base-button '

      switch (this.color) {
        case 'success':
          if (this.outline) {
            btnClass += `btn btn-outline-success `
          } else {
            btnClass += `btn btn-success `
          }
          break

        case 'danger':
          if (this.outline) {
            btnClass += `btn btn-outline-danger `
          } else {
            btnClass += `btn btn-danger `
          }
          break

        case 'warning':
          if (this.outline) {
            btnClass += `btn btn-outline-warning `
          } else {
            btnClass += `btn btn-warning `
          }
          break

        case 'info':
          if (this.outline) {
            btnClass += `btn btn-outline-info `
          } else {
            btnClass += `btn btn-info `
          }
          break

        case 'theme':
          if (this.outline) {
            btnClass += `btn btn-outline-primary `
          } else {
            btnClass += `btn btn-primary `
          }
          break

        case 'theme-light':
          if (this.outline) {
            btnClass += `btn btn-outline-light `
          } else {
            btnClass += `btn btn-light `
          }
          break

        default:
          if (this.outline) {
            btnClass += `btn btn-outline-dark `
          } else {
            btnClass += `btn btn-dark `
          }
          break
      }

      switch (this.size) {
        case 'large':
          btnClass += 'btn-lg '
          break

        case 'small':
          btnClass += 'btn-sm '
          break

        default:
          btnClass += 'default-size '
      }

      if (this.block) {
        btnClass += 'btn-block '
      }

      if (this.disabled) {
        btnClass += ' btn-cursor-not-allowed'
      }

      return btnClass
    },
    iconClass () {
      if (this.loading || !this.iconButton) {
        if (this.rightIcon) {
          return 'ml-2'
        }
        return 'mr-2'
      }
      return 'icon-button'
    }
  },
  methods: {
    handleClick (e) {
      this.$emit('click')
    }
  }
}
</script>

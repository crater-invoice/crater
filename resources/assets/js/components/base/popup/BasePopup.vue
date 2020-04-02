<template>
  <div v-click-outside="clickOutsideMenu" class="search-select" >
    <div
      class="activator"
      @click="toggleSearchMenu">
      <slot name="activator" />
    </div>
    <transition name="fade">
      <div
        v-if="showMenu"
        :class="{'selector-menu-above': isAbove}"
        class="selector-menu"
      >

        <slot />

      </div>
    </transition>
  </div>
</template>

<script>
export default {
  props: {
    toggle: {
      type: Boolean,
      default: true
    },
    openDirection: {
      type: String,
      default: ''
    },
    maxHeight: {
      type: Number,
      default: 180
    }
  },
  data () {
    return {
      showMenu: false,
      preferredOpenDirection: 'below',
      optimizedHeight: null
    }
  },
  computed: {
    isAbove () {
      if (this.openDirection === 'above' || this.openDirection === 'top') {
        return true
      } else if (this.openDirection === 'below' || this.openDirection === 'bottom') {
        return false
      } else {
        return this.preferredOpenDirection === 'above'
      }
    }
  },
  methods: {
    toggleSearchMenu () {
      this.adjustPosition()
      if (this.toggle) {
        this.showMenu = !this.showMenu
      } else {
        this.showMenu = true
      }
    },
    clickOutsideMenu () {
      this.showMenu = false
    },
    open () {
      this.showMenu = true
    },
    close () {
      this.showMenu = false
    },
    adjustPosition () {
      if (typeof window === 'undefined') return

      const spaceAbove = this.$el.getBoundingClientRect().top
      const spaceBelow = window.innerHeight - this.$el.getBoundingClientRect().bottom
      const hasEnoughSpaceBelow = spaceBelow > this.maxHeight

      if (hasEnoughSpaceBelow || spaceBelow > spaceAbove || this.openDirection === 'below' || this.openDirection === 'bottom') {
        this.preferredOpenDirection = 'below'
        this.optimizedHeight = Math.min(spaceBelow - 20, this.maxHeight)
      } else {
        this.preferredOpenDirection = 'above'
        this.optimizedHeight = Math.min(spaceAbove - 20, this.maxHeight)
      }
    }
  }
}
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>

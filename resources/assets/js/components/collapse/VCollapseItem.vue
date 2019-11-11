<template>
  <div :class="['collapse-group-item', { active: isCollapseOpen } ]">
    <div v-if="!itemTrigger" class="collapse-item-title" @click="toggleCollapse">
      <slot name="item-title"/>
    </div>
    <div v-else class="collapse-item-title">
      <slot name="item-title" :trigger="toggleCollapse"/>
    </div>
    <transition
      :duration="{ enter: 0 }"
      name="slide"
      @after-enter="afterEnter"
      @after-leave="afterLeave"
    >
      <div
        v-show="isCollapseOpen"
        v-if="hasChild"
        ref="collapseItems"
        :style="'max-height:' + height + 'px'"
        class="collapse-group-items"
      >
        <slot/>
      </div>
    </transition>
  </div>
</template>
<script>

export default {
  props: {
    activeUrl: {
      type: String,
      require: true,
      default: ''
    },
    isActive: {
      type: Boolean,
      require: true,
      default: false
    },
    itemTrigger: {
      type: Boolean,
      require: true,
      default: false
    }
  },
  data () {
    return {
      height: '',
      originalHeight: '',
      isCollapseOpen: true,
      hasChild: true,
      accordion: this.$parent.accordion
    }
  },
  mounted () {
    this.$nextTick(() => {
      if (this.accordion === true) {
        this.hasActive()
      } else {
        this.isCollapseOpen = false
      }
      this.height = this.originalHeight = this.$refs.collapseItems.clientHeight

      if (this.$refs.collapseItems.children.length === 0) {
        this.hasChild = false
      }
    })
  },
  methods: {
    hasActiveUrl () {
      return this.$route.path.indexOf(this.activeUrl) > -1
    },
    hasActive () {
      if (this.isActive) {
        this.isCollapseOpen = this.isActive
      } else {
        if (this.activeUrl) {
          this.isCollapseOpen = this.hasActiveUrl()
        } else {
          this.isCollapseOpen = false
        }
      }
    },
    toggleCollapse () {
      let self = this
      if (this.accordion) {
        if (this.isCollapseOpen === false) {
          this.$parent.$children.filter((value) => {
            if (value !== self) {
              if (value.isCollapseOpen === true) {
                value.isCollapseOpen = false
              }
            }
          })
        }
      }
      this.isCollapseOpen = !this.isCollapseOpen
    },
    afterEnter () {
      this.height = this.originalHeight
    },
    afterLeave () {
      this.height = 0
    }
  }
}
</script>
<style scoped>
.collapse-group-items {
  overflow: hidden;
  transition: max-height .3s ease-in-out;
}
.slide-enter-active, .slide-leave-active {
  overflow: hidden;
}
.slide-leave-to {
  max-height: 0px !important;
}
</style>

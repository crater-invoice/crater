<template>
  <div class="flex items-center" style="margin-top: 5px">
    <sw-input
      v-model="option"
      type="text"
      style="width: 90%"
      @handleEnter.stop="onAddOption"
    />
    <plus-circle-icon
      class="ml-1 cursor-pointer text-danger"
      @click="onAddOption"
    />
  </div>
</template>
<script>
import { PlusCircleIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    PlusCircleIcon,
  },
  data() {
    return {
      option: null,
    }
  },
  methods: {
    onAddOption() {
      if (
        this.option == null ||
        this.option == '' ||
        this.option == undefined
      ) {
        return true
      }
      this.$emit('onAdd', this.option)
      this.option = null
    },
  },
}
</script>

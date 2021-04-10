<template>
  <sw-switch
    v-model="switchData"
    class="btn-switch"
    @change="onChange"
    style="margin-top: -15px"
  />
</template>
<script>
export default {
  props: {
    type: {
      type: String,
      default: 'text',
      required: false,
    },
    field: {
      type: Object,
      default: null,
      required: true,
    },
  },
  data() {
    return {
      switchData: false,
    }
  },
  mounted() {
    this.switchData = this.field && this.field.defaultAnswer ? true : false
  },
  methods: {
    onChange() {
      this.$emit('update', { field: this.field, value: this.switchData })
    },
  },
}
</script>

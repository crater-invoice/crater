<template>
  <div class="collapse-group">
    <slot/>
  </div>
</template>
<script>
export default {
  props: {
    accordion: {
      type: Boolean,
      require: true,
      default: false
    }
  }
}
</script>

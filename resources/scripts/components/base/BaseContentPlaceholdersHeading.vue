<template>
  <div class="base-content-placeholders-heading">
    <div v-if="box" class="base-content-placeholders-heading__box" />
    <div class="base-content-placeholders-heading__content">
      <div
        class="base-content-placeholders-heading__title"
        style="background: #eee"
      />
      <div class="base-content-placeholders-heading__subtitle" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  box: {
    type: Boolean,
    default: false,
  },
  rounded: {
    type: Boolean,
    default: false,
  },
})
</script>

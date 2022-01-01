<template>
  <span
    class="
      px-2
      py-1
      text-sm
      font-normal
      text-center text-green-800
      uppercase
      bg-success
    "
    :style="{ backgroundColor: bgColor, color }"
  >
    <slot />
  </span>
</template>

<script setup>
const props = defineProps({
  bgColor: {
    type: String,
    default: null,
  },
  color: {
    type: String,
    default: null,
  },
})
</script>

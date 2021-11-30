<template>
  <span
    :class="[
      sucess ? 'bg-green-100 text-green-700 ' : 'bg-red-100 text-red-700',
      'px-2 py-1 text-sm font-normal text-center uppercase',
    ]"
  >
    <slot />
  </span>
</template>

<script setup>
const props = defineProps({
  sucess: {
    type: Boolean,
    default: false,
  },
})
</script>

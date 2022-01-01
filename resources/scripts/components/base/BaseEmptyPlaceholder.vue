<template>
  <div class="flex flex-col items-center justify-center mt-16">
    <div class="flex flex-col items-center justify-center">
      <slot></slot>
    </div>
    <div class="mt-2">
      <label class="font-medium">{{ title }}</label>
    </div>
    <div class="mt-2">
      <label class="text-gray-500">
        {{ description }}
      </label>
    </div>
    <div class="mt-6">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: String,
  },
  description: {
    type: String,
    default: String,
  },
})
</script>

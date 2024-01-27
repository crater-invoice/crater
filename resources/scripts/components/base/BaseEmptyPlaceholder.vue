<template>
  <div class="flex flex-col items-center justify-center mt-16">
    <div class="relative">
      <BaseDarkHighlight class="bg-highlight/[.07] top-2" />

      <div class="relative z-5 flex flex-col items-center">
        <div class="flex flex-col items-center justify-center">
          <slot />
        </div>
        <div class="mt-2">
          <label class="font-medium">{{ title }}</label>
        </div>
        <div class="mt-2 text-center md:text-left">
          <label class="text-gray-500 dark:text-gray-400">
            {{ description }}
          </label>
        </div>
        <div class="mt-6">
          <slot name="actions" />
        </div>
      </div>
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

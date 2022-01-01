<template>
  <div>
    <BaseContentPlaceholders v-if="contentLoading">
      <BaseContentPlaceholdersBox class="w-20 h-5 mb-1" />
      <BaseContentPlaceholdersBox class="w-40 h-5" />
    </BaseContentPlaceholders>

    <div v-else>
      <BaseLabel class="font-normal mb-1">
        {{ label }}
      </BaseLabel>

      <p class="text-sm font-bold leading-5 text-black non-italic">
        {{ value }}

        <slot />
      </p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    default: '',
  },
  contentLoading: {
    type: Boolean,
    default: false,
  },
})
</script>

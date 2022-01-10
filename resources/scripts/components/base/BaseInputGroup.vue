<template>
  <div :class="containerClasses" class="relative w-full text-left">
    <BaseContentPlaceholders v-if="contentLoading">
      <BaseContentPlaceholdersText :lines="1" :class="contentLoadClass" />
    </BaseContentPlaceholders>
    <label
      v-else-if="label"
      :class="labelClasses"
      class="
        flex
        text-sm
        not-italic
        items-center
        font-medium
        text-gray-800
        whitespace-nowrap
        justify-between
      "
    >
      <div>
        {{ label }}
        <span v-show="required" class="text-sm text-red-500"> * </span>
      </div>
      <slot v-if="hasRightLabelSlot" name="labelRight" />
      <BaseIcon
        v-if="tooltip"
        v-tooltip="{ content: tooltip }"
        name="InformationCircleIcon"
        class="h-4 text-gray-400 cursor-pointer hover:text-gray-600"
      />
    </label>
    <div :class="inputContainerClasses">
      <slot></slot>
      <span v-if="helpText" class="text-gray-500 text-xs mt-1 font-light">
        {{ helpText }}
      </span>
      <span v-if="error" class="block mt-0.5 text-sm text-red-500">
        {{ error }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed, useSlots } from 'vue'

const props = defineProps({
  contentLoading: {
    type: Boolean,
    default: false,
  },
  contentLoadClass: {
    type: String,
    default: 'w-16 h-5',
  },
  label: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'vertical',
  },
  error: {
    type: [String, Boolean],
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
  tooltip: {
    type: String,
    default: null,
    required: false,
  },
  helpText: {
    type: String,
    default: null,
    required: false,
  },
})

const containerClasses = computed(() => {
  if (props.variant === 'horizontal') {
    return 'grid md:grid-cols-12 items-center'
  }

  return ''
})

const labelClasses = computed(() => {
  if (props.variant === 'horizontal') {
    return 'relative pr-0 pt-1 mr-3 text-sm md:col-span-4 md:text-right mb-1  md:mb-0'
  }

  return ''
})

const inputContainerClasses = computed(() => {
  if (props.variant === 'horizontal') {
    return 'md:col-span-8 md:col-start-5 md:col-ends-12'
  }

  return 'flex flex-col mt-1'
})

const slots = useSlots()

const hasRightLabelSlot = computed(() => {
  return !!slots.labelRight
})
</script>

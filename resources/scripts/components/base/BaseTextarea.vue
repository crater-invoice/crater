<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      class="w-full"
      :style="`height: ${loadingPlaceholderSize}px`"
    />
  </BaseContentPlaceholders>

  <textarea
    v-else
    v-bind="$attrs"
    ref="textarea"
    :value="modelValue"
    :class="[defaultInputClass, inputBorderClass]"
    :disabled="disabled"
    @input="onInput"
  />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  contentLoading: {
    type: Boolean,
    default: false,
  },
  row: {
    type: Number,
    default: null,
  },
  invalid: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  modelValue: {
    type: [String, Number],
    default: '',
  },
  defaultInputClass: {
    type: String,
    default:
      'box-border w-full px-3 py-2 text-sm not-italic font-normal leading-snug text-left text-black placeholder-gray-400 bg-white border border-gray-200 border-solid rounded outline-none',
  },
  autosize: {
    type: Boolean,
    default: false,
  },
  borderless: {
    type: Boolean,
    default: false,
  },
})

const textarea = ref(null)

const inputBorderClass = computed(() => {
  if (props.invalid && !props.borderless) {
    return 'border-red-400 ring-red-400 focus:ring-red-400 focus:border-red-400'
  } else if (!props.borderless) {
    return 'focus:ring-primary-400 focus:border-primary-400'
  }

  return 'border-none outline-none focus:ring-primary-400 focus:border focus:border-primary-400'
})

const loadingPlaceholderSize = computed(() => {
  switch (props.row) {
    case 2:
      return '56'
    case 4:
      return '94'
    default:
      return '56'
  }
})

const emit = defineEmits(['update:modelValue'])

function onInput(e) {
  emit('update:modelValue', e.target.value)

  if (props.autosize) {
    e.target.style.height = 'auto'
    e.target.style.height = `${e.target.scrollHeight}px`
  }
}

onMounted(() => {
  if (textarea.value && props.autosize) {
    textarea.value.style.height = textarea.value.scrollHeight + 'px'

    if (textarea.value.style.overflow && textarea.value.style.overflow.y) {
      textarea.value.style.overflow.y = 'hidden'
    }

    textarea.value.style.resize = 'none'
  }
})
</script>

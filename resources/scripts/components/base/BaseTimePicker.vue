<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      :class="`w-full ${computedContainerClass}`"
      style="height: 38px"
    />
  </BaseContentPlaceholders>

  <div v-else :class="computedContainerClass" class="relative flex flex-row">
    <svg
      v-if="clockIcon && !hasIconSlot"
      xmlns="http://www.w3.org/2000/svg"
      class="
        absolute
        top-px
        w-4
        h-4
        mx-2
        my-2.5
        text-sm
        not-italic
        font-black
        text-gray-400
        cursor-pointer
      "
      viewBox="0 0 20 20"
      fill="currentColor"
      @click="onClickPicker"
    >
      <path
        fill-rule="evenodd"
        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
        clip-rule="evenodd"
      />
    </svg>

    <slot v-if="clockIcon && hasIconSlot" name="icon" />

    <FlatPickr
      ref="dpt"
      v-model="time"
      v-bind="$attrs"
      :disabled="disabled"
      :config="config"
      :class="[defaultInputClass, inputInvalidClass, inputDisabledClass]"
    />
  </div>
</template>

<script setup>
import FlatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import { computed, reactive, useSlots, ref } from 'vue'
const dpt = ref(null)

const props = defineProps({
  modelValue: {
    type: [String, Date],
    default: () => moment(new Date()),
  },
  contentLoading: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
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
  containerClass: {
    type: String,
    default: '',
  },
  clockIcon: {
    type: Boolean,
    default: true,
  },
  defaultInputClass: {
    type: String,
    default:
      'font-base pl-8 py-2 outline-none focus:ring-primary-400 focus:outline-none focus:border-primary-400 block w-full sm:text-sm border-gray-300 rounded-md text-black',
  },
})

const emit = defineEmits(['update:modelValue'])

const slots = useSlots()

let config = reactive({
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
})

const time = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const hasIconSlot = computed(() => {
  return !!slots.icon
})

function onClickPicker(params) {
  dpt.value.fp.open()
}

const computedContainerClass = computed(() => {
  let containerClass = `${props.containerClass} `

  return containerClass
})

const inputInvalidClass = computed(() => {
  if (props.invalid) {
    return 'border-red-400 ring-red-400 focus:ring-red-400 focus:border-red-400'
  }

  return ''
})

const inputDisabledClass = computed(() => {
  if (props.disabled) {
    return 'border border-solid rounded-md outline-none input-field box-border-2 base-date-picker-input placeholder-gray-400 bg-gray-300 text-gray-600 border-gray-300'
  }

  return ''
})
</script>

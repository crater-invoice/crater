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
      v-if="showCalendarIcon && !hasIconSlot"
      viewBox="0 0 20 20"
      fill="currentColor"
      class="
        absolute
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
      @click="onClickDp"
    >
      <path
        fill-rule="evenodd"
        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
        clip-rule="evenodd"
      ></path>
    </svg>

    <slot v-if="showCalendarIcon && hasIconSlot" name="icon" />

    <FlatPickr
      ref="dp"
      v-model="date"
      v-bind="$attrs"
      :disabled="disabled"
      :config="config"
      :class="[defaultInputClass, inputInvalidClass, inputDisabledClass]"
    />
  </div>
</template>

<script type="text/babel" setup>
import FlatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import { computed, reactive, watch, ref, useSlots } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const dp = ref(null)

const props = defineProps({
  modelValue: {
    type: [String, Date],
    default: () => new Date(),
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
  enableTime: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  showCalendarIcon: {
    type: Boolean,
    default: true,
  },
  containerClass: {
    type: String,
    default: '',
  },
  defaultInputClass: {
    type: String,
    default:
      'font-base pl-8 py-2 outline-none focus:ring-primary-400 focus:outline-none focus:border-primary-400 block w-full sm:text-sm border-gray-200 rounded-md text-black',
  },
  time24hr: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const slots = useSlots()

const companyStore = useCompanyStore()

let config = reactive({
  altInput: true,
  enableTime: props.enableTime,
  time_24hr: props.time24hr,
})

const date = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

const carbonFormat = computed(() => {
  return companyStore.selectedCompanySettings?.carbon_date_format
})

const hasIconSlot = computed(() => {
  return !!slots.icon
})

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
    return 'border border-solid rounded-md outline-none input-field box-border-2 base-date-picker-input placeholder-gray-400 bg-gray-200 text-gray-600 border-gray-200'
  }

  return ''
})

function onClickDp(params) {
  dp.value.fp.open()
}

watch(
  () => props.enableTime,
  (val) => {
    if (props.enableTime) {
      config.enableTime = props.enableTime
    }
  },
  { immediate: true }
)

watch(
  () => carbonFormat,
  () => {
    if (!props.enableTime) {
      config.altFormat = carbonFormat.value ? carbonFormat.value : 'd M Y'
    } else {
      config.altFormat = carbonFormat.value
        ? `${carbonFormat.value} H:i `
        : 'd M Y H:i'
    }
  },
  { immediate: true }
)
</script>

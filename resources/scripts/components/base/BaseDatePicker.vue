<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      :class="`w-full ${computedContainerClass}`"
      style="height: 38px"
    />
  </BaseContentPlaceholders>

  <div v-else :class="computedContainerClass">
    <date-picker
      ref="vCalendar"
      v-model="date"
      :mode="mode"
      :is24hr="time24hr"
      class="w-full"
      color="indigo"
      :input-debounce="500"
      :update-on-input="false"
      :is-range="false"
      trim-weeks
      :is-required="isRequired"
      :popover="{
        visibility: disabled ? 'hidden' : 'focus',
        showDelay: 0,
        hideDelay: 1,
      }"
      :attributes="attrs"
      :model-config="config"
      :masks="masks"
      :is-dark="isDarkModeOn"
      :locale="global.locale"
    >
      <template
        #default="{ inputValue, inputEvents, togglePopover, hidePopover }"
      >
        <!-- calendar icon -->
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
          @click="togglePopover()"
        >
          <path
            fill-rule="evenodd"
            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
            clip-rule="evenodd"
          ></path>
        </svg>

        <slot v-if="showCalendarIcon && hasIconSlot" name="icon" />

        <input
          :value="inputValue"
          :class="[defaultInputClass, inputInvalidClass, inputDisabledClass]"
          readonly
          v-on="inputEvents"
          @blur="hidePopover()"
        />
      </template>

      <template v-if="showExtraOptions" #footer>
        <div
          class="bg-gray-100 dark:bg-gray-800 grid grid-cols-3 gap-2 p-2 border-t dark:border-gray-500 rounded-b-lg"
        >
          <button type="button" class="extra-button" @click="moveToDate(sourceDate)">
            {{ global.t('date_picker.same_day') }}
          </button>

          <button type="button" class="extra-button" @click="withInDays(7)">
            {{ global.t('date_picker.within_7_days') }}
          </button>

          <button type="button" class="extra-button" @click="withInDays(15)">
            {{ global.t('date_picker.within_15_days') }}
          </button>

          <button type="button" class="extra-button" @click="withInDays(30)">
            {{ global.t('date_picker.within_30_days') }}
          </button>

          <button type="button" class="extra-button" @click="withInDays(45)">
            {{ global.t('date_picker.within_45_days') }}
          </button>

          <button type="button" class="extra-button" @click="withInDays(60)">
            {{ global.t('date_picker.within_60_days') }}
          </button>
        </div>
      </template>
    </date-picker>
  </div>
</template>

<script type="text/babel" setup>
import { Calendar, DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'
import { computed, reactive, watch, ref, useSlots } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import moment from 'moment'

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
      'border-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-base pl-8 py-2 outline-none focus:ring-primary-400 focus:outline-none focus:border-primary-400 block w-full sm:text-sm border-gray-200 rounded-md text-black',
  },
  time24hr: {
    type: Boolean,
    default: false,
  },
  isRequired: {
    type: Boolean,
    default: false,
  },
  showExtraOptions: {
    type: Boolean,
    default: false,
  },
  sourceDate: {
    type: [String, Date],
    default: () => new Date(),
  },
})

const emit = defineEmits(['update:modelValue'])
const slots = useSlots()
const companyStore = useCompanyStore()
const { global } = window.i18n
const vCalendar = ref(null)

const hasIconSlot = computed(() => {
  return !!slots.icon
})
const isDarkModeOn = computed(() =>
  document.documentElement.classList.contains('dark')
)

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

// to convert YYYY-MM-DD | YYYY-MM-DD HH:mm format
function convertYMDFormat(date) {
  let format = props.enableTime ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD'
  return date ? moment(date).format(format) : date
}

const date = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

const mode = computed(() => {
  return props.enableTime ? 'dateTime' : 'date'
})

const config = reactive({
  type: 'string',
  mask: 'YYYY-MM-DD', // Uses 'iso' if missing
  //timeAdjust: '00:00:00',
})

const masks = reactive({
  input: null,
  inputDateTime: null,
  inputDateTime24hr: null,
})

const attrs = reactive([
  {
    dates: new Date(),
    highlight: {
      fillMode: 'outline',
    },
    /* popover: {
      label: 'Today Date',
      visibility: 'hover',
    }, */
  },
])

const carbonFormat = computed(() => {
  return companyStore.selectedCompanySettings?.moment_date_format
})

watch(
  () => carbonFormat,
  () => {
    if (!props.enableTime) {
      masks.input = carbonFormat.value ? carbonFormat.value : 'DD MMM YYYY'
      config.mask = 'YYYY-MM-DD'
    } else {
      let timeFormat = 'HH:mm'
      if (props.time24hr) {
        masks.inputDateTime24hr = carbonFormat.value
          ? `${carbonFormat.value} ${timeFormat}`
          : `DD MMM YYYY ${timeFormat}`
      } else {
        masks.inputDateTime = carbonFormat.value
          ? `${carbonFormat.value} ${timeFormat}`
          : `DD MMM YYYY ${timeFormat}`
      }
      config.mask = `YYYY-MM-DD ${timeFormat}`
    }
  },
  { immediate: true }
)

async function moveToDate(_date) {
  const calendar = vCalendar.value
  _date = _date ? _date : convertYMDFormat(new Date())
  date.value = _date
  // await calendar.move(_date)
  calendar.hidePopover()
}

async function withInDays(noOfDays) {
  if (!noOfDays) return false

  let newDate = moment(props.sourceDate).add(noOfDays, 'days').toDate()
  newDate = convertYMDFormat(newDate)
  moveToDate(newDate)
}
</script>

<style scoped>
.extra-button {
  @apply bg-primary-500 text-white text-sm font-semibold px-2 py-1 rounded hover:bg-primary-700;
}
</style>

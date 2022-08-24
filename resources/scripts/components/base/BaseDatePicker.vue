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
import { Arabic } from 'flatpickr/dist/l10n/ar.js'
import { Czech } from 'flatpickr/dist/l10n/cs.js'
import { German } from 'flatpickr/dist/l10n/de.js'
import { Greek } from 'flatpickr/dist/l10n/gr.js'
import { english } from 'flatpickr/dist/l10n/default.js'
import { Spanish } from 'flatpickr/dist/l10n/es.js'
import { Persian } from 'flatpickr/dist/l10n/fa.js'
import { Finnish } from 'flatpickr/dist/l10n/fi.js'
import { French } from 'flatpickr/dist/l10n/fr.js'
import { Hindi } from 'flatpickr/dist/l10n/hi.js'
import { Croatian } from 'flatpickr/dist/l10n/hr.js'
import { Indonesian } from 'flatpickr/dist/l10n/id.js'
import { Italian } from 'flatpickr/dist/l10n/it.js'
import { Japanese } from 'flatpickr/dist/l10n/ja.js'
import { Korean } from 'flatpickr/dist/l10n/ko.js'
import { Lithuanian } from 'flatpickr/dist/l10n/lt.js'
import { Latvian } from 'flatpickr/dist/l10n/lv.js'
import { Dutch } from 'flatpickr/dist/l10n/nl.js'
import { Polish } from 'flatpickr/dist/l10n/pl.js'
import { Portuguese } from 'flatpickr/dist/l10n/pt.js'
import { Romanian } from 'flatpickr/dist/l10n/ro.js'
import { Russian } from 'flatpickr/dist/l10n/ru.js'
import { Slovak } from 'flatpickr/dist/l10n/sk.js'
import { Slovenian } from 'flatpickr/dist/l10n/sl.js'
import { Serbian } from 'flatpickr/dist/l10n/sr.js'
import { Swedish } from 'flatpickr/dist/l10n/sv.js'
import { Thai } from 'flatpickr/dist/l10n/th.js'
import { Turkish } from 'flatpickr/dist/l10n/tr.js'
import { Vietnamese } from 'flatpickr/dist/l10n/vn.js'
import { Mandarin } from 'flatpickr/dist/l10n/zh.js'
import { computed, reactive, watch, ref, useSlots } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useUserStore } from '@/scripts/admin/stores/user'

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
const userStore = useUserStore()

//Localize Flatpicker stuff
const lang = userStore.currentUserSettings.language
let fpLocale = null

switch(lang){
  case 'ar':
        fpLocale = Arabic;
        break;
  case 'cs':
        fpLocale = Czech;
        break;
  case 'de':
        fpLocale = German;
        break;
  case 'el':
      fpLocale = Greek;
      break;
  case 'en':
      fpLocale = english;
      break;
  case 'es':
        fpLocale = Spanish;
        break;
  case 'fa':
        fpLocale = Persian;
        break;
  case 'fi':
        fpLocale = Finnish;
        break;
  case 'fr':
      fpLocale = French;
      break;
  case 'hi':
      fpLocale = Hindi;
      break;
  case 'hr':
        fpLocale = Croatian;
        break;
  case 'id':
        fpLocale = Indonesian;
        break;
  case 'it':
        fpLocale = Italian;
        break;
  case 'ja':
      fpLocale = Japanese;
      break;
  case 'ko':
      fpLocale = Korean;
      break;
  case 'lt':
        fpLocale = Lithuanian;
        break;
  case 'lv':
        fpLocale = Latvian;
        break;
  case 'nl':
        fpLocale = Dutch;
        break;
  case 'pl':
      fpLocale = Polish;
      break;
  case 'pt':
  case 'pt_BR':
      fpLocale = Portuguese;
      break;
  case 'ro':
        fpLocale = Romanian;
        break;
  case 'ru':
        fpLocale = Russian;
        break;
  case 'sk':
      fpLocale = Slovak;
      break;
  case 'sl':
      fpLocale = Slovenian;
      break;
  case 'sr':
        fpLocale = Serbian;
        break;
  case 'sv':
        fpLocale = Swedish;
        break;
  case 'th':
        fpLocale = Thai;
        break;
  case 'tr':
      fpLocale = Turkish;
      break;
  case 'vi':
      fpLocale = Vietnamese;
      break;
  case 'zh':
        fpLocale = Mandarin;
        break;
  default:
      fpLocale = english;
}
let config = reactive({
  altInput: true,
  enableTime: props.enableTime,
  time_24hr: props.time24hr,
  locale: fpLocale
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

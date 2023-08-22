<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      :class="`w-full ${contentLoadClass}`"
      style="height: 38px"
    />
  </BaseContentPlaceholders>

  <div
    v-else
    :class="[containerClass, computedContainerClass]"
    class="relative rounded-md shadow-sm font-base"
  >
    <div
      v-if="loading && loadingPosition === 'left'"
      class="
        absolute
        inset-y-0
        left-0
        flex
        items-center
        pl-3
        pointer-events-none
      "
    >
      <svg
        class="animate-spin !text-primary-500"
        :class="[iconLeftClass]"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </div>

    <div
      v-else-if="hasLeftIconSlot"
      class="absolute inset-y-0 left-0 flex items-center pl-3"
    >
      <slot name="left" :class="iconLeftClass" />
    </div>

    <span
      v-if="addon"
      class="
        inline-flex
        items-center
        px-3
        text-gray-500
        border border-r-0 border-gray-200
        rounded-l-md
        bg-gray-50
        sm:text-sm
        dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300
      "
    >
      {{ addon }}
    </span>

    <div
      v-if="inlineAddon"
      class="
        absolute
        inset-y-0
        left-0
        flex
        items-center
        pl-3
        pointer-events-none
      "
    >
      <span class="text-gray-500 dark:text-white sm:text-sm ">
        {{ inlineAddon }}
      </span>
    </div>

    <input
      v-bind="$attrs"
      :type="getType"
      :value="modelValue"
      :disabled="disabled"
      :class="[
        defaultInputClass,
        inputPaddingClass,
        inputAddonClass,
        inputInvalidClass,
        inputDisabledClass,
      ]"
      @input="emitValue"
    />

    <div
      v-if="loading && loadingPosition === 'right'"
      class="
        absolute
        inset-y-0
        right-0
        flex
        items-center
        pr-3
        pointer-events-none
      "
    >
      <svg
        class="animate-spin !text-primary-500"
        :class="[iconRightClass]"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </div>

    <div
      v-if="hasRightIconSlot"
      class="absolute inset-y-0 right-0 flex items-center pr-3"
    >
      <slot name="right" :class="iconRightClass" />
    </div>
    <div
      v-if="loading && loadingPosition === 'right'" class="
        absolute
        inset-y-0
        right-0
        flex
        items-center
        pr-3
        pointer-events-none
      "
    >
      <svg
        class="animate-spin !text-primary-500" :class="[iconRightClass]" xmlns="http://www.w3.org/2000/svg"
        fill="none" viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path
          class="opacity-75" fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
      </svg>
    </div>

    <div v-if="hasRightIconSlot" class="absolute inset-y-0 right-0 flex items-center pr-3">
      <slot name="right" :class="iconRightClass" />
    </div>

    <div v-if="type == 'password'" class="absolute inset-y-0 right-0 flex items-center pr-3">
      <a
        v-if="isShowPassword" href="#" class="block" data-cy="eye" tabindex="-1"
        @click.prevent="isShowPassword = !isShowPassword"
      >
        <BaseIcon name="EyeOffIcon" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer" />
      </a>
      <a v-else href="#" class="block" data-cy="eye" tabindex="-1" @click.prevent="isShowPassword = !isShowPassword">
        <BaseIcon name="EyeIcon" class="w-5 h-5 mr-1 text-gray-500 cursor-pointer" />
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, useSlots } from 'vue'

let inheritAttrs = ref(false)

const props = defineProps({
  contentLoading: {
    type: Boolean,
    default: false,
  },
  type: {
    type: [Number, String],
    default: 'text',
  },
  modelValue: {
    type: [String, Number],
    default: '',
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingPosition: {
    type: String,
    default: 'left',
  },
  addon: {
    type: String,
    default: null,
  },
  inlineAddon: {
    type: String,
    default: '',
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
  contentLoadClass: {
    type: String,
    default: '',
  },
  defaultInputClass: {
    type: String,
    default:
      'font-base block w-full sm:text-sm border-gray-200 rounded-md text-black  dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-500',
  },
  iconLeftClass: {
    type: String,
    default: 'h-5 w-5 text-gray-400',
  },
  iconRightClass: {
    type: String,
    default: 'h-5 w-5 text-gray-400',
  },
  modelModifiers: {
    default: () => ({}),
  },
})

const slots = useSlots()

const emit = defineEmits(['update:modelValue'])

const isShowPassword = ref(false)

const getType = computed(() => {
  if (props.type === 'password')
    return isShowPassword.value ? 'text' : 'password'

  return props.type
})

const hasLeftIconSlot = computed(() => {
  return !!slots.left || (props.loading && props.loadingPosition === 'left')
})

const hasRightIconSlot = computed(() => {
  return !!slots.right || (props.loading && props.loadingPosition === 'right')
})

const inputPaddingClass = computed(() => {
  if (hasLeftIconSlot.value && hasRightIconSlot.value) {
    return 'px-10'
  } else if (hasLeftIconSlot.value) {
    return 'pl-10'
  } else if (hasRightIconSlot.value) {
    return 'pr-10'
  }

  return ''
})

const inputAddonClass = computed(() => {
  if (props.addon) {
    return 'flex-1 min-w-0 block w-full px-3 py-2 !rounded-none !rounded-r-md'
  } else if (props.inlineAddon) {
    return 'pl-7'
  }

  return ''
})

const inputInvalidClass = computed(() => {
  if (props.invalid) {
    return 'border-red-500 dark:border-red-500 ring-red-500 dark:ring-red-500 focus:ring-red-500 dark:focus:ring-red-500  focus:border-red-500  dark:focus:border-red-500  '

  }

  return 'focus:ring-primary-400 focus:border-primary-400'
})

const inputDisabledClass = computed(() => {
  if (props.disabled) {
    return `border-gray-100 bg-gray-100 !text-gray-400 dark:!text-gray-200   ring-gray-200 focus:ring-gray-200 focus:border-gray-100 dark:opacity-25 `
  }

  return ''
})

const computedContainerClass = computed(() => {
  let containerClass = `${props.containerClass} `

  if (props.addon) {
    return `${props.containerClass} flex`
  }

  return containerClass
})

function emitValue(e) {
  let val = e.target.value
  if (props.modelModifiers.uppercase) {
    val = val.toUpperCase()
  }

  emit('update:modelValue', val)
}
</script>

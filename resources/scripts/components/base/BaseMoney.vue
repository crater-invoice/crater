<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      class="w-full"
      style="height: 38px"
    />
  </BaseContentPlaceholders>
  <money3
    v-else
    v-model="money"
    v-bind="currencyBindings"
    :class="[inputClass, invalidClass]"
    :disabled="disabled"
  />
</template>

<script setup>
import { computed, ref } from 'vue'
import { Money3Component } from 'v-money3'
import { useCompanyStore } from '@/scripts/admin/stores/company'

let money3 = Money3Component

const props = defineProps({
  contentLoading: {
    type: Boolean,
    default: false,
  },
  modelValue: {
    type: [String, Number],
    required: true,
    default: '',
  },
  invalid: {
    type: Boolean,
    default: false,
  },
  inputClass: {
    type: String,
    default:
      'font-base block w-full sm:text-sm border-gray-200 rounded-md text-black',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  percent: {
    type: Boolean,
    default: false,
  },
  currency: {
    type: Object,
    default: null,
  },
})
const emit = defineEmits(['update:modelValue'])
const companyStore = useCompanyStore()
let hasInitialValueSet = false

const money = computed({
  get: () => props.modelValue,
  set: (value) => {
    if (!hasInitialValueSet) {
      hasInitialValueSet = true
      return
    }

    emit('update:modelValue', value)
  },
})

const currencyBindings = computed(() => {
  const currency = props.currency
    ? props.currency
    : companyStore.selectedCompanyCurrency

  return {
    decimal: currency.decimal_separator,
    thousands: currency.thousand_separator,
    prefix: currency.symbol + ' ',
    precision: currency.precision,
    masked: false,
  }
})

const invalidClass = computed(() => {
  if (props.invalid) {
    return 'border-red-500 ring-red-500 focus:ring-red-500 focus:border-red-500'
  }
  return 'focus:ring-primary-400 focus:border-primary-400'
})
</script>

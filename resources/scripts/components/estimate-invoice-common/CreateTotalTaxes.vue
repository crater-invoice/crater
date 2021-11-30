<template>
  <div class="flex items-center justify-between w-full mt-2 text-sm">
    <label class="font-semibold leading-5 text-gray-500 uppercase">
      {{ tax.name }} ({{ tax.percent }} %)
    </label>
    <label class="flex items-center justify-center text-lg text-black">
      <BaseFormatMoney :amount="tax.amount" :currency="currency" />

      <BaseIcon
        name="TrashIcon"
        class="h-5 ml-2 cursor-pointer"
        @click="$emit('remove', tax.id)"
      />
    </label>
  </div>
</template>

<script setup>
import { computed, watch, inject, watchEffect } from 'vue'

const props = defineProps({
  index: {
    type: Number,
    required: true,
  },
  tax: {
    type: Object,
    required: true,
  },
  taxes: {
    type: Array,
    required: true,
  },
  currency: {
    type: [Object, String],
    required: true,
  },
  store: {
    type: Object,
    default: null,
  },
  data: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update', 'remove'])

const utils = inject('$utils')

const taxAmount = computed(() => {
  if (props.tax.compound_tax && props.store.getSubtotalWithDiscount) {
    return Math.round(
      ((props.store.getSubtotalWithDiscount + props.store.getTotalSimpleTax) *
        props.tax.percent) /
        100
    )
  }
  if (props.store.getSubtotalWithDiscount && props.tax.percent) {
    return Math.round(
      (props.store.getSubtotalWithDiscount * props.tax.percent) / 100
    )
  }
  return 0
})

watchEffect(() => {
  if (props.store.getSubtotalWithDiscount) {
    updateTax()
  }
  if (props.store.getTotalSimpleTax) {
    updateTax()
  }
})

function updateTax() {
  emit('update', {
    ...props.tax,
    amount: taxAmount.value,
  })
}
</script>

<template>
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center text-base" style="flex: 4">
      <label class="pr-2 mb-0" align="right">
        {{ $t('invoices.item.tax') }}
      </label>

      <BaseMultiselect
        v-model="selectedTax"
        value-prop="id"
        :options="filteredTypes"
        :placeholder="$t('general.select_a_tax')"
        open-direction="top"
        track-by="name"
        searchable
        object
        label="name"
        @update:modelValue="(val) => onSelectTax(val)"
      >
        <template #singlelabel="{ value }">
          <div class="absolute left-3.5">
            {{ value.name }} - {{ value.percent }} %
          </div>
        </template>

        <template #option="{ option }">
          {{ option.name }} - {{ option.percent }} %
        </template>

        <template v-if="userStore.hasAbilities(ability)" #action>
          <button
            type="button"
            class="flex items-center justify-center w-full px-2 py-2 bg-gray-200 border-none outline-none cursor-pointer "
            @click="openTaxModal"
          >
            <BaseIcon name="CheckCircleIcon" class="h-5 text-primary-400" />

            <label
              class="ml-2 text-sm leading-none cursor-pointer text-primary-400"
              >{{ $t('invoices.add_new_tax') }}</label
            >
          </button>
        </template>
      </BaseMultiselect>
      <br />
    </div>

    <div class="text-sm text-right" style="flex: 3">
      <BaseFormatMoney :amount="taxAmount" :currency="currency" />
    </div>

    <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
      <BaseIcon
        v-if="taxes.length && index !== taxes.length - 1"
        name="TrashIcon"
        class="h-5 text-gray-700 cursor-pointer"
        @click="removeTax(index)"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref, inject, reactive, watch } from 'vue'
import { useTaxTypeStore } from '@/scripts/admin/stores/tax-type'
import { useModalStore } from '@/scripts/stores/modal'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/scripts/admin/stores/user'

const props = defineProps({
  ability: {
    type: String,
    default: '',
  },
  store: {
    type: Object,
    default: null,
  },
  storeProp: {
    type: String,
    default: '',
  },
  itemIndex: {
    type: Number,
    required: true,
  },
  index: {
    type: Number,
    required: true,
  },
  taxData: {
    type: Object,
    required: true,
  },
  taxes: {
    type: Array,
    default: [],
  },
  total: {
    type: Number,
    default: 0,
  },
  totalTax: {
    type: Number,
    default: 0,
  },
  discountedTotal: {
    type: Number,
    default: 0,
  },
  currency: {
    type: [Object, String],
    required: true,
  },
  updateItems: {
    type: Function,
    default: () => {},
  },
})

const emit = defineEmits(['remove', 'update'])

const taxTypeStore = useTaxTypeStore()
const modalStore = useModalStore()
const userStore = useUserStore()

const selectedTax = ref(null)
const localTax = reactive({ ...props.taxData })
const utils = inject('utils')
const { t } = useI18n()

const filteredTypes = computed(() => {
  const clonedTypes = taxTypeStore.taxTypes.map((a) => ({ ...a }))

  return clonedTypes.map((taxType) => {
    let found = props.taxes.find((tax) => tax.tax_type_id === taxType.id)

    if (found) {
      taxType.disabled = true
    } else {
      taxType.disabled = false
    }

    return taxType
  })
})

const taxAmount = computed(() => {
  if (props.discountedTotal && localTax.percent) {
    const taxPerItemEnabled = props.store[props.storeProp].tax_per_item === 'YES'
    const discountPerItemEnabled = props.store[props.storeProp].discount_per_item === 'YES'
    if (taxPerItemEnabled && !discountPerItemEnabled){
      return getTaxAmount()
    }
    return (props.discountedTotal * localTax.percent) / 100
  }
  return 0
})

watch(
  () => props.discountedTotal,
  () => {
    updateRowTax()
  }
)

watch(
  () => props.totalTax,
  () => {
    updateRowTax()
  }
)

watch(
  () => taxAmount.value,
  () => {
    updateRowTax()
  },
)

// Set SelectedTax
if (props.taxData.tax_type_id > 0) {
  selectedTax.value = taxTypeStore.taxTypes.find(
    (_type) => _type.id === props.taxData.tax_type_id
  )
}

updateRowTax()

function onSelectTax(val) {
  localTax.percent = val.percent
  localTax.tax_type_id = val.id
  localTax.name = val.name

  updateRowTax()
}

function updateRowTax() {
  if (localTax.tax_type_id === 0) {
    return
  }

  emit('update', {
    index: props.index,
    item: {
      ...localTax,
      amount: taxAmount.value,
    },
  })
}

function openTaxModal() {
  let data = {
    itemIndex: props.itemIndex,
    taxIndex: props.index,
  }

  modalStore.openModal({
    title: t('settings.tax_types.add_tax'),
    componentName: 'TaxTypeModal',
    data: data,
    size: 'sm',
  })
}

function removeTax(index) {
  props.store.$patch((state) => {
    state[props.storeProp].items[props.itemIndex].taxes.splice(index, 1)
    state[props.storeProp].items[props.itemIndex].tax = 0
    state[props.storeProp].items[props.itemIndex].totalTax = 0
  })
}

function getTaxAmount() {
  let total = 0
  let discount = 0
  const itemTotal = props.discountedTotal
  const modelDiscount = props.store[props.storeProp].discount ? props.store[props.storeProp].discount : 0
  const type = props.store[props.storeProp].discount_type
  if (modelDiscount > 0) {
    props.store[props.storeProp].items.forEach((_i) => {
      total += _i.total
    })
    const proportion = (itemTotal / total).toFixed(2)
    discount = type === 'fixed' ? modelDiscount * 100 : (total * modelDiscount) / 100
    const itemDiscount = Math.round(discount * proportion)
    const discounted = itemTotal - itemDiscount
    return Math.round((discounted * localTax.percent) / 100)
  }
  return Math.round((props.discountedTotal * localTax.percent) / 100)
}
</script>

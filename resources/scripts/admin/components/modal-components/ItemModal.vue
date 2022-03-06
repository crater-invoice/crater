<template>
  <BaseModal :show="modalActive" @close="closeItemModal">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="h-6 w-6 text-gray-500 cursor-pointer"
          @click="closeItemModal"
        />
      </div>
    </template>
    <div class="item-modal">
      <form action="" @submit.prevent="submitItemData">
        <div class="px-8 py-8 sm:p-6">
          <BaseInputGrid layout="one-column">
            <BaseInputGroup
              :label="$t('items.name')"
              required
              :error="v$.name.$error && v$.name.$errors[0].$message"
            >
              <BaseInput
                v-model="itemStore.currentItem.name"
                type="text"
                :invalid="v$.name.$error"
                @input="v$.name.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup :label="$t('items.price')">
              <BaseMoney
                :key="companyStore.selectedCompanyCurrency"
                v-model="price"
                :currency="companyStore.selectedCompanyCurrency"
                class="
                  relative
                  w-full
                  focus:border focus:border-solid focus:border-primary
                "
              />
            </BaseInputGroup>

            <BaseInputGroup :label="$t('items.unit')">
              <BaseMultiselect
                v-model="itemStore.currentItem.unit_id"
                label="name"
                :options="itemStore.itemUnits"
                value-prop="id"
                :can-deselect="false"
                :can-clear="false"
                :placeholder="$t('items.select_a_unit')"
                searchable
                track-by="name"
              />
            </BaseInputGroup>

            <BaseInputGroup
              v-if="isTaxPerItemEnabled"
              :label="$t('items.taxes')"
            >
              <BaseMultiselect
                v-model="taxes"
                :options="getTaxTypes"
                mode="tags"
                label="tax_name"
                value-prop="id"
                class="w-full"
                :can-deselect="false"
                :can-clear="false"
                searchable
                track-by="tax_name"
                object
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('items.description')"
              :error="
                v$.description.$error && v$.description.$errors[0].$message
              "
            >
              <BaseTextarea
                v-model="itemStore.currentItem.description"
                rows="4"
                cols="50"
                :invalid="v$.description.$error"
                @input="v$.description.$touch()"
              />
            </BaseInputGroup>
          </BaseInputGrid>
        </div>
        <div
          class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
        >
          <BaseButton
            class="mr-3"
            variant="primary-outline"
            type="button"
            @click="closeItemModal"
          >
            {{ $t('general.cancel') }}
          </BaseButton>
          <BaseButton
            :loading="isLoading"
            :disabled="isLoading"
            variant="primary"
            type="submit"
          >
            <template #left="slotProps">
              <BaseIcon name="SaveIcon" :class="slotProps.class" />
            </template>
            {{ itemStore.isEdit ? $t('general.update') : $t('general.save') }}
          </BaseButton>
        </div>
      </form>
    </div>
  </BaseModal>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import {
  required,
  minLength,
  maxLength,
  minValue,
  helpers,
  alpha,
} from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useModalStore } from '@/scripts/stores/modal'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useItemStore } from '@/scripts/admin/stores/item'
import { useTaxTypeStore } from '@/scripts/admin/stores/tax-type'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useEstimateStore } from '@/scripts/admin/stores/estimate'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'

const emit = defineEmits(['newItem'])

const modalStore = useModalStore()
const itemStore = useItemStore()
const companyStore = useCompanyStore()
const taxTypeStore = useTaxTypeStore()
const estimateStore = useEstimateStore()
const notificationStore = useNotificationStore()

const { t } = useI18n()
const isLoading = ref(false)
const taxPerItemSetting = ref(companyStore.selectedCompanySettings.tax_per_item)

const modalActive = computed(
  () => modalStore.active && modalStore.componentName === 'ItemModal'
)

const price = computed({
  get: () => itemStore.currentItem.price / 100,
  set: (value) => {
    itemStore.currentItem.price = Math.round(value * 100)
  },
})

const taxes = computed({
  get: () =>
    itemStore.currentItem.taxes.map((tax) => {
      if (tax) {
        return {
          ...tax,
          tax_type_id: tax.id,
          tax_name: tax.name + ' (' + tax.percent + '%)',
        }
      }
    }),
  set: (value) => {
    itemStore.$patch((state) => {
      state.currentItem.taxes = value
    })
  },
})

const isTaxPerItemEnabled = computed(() => {
  return taxPerItemSetting.value === 'YES'
})

const rules = {
  name: {
    required: helpers.withMessage(t('validation.required'), required),
    minLength: helpers.withMessage(
      t('validation.name_min_length', { count: 3 }),
      minLength(3)
    ),
  },

  description: {
    maxLength: helpers.withMessage(
      t('validation.description_maxlength', { count: 255 }),
      maxLength(255)
    ),
  },
}

const v$ = useVuelidate(
  rules,
  computed(() => itemStore.currentItem)
)

const getTaxTypes = computed(() => {
  return taxTypeStore.taxTypes.map((tax) => {
    return { ...tax, tax_name: tax.name + ' (' + tax.percent + '%)' }
  })
})

onMounted(() => {
  v$.value.$reset()
  itemStore.fetchItemUnits({ limit: 'all' })
})

async function submitItemData() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  let data = {
    ...itemStore.currentItem,
    taxes: itemStore.currentItem.taxes.map((tax) => {
      return {
        tax_type_id: tax.id,
        amount: (price.value * tax.percent) / 100,
        percent: tax.percent,
        name: tax.name,
        collective_tax: 0,
      }
    }),
  }

  isLoading.value = true

  const action = itemStore.isEdit ? itemStore.updateItem : itemStore.addItem

  await action(data).then((res) => {
    isLoading.value = false
    if (res.data.data) {
      if (modalStore.data) {
        modalStore.refreshData(res.data.data)
      }
    }
    closeItemModal()
  })
}

function closeItemModal() {
  modalStore.closeModal()
  setTimeout(() => {
    itemStore.resetCurrentItem()
    modalStore.$reset()
    v$.value.$reset()
  }, 300)
}
</script>

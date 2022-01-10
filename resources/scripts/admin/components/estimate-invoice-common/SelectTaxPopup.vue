<template>
  <div class="w-full mt-4 tax-select">
    <Popover v-slot="{ isOpen }" class="relative">
      <PopoverButton
        :class="isOpen ? '' : 'text-opacity-90'"
        class="
          flex
          items-center
          text-sm
          font-medium
          text-primary-400
          focus:outline-none focus:border-none
        "
      >
        <BaseIcon
          name="PlusIcon"
          class="w-4 h-4 font-medium text-primary-400"
        />
        {{ $t('settings.tax_types.add_tax') }}
      </PopoverButton>

      <!-- Tax Select Popup -->
      <div class="relative w-full max-w-md px-4">
        <transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="translate-y-1 opacity-0"
          enter-to-class="translate-y-0 opacity-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="translate-y-0 opacity-100"
          leave-to-class="translate-y-1 opacity-0"
        >
          <PopoverPanel
            v-slot="{ close }"
            style="min-width: 350px; margin-left: 62px; top: -28px"
            class="absolute z-10 px-4 py-2 -translate-x-full sm:px-0"
          >
            <div
              class="
                overflow-hidden
                rounded-md
                shadow-lg
                ring-1 ring-black ring-opacity-5
              "
            >
              <!-- Tax Search Input  -->

              <div class="relative bg-white">
                <div class="relative p-4">
                  <BaseInput
                    v-model="textSearch"
                    :placeholder="$t('general.search')"
                    type="text"
                    class="text-black"
                  >
                  </BaseInput>
                </div>

                <!-- List of Taxes  -->
                <div
                  v-if="filteredTaxType.length > 0"
                  class="
                    relative
                    flex flex-col
                    overflow-auto
                    list
                    max-h-36
                    border-t border-gray-200
                  "
                >
                  <div
                    v-for="(taxType, index) in filteredTaxType"
                    :key="index"
                    :class="{
                      'bg-gray-100 cursor-not-allowed opacity-50 pointer-events-none':
                        taxes.find((val) => {
                          return val.tax_type_id === taxType.id
                        }),
                    }"
                    tabindex="2"
                    class="
                      px-6
                      py-4
                      border-b border-gray-200 border-solid
                      cursor-pointer
                      hover:bg-gray-100 hover:cursor-pointer
                      last:border-b-0
                    "
                    @click="selectTaxType(taxType, close)"
                  >
                    <div class="flex justify-between px-2">
                      <label
                        class="
                          m-0
                          text-base
                          font-semibold
                          leading-tight
                          text-gray-700
                          cursor-pointer
                        "
                      >
                        {{ taxType.name }}
                      </label>

                      <label
                        class="
                          m-0
                          text-base
                          font-semibold
                          text-gray-700
                          cursor-pointer
                        "
                      >
                        {{ taxType.percent }} %
                      </label>
                    </div>
                  </div>
                </div>

                <div v-else class="flex justify-center p-5 text-gray-400">
                  <label class="text-base text-gray-500 cursor-pointer">
                    {{ $t('general.no_tax_found') }}
                  </label>
                </div>
              </div>

              <!-- Add new Tax action -->
              <button
                v-if="userStore.hasAbilities(abilities.CREATE_TAX_TYPE)"
                type="button"
                class="
                  flex
                  items-center
                  justify-center
                  w-full
                  h-10
                  px-2
                  py-3
                  bg-gray-200
                  border-none
                  outline-none
                "
                @click="openTaxTypeModal"
              >
                <BaseIcon name="CheckCircleIcon" class="text-primary-400" />
                <label
                  class="
                    m-0
                    ml-3
                    text-sm
                    leading-none
                    cursor-pointer
                    font-base
                    text-primary-400
                  "
                >
                  {{ $t('estimates.add_new_tax') }}
                </label>
              </button>
            </div>
          </PopoverPanel>
        </transition>
      </div>
    </Popover>
  </div>
</template>

<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { computed, ref, inject, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useEstimateStore } from '@/scripts/admin/stores/estimate'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import { useModalStore } from '@/scripts/stores/modal'
import { useTaxTypeStore } from '@/scripts/admin/stores/tax-type'
import { useUserStore } from '@/scripts/admin/stores/user'
import abilities from '@/scripts/admin/stub/abilities'

const props = defineProps({
  type: {
    type: String,
    default: null,
  },
  store: {
    type: Object,
    default: null,
  },
  storeProp: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['select:taxType'])

const modalStore = useModalStore()
const taxTypeStore = useTaxTypeStore()
const userStore = useUserStore()

const { t } = useI18n()
const textSearch = ref(null)

const filteredTaxType = computed(() => {
  if (textSearch.value) {
    return taxTypeStore.taxTypes.filter(function (el) {
      return (
        el.name.toLowerCase().indexOf(textSearch.value.toLowerCase()) !== -1
      )
    })
  } else {
    return taxTypeStore.taxTypes
  }
})

const taxes = computed(() => {
  return props.store[props.storeProp].taxes
})

function selectTaxType(data, close) {
  emit('select:taxType', { ...data })
  close()
}

function openTaxTypeModal() {
  modalStore.openModal({
    title: t('settings.tax_types.add_tax'),
    componentName: 'TaxTypeModal',
    size: 'sm',
    refreshData: (data) => emit('select:taxType', data),
  })
}
</script>

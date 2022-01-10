<template>
  <BaseModal :show="modalActive" @close="closeModal" @open="setAddress">
    <template #header>
      <div class="flex justify-between w-full">
        <div class="flex flex-col">
          {{ modalStore.title }}

          <p class="text-sm text-gray-500 mt-1">
            {{ modalStore.content }}
          </p>
        </div>
        <BaseIcon
          name="XIcon"
          class="h-6 w-6 text-gray-500 cursor-pointer"
          @click="closeModal"
        />
      </div>
    </template>
    <form @submit.prevent="saveCustomerAddress">
      <div class="p-4 sm:p-6">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            required
            :error="v$.state.$error && v$.state.$errors[0].$message"
            :label="$t('customers.state')"
          >
            <BaseInput
              v-model="address.state"
              type="text"
              name="shippingState"
              class="mt-1 md:mt-0"
              :invalid="v$.state.$error"
              @input="v$.state.$touch()"
              :placeholder="$t('settings.taxations.state_placeholder')"
            />
          </BaseInputGroup>

          <BaseInputGroup
            required
            :error="v$.city.$error && v$.city.$errors[0].$message"
            :label="$t('customers.city')"
          >
            <BaseInput
              v-model="address.city"
              type="text"
              name="shippingCity"
              class="mt-1 md:mt-0"
              :invalid="v$.city.$error"
              @input="v$.city.$touch()"
              :placeholder="$t('settings.taxations.city_placeholder')"
            />
          </BaseInputGroup>

          <BaseInputGroup
            required
            :error="
              v$.address_street_1.$error &&
              v$.address_street_1.$errors[0].$message
            "
            :label="$t('customers.address')"
          >
            <BaseTextarea
              v-model="address.address_street_1"
              rows="2"
              cols="50"
              class="mt-1 md:mt-0"
              :invalid="v$.address_street_1.$error"
              @input="v$.address_street_1.$touch()"
              :placeholder="$t('settings.taxations.address_placeholder')"
            />
          </BaseInputGroup>

          <BaseInputGroup
            required
            :error="v$.zip.$error && v$.zip.$errors[0].$message"
            :label="$t('customers.zip_code')"
          >
            <BaseInput
              v-model="address.zip"
              :invalid="v$.zip.$error"
              @input="v$.zip.$touch()"
              type="text"
              class="mt-1 md:mt-0"
              :placeholder="$t('settings.taxations.zip_placeholder')"
            />
          </BaseInputGroup>
        </BaseInputGrid>
      </div>

      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          class="mr-3 text-sm"
          type="button"
          variant="primary-outline"
          @click="closeModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton :loading="isLoading" variant="primary" type="submit">
          <template #left="slotProps">
            <BaseIcon
              v-if="!isLoading"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.save') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { useModalStore } from '@/scripts/stores/modal'
import { useTaxTypeStore } from '@/scripts/admin/stores/tax-type'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useI18n } from 'vue-i18n'
import { helpers, required } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'

const modalStore = useModalStore()
const globalStore = useGlobalStore()

const address = reactive({
  state: '',
  city: '',
  address_street_1: '',
  zip: '',
})

const isLoading = ref(false)
const taxTypeStore = useTaxTypeStore()
const { t } = useI18n()

const modalActive = computed(
  () => modalStore.active && modalStore.componentName === 'TaxationAddressModal'
)

const rules = computed(() => {
  return {
    state: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    city: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    address_street_1: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    zip: {
      required: helpers.withMessage(t('validation.required'), required),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => address)
)

async function saveCustomerAddress() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  let data = {
    address,
  }
  if (modalStore.id) {
    data.customer_id = modalStore.id
  }
  // replace '/n' with empty string
  address.address_street_1 = address.address_street_1.replace(
    /(\r\n|\n|\r)/gm,
    ''
  )

  isLoading.value = true
  await taxTypeStore
    .fetchSalesTax(data)
    .then((res) => {
      isLoading.value = false
      emit('addTax', res.data.data)
      closeModal()
    })
    .catch((e) => {
      isLoading.value = false
    })
}
const emit = defineEmits(['addTax'])

function setAddress() {
  address.state = modalStore?.data?.state
  address.city = modalStore?.data?.city
  address.address_street_1 = modalStore?.data?.address_street_1
  address.zip = modalStore?.data?.zip
}

function closeModal() {
  modalStore.closeModal()
}
</script>

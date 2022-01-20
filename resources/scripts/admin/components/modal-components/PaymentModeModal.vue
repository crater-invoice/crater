<template>
  <BaseModal :show="modalActive" @close="closePaymentModeModal">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closePaymentModeModal"
        />
      </div>
    </template>

    <form action="" @submit.prevent="submitPaymentMode">
      <div class="p-4 sm:p-6">
        <BaseInputGroup
          :label="$t('settings.payment_modes.mode_name')"
          :error="
            v$.currentPaymentMode.name.$error &&
            v$.currentPaymentMode.name.$errors[0].$message
          "
          required
        >
          <BaseInput
            v-model="paymentStore.currentPaymentMode.name"
            :invalid="v$.currentPaymentMode.name.$error"
            @input="v$.currentPaymentMode.name.$touch()"
          />
        </BaseInputGroup>
      </div>

      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          variant="primary-outline"
          class="mr-3"
          type="button"
          @click="closePaymentModeModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton
          :loading="isSaving"
          :disabled="isSaving"
          variant="primary"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon name="SaveIcon" :class="slotProps.class" />
          </template>
          {{
            paymentStore.currentPaymentMode.id
              ? $t('general.update')
              : $t('general.save')
          }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { usePaymentStore } from '@/scripts/admin/stores/payment'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, minLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useModalStore } from '@/scripts/stores/modal'

const modalStore = useModalStore()
const paymentStore = usePaymentStore()

const { t } = useI18n()
const isSaving = ref(false)

const rules = computed(() => {
  return {
    currentPaymentMode: {
      name: {
        required: helpers.withMessage(t('validation.required'), required),
        minLength: helpers.withMessage(
          t('validation.name_min_length', { count: 3 }),
          minLength(3)
        ),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => paymentStore)
)

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'PaymentModeModal'
})

async function submitPaymentMode() {
  v$.value.currentPaymentMode.$touch()

  if (v$.value.currentPaymentMode.$invalid) {
    return true
  }
  try {
    const action = paymentStore.currentPaymentMode.id
      ? paymentStore.updatePaymentMode
      : paymentStore.addPaymentMode
    isSaving.value = true
    await action(paymentStore.currentPaymentMode)
    isSaving.value = false
    modalStore.refreshData ? modalStore.refreshData() : ''
    closePaymentModeModal()
  } catch (err) {
    isSaving.value = false
    return true
  }
}

function closePaymentModeModal() {
  modalStore.closeModal()

  setTimeout(() => {
    v$.value.$reset()
    paymentStore.currentPaymentMode = {
      id: '',
      name: null,
    }
  })
}
</script>

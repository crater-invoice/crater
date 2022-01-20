<template>
  <BaseModal
    :show="modalStore.active && modalStore.componentName === 'ItemUnitModal'"
    @close="closeItemUnitModal"
  >
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeItemUnitModal"
        />
      </div>
    </template>

    <form action="" @submit.prevent="submitItemUnit">
      <div class="p-8 sm:p-6">
        <BaseInputGroup
          :label="$t('settings.customization.items.unit_name')"
          :error="v$.name.$error && v$.name.$errors[0].$message"
          variant="horizontal"
          required
        >
          <BaseInput
            v-model="itemStore.currentItemUnit.name"
            :invalid="v$.name.$error"
            type="text"
            @input="v$.name.$touch()"
          />
        </BaseInputGroup>
      </div>

      <div
        class="
          z-0
          flex
          justify-end
          p-4
          border-t border-gray-200 border-solid border-modal-bg
        "
      >
        <BaseButton
          type="button"
          variant="primary-outline"
          class="mr-3 text-sm"
          @click="closeItemUnitModal"
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
            <BaseIcon
              v-if="!isSaving"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{
            itemStore.isItemUnitEdit ? $t('general.update') : $t('general.save')
          }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { useItemStore } from '@/scripts/admin/stores/item'
import { useModalStore } from '@/scripts/stores/modal'
import { computed, ref, watch } from 'vue'
import { required, minLength, maxLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useI18n } from 'vue-i18n'

const itemStore = useItemStore()
const modalStore = useModalStore()

const { t } = useI18n()
let isSaving = ref(false)

const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => itemStore.currentItemUnit)
)

async function submitItemUnit() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }
  try {
    const action = itemStore.isItemUnitEdit
      ? itemStore.updateItemUnit
      : itemStore.addItemUnit

    isSaving.value = true

    await action(itemStore.currentItemUnit)

    modalStore.refreshData ? modalStore.refreshData() : ''

    closeItemUnitModal()
    isSaving.value = false
  } catch (err) {
    isSaving.value = false
    return true
  }
}

function closeItemUnitModal() {
  modalStore.closeModal()

  setTimeout(() => {
    itemStore.currentItemUnit = {
      id: null,
      name: '',
    }

    modalStore.$reset()
    v$.value.$reset()
  }, 300)
}
</script>

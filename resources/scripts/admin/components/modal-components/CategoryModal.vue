<template>
  <BaseModal :show="modalActive" @close="closeCategoryModal">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}
        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeCategoryModal"
        />
      </div>
    </template>

    <form action="" @submit.prevent="submitCategoryData">
      <div class="p-8 sm:p-6">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$t('expenses.category')"
            :error="
              v$.currentCategory.name.$error &&
              v$.currentCategory.name.$errors[0].$message
            "
            required
          >
            <BaseInput
              v-model="categoryStore.currentCategory.name"
              :invalid="v$.currentCategory.name.$error"
              type="text"
              @input="v$.currentCategory.name.$touch()"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('expenses.description')"
            :error="
              v$.currentCategory.description.$error &&
              v$.currentCategory.description.$errors[0].$message
            "
          >
            <BaseTextarea
              v-model="categoryStore.currentCategory.description"
              rows="4"
              cols="50"
              @input="v$.currentCategory.description.$touch()"
            />
          </BaseInputGroup>
        </BaseInputGrid>
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
          @click="closeCategoryModal"
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
          {{ categoryStore.isEdit ? $t('general.update') : $t('general.save') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { useCategoryStore } from '@/scripts/admin/stores/category'
import { useModalStore } from '@/scripts/stores/modal'
import { computed, ref } from 'vue'
import { required, minLength, maxLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useI18n } from 'vue-i18n'

const categoryStore = useCategoryStore()
const modalStore = useModalStore()
const { t } = useI18n()

let isSaving = ref(false)

const rules = computed(() => {
  return {
    currentCategory: {
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
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => categoryStore)
)

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'CategoryModal'
})

async function submitCategoryData() {
  v$.value.currentCategory.$touch()

  if (v$.value.currentCategory.$invalid) {
    return true
  }

  const action = categoryStore.isEdit
    ? categoryStore.updateCategory
    : categoryStore.addCategory

  isSaving.value = true

  await action(categoryStore.currentCategory)

  isSaving.value = false

  modalStore.refreshData ? modalStore.refreshData() : ''

  closeCategoryModal()
}

function closeCategoryModal() {
  modalStore.closeModal()

  setTimeout(() => {
    categoryStore.$reset()
    v$.value.$reset()
  }, 300)
}
</script>

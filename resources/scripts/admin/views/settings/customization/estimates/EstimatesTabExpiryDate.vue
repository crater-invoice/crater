<template>
  <form @submit.prevent="submitForm">
    <h6 class="text-gray-900 text-lg font-medium">
      {{ $t('settings.customization.estimates.expiry_date') }}
    </h6>
    <p class="mt-1 text-sm text-gray-500 mb-2">
      {{ $t('settings.customization.estimates.expiry_date_description') }}
    </p>

    <BaseSwitchSection
      v-model="expiryDateAutoField"
      :title="
        $t('settings.customization.estimates.set_expiry_date_automatically')
      "
      :description="
        $t(
          'settings.customization.estimates.set_expiry_date_automatically_description'
        )
      "
    />

    <BaseInputGroup
      v-if="expiryDateAutoField"
      :label="$t('settings.customization.estimates.expiry_date_days')"
      :error="
        v$.expiryDateSettings.estimate_expiry_date_days.$error &&
        v$.expiryDateSettings.estimate_expiry_date_days.$errors[0].$message
      "
      class="mt-2 mb-4"
    >
      <div class="w-full sm:w-1/2 md:w-1/4 lg:w-1/5">
        <BaseInput
          v-model="expiryDateSettings.estimate_expiry_date_days"
          :invalid="v$.expiryDateSettings.estimate_expiry_date_days.$error"
          type="number"
          @input="v$.expiryDateSettings.estimate_expiry_date_days.$touch()"
        />
      </div>
    </BaseInputGroup>

    <BaseButton
      :loading="isSaving"
      :disabled="isSaving"
      variant="primary"
      type="submit"
      class="mt-4"
    >
      <template #left="slotProps">
        <BaseIcon v-if="!isSaving" :class="slotProps.class" name="SaveIcon" />
      </template>
      {{ $t('settings.customization.save') }}
    </BaseButton>
  </form>
</template>

<script setup>
import { ref, computed, onMounted, reactive, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { numeric, helpers, requiredIf } from '@vuelidate/validators'

import useVuelidate from '@vuelidate/core'

const { t } = useI18n()
const companyStore = useCompanyStore()

const utils = inject('utils')

let isSaving = ref(false)

const expiryDateSettings = reactive({
  estimate_set_expiry_date_automatically: null,
  estimate_expiry_date_days: null,
})

utils.mergeSettings(expiryDateSettings, {
  ...companyStore.selectedCompanySettings,
})

const expiryDateAutoField = computed({
  get: () => {
    return expiryDateSettings.estimate_set_expiry_date_automatically === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    expiryDateSettings.estimate_set_expiry_date_automatically = value
  },
})

const rules = computed(() => {
  return {
    expiryDateSettings: {
      estimate_expiry_date_days: {
        required: helpers.withMessage(
          t('validation.required'),
          requiredIf(expiryDateAutoField.value)
        ),
        numeric: helpers.withMessage(t('validation.numbers_only'), numeric),
      },
    },
  }
})

const v$ = useVuelidate(rules, { expiryDateSettings })

async function submitForm() {
  v$.value.expiryDateSettings.$touch()

  if (v$.value.expiryDateSettings.$invalid) {
    return false
  }

  isSaving.value = true

  let data = {
    settings: {
      ...expiryDateSettings,
    },
  }
  // Don't pass expiry_date_days if setting is not enabled

  if (!expiryDateAutoField.value) {
    delete data.settings.estimate_expiry_date_days
  }

  await companyStore.updateCompanySettings({
    data,
    message: 'settings.customization.estimates.estimate_settings_updated',
  })

  isSaving.value = false

  return true
}
</script>

<template>
  <h6 class="text-gray-900 text-lg font-medium">
    {{ $t('settings.customization.estimates.convert_estimate_options') }}
  </h6>
  <p class="mt-1 text-sm text-gray-500">
    {{ $t('settings.customization.estimates.convert_estimate_description') }}
  </p>

  <BaseInputGroup required>
    <BaseRadio
      id="no_action"
      v-model="settingsForm.estimate_convert_action"
      :label="$t('settings.customization.estimates.no_action')"
      size="sm"
      name="filter"
      value="no_action"
      class="mt-2"
      @update:modelValue="submitForm"
    />
    <BaseRadio
      id="delete_estimate"
      v-model="settingsForm.estimate_convert_action"
      :label="$t('settings.customization.estimates.delete_estimate')"
      size="sm"
      name="filter"
      value="delete_estimate"
      class="my-2"
      @update:modelValue="submitForm"
    />
    <BaseRadio
      id="mark_estimate_as_accepted"
      v-model="settingsForm.estimate_convert_action"
      :label="$t('settings.customization.estimates.mark_estimate_as_accepted')"
      size="sm"
      name="filter"
      value="mark_estimate_as_accepted"
      @update:modelValue="submitForm"
    />
  </BaseInputGroup>
</template>

<script setup>
import { reactive, computed, ref, inject } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { required, helpers } from '@vuelidate/validators'
import { useI18n } from 'vue-i18n'
import { useGlobalStore } from '@/scripts/admin/stores/global'

const { t, tm } = useI18n()
const companyStore = useCompanyStore()
const globalStore = useGlobalStore()

const utils = inject('utils')

const settingsForm = reactive({ estimate_convert_action: null })

utils.mergeSettings(settingsForm, {
  ...companyStore.selectedCompanySettings,
})

const retrospectiveEditOptions = computed(() => {
  return globalStore.config.estimate_convert_action.map((option) => {
    option.title = t(option.key)
    return option
  })
})

async function submitForm() {
  let data = {
    settings: {
      ...settingsForm,
    },
  }

  await companyStore.updateCompanySettings({
    data,
    message: 'settings.customization.estimates.estimate_settings_updated',
  })

  return true
}
</script>

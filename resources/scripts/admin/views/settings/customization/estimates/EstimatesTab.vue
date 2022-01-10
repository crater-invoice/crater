<template>
  <EstimatesTabEstimateNumber />

  <BaseDivider class="my-8" />

  <EstimatesTabExpiryDate />

  <BaseDivider class="my-8" />

  <EstimatesTabConvertEstimate />

  <BaseDivider class="my-8" />

  <EstimatesTabDefaultFormats />

  <BaseDivider class="mt-6 mb-2" />

  <ul class="divide-y divide-gray-200">
    <BaseSwitchSection
      v-model="sendAsAttachmentField"
      :title="$t('settings.customization.estimates.estimate_email_attachment')"
      :description="
        $t(
          'settings.customization.estimates.estimate_email_attachment_setting_description'
        )
      "
    />
  </ul>
</template>

<script setup>
import { computed, reactive, inject } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'

import EstimatesTabEstimateNumber from './EstimatesTabEstimateNumber.vue'
import EstimatesTabExpiryDate from './EstimatesTabExpiryDate.vue'
import EstimatesTabDefaultFormats from './EstimatesTabDefaultFormats.vue'
import EstimatesTabConvertEstimate from './EstimatesTabConvertEstimate.vue'

const utils = inject('utils')

const companyStore = useCompanyStore()

const estimateSettings = reactive({
  estimate_email_attachment: null,
})

utils.mergeSettings(estimateSettings, {
  ...companyStore.selectedCompanySettings,
})

const sendAsAttachmentField = computed({
  get: () => {
    return estimateSettings.estimate_email_attachment === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        estimate_email_attachment: value,
      },
    }

    estimateSettings.estimate_email_attachment = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})
</script>

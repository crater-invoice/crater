<template>
  <form @submit.prevent="submitForm">
    <BaseHeading
      type="heading-title"
      :subtitle="
        $t('settings.customization.estimates.default_formats_description')
      "
    >
      {{ $t('settings.customization.estimates.default_formats') }}
    </BaseHeading>

    <BaseInputGroup
      :label="
        $t('settings.customization.estimates.default_estimate_email_body')
      "
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_mail_body"
        :fields="estimateMailFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.estimates.company_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_company_address_format"
        :fields="companyFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.estimates.shipping_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_shipping_address_format"
        :fields="shippingFields"
      />
    </BaseInputGroup>

    <BaseInputGroup
      :label="$t('settings.customization.estimates.billing_address_format')"
      class="mt-6 mb-4"
    >
      <BaseCustomInput
        v-model="formatSettings.estimate_billing_address_format"
        :fields="billingFields"
      />
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
import { ref, reactive, inject } from 'vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const companyStore = useCompanyStore()
const utils = inject('utils')

const estimateMailFields = ref([
  'customer',
  'customerCustom',
  'estimate',
  'estimateCustom',
  'company',
])

const billingFields = ref([
  'billing',
  'customer',
  'customerCustom',
  'estimateCustom',
])

const shippingFields = ref([
  'shipping',
  'customer',
  'customerCustom',
  'estimateCustom',
])

const companyFields = ref(['company', 'estimateCustom'])

let isSaving = ref(false)

const formatSettings = reactive({
  estimate_mail_body: null,
  estimate_company_address_format: null,
  estimate_shipping_address_format: null,
  estimate_billing_address_format: null,
})

utils.mergeSettings(formatSettings, {
  ...companyStore.selectedCompanySettings,
})

async function submitForm() {
  isSaving.value = true

  let data = {
    settings: {
      ...formatSettings,
    },
  }

  await companyStore.updateCompanySettings({
    data,
    message: 'settings.customization.estimates.estimate_settings_updated',
  })

  isSaving.value = false

  return true
}
</script>

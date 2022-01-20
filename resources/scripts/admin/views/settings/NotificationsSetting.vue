<template>
  <BaseSettingCard
    :title="$t('settings.notification.title')"
    :description="$t('settings.notification.description')"
  >
    <form action="" @submit.prevent="submitForm">
      <div class="grid-cols-2 col-span-1 mt-14">
        <BaseInputGroup
          :error="
            v$.notification_email.$error &&
            v$.notification_email.$errors[0].$message
          "
          :label="$t('settings.notification.email')"
          class="my-2"
          required
        >
          <BaseInput
            v-model.trim="settingsForm.notification_email"
            :invalid="v$.notification_email.$error"
            type="email"
            @input="v$.notification_email.$touch()"
          />
        </BaseInputGroup>

        <BaseButton
          :disabled="isSaving"
          :loading="isSaving"
          variant="primary"
          type="submit"
          class="mt-6"
        >
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              :class="slotProps.class"
              name="SaveIcon"
            />
          </template>

          {{ $tc('settings.notification.save') }}
        </BaseButton>
      </div>
    </form>

    <BaseDivider class="mt-6 mb-2" />

    <ul class="divide-y divide-gray-200">
      <BaseSwitchSection
        v-model="invoiceViewedField"
        :title="$t('settings.notification.invoice_viewed')"
        :description="$t('settings.notification.invoice_viewed_desc')"
      />

      <BaseSwitchSection
        v-model="estimateViewedField"
        :title="$t('settings.notification.estimate_viewed')"
        :description="$t('settings.notification.estimate_viewed_desc')"
      />
    </ul>
  </BaseSettingCard>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, email, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const companyStore = useCompanyStore()

let isSaving = ref(false)
const { t } = useI18n()

const settingsForm = reactive({
  notify_invoice_viewed:
    companyStore.selectedCompanySettings.notify_invoice_viewed,
  notify_estimate_viewed:
    companyStore.selectedCompanySettings.notify_estimate_viewed,
  notification_email: companyStore.selectedCompanySettings.notification_email,
})

const rules = computed(() => {
  return {
    notification_email: {
      required: helpers.withMessage(t('validation.required'), required),
      email: helpers.withMessage(t('validation.email_incorrect'), email),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => settingsForm)
)

const invoiceViewedField = computed({
  get: () => {
    return settingsForm.notify_invoice_viewed === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        notify_invoice_viewed: value,
      },
    }

    settingsForm.notify_invoice_viewed = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

const estimateViewedField = computed({
  get: () => {
    return settingsForm.notify_estimate_viewed === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        notify_estimate_viewed: value,
      },
    }

    settingsForm.notify_estimate_viewed = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

async function submitForm() {
  v$.value.$touch()
  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true

  const data = {
    settings: {
      notification_email: settingsForm.notification_email,
    },
  }

  await companyStore.updateCompanySettings({
    data,
    message: 'settings.notification.email_save_message',
  })

  isSaving.value = false
}
</script>

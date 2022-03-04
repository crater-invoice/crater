<template>
  <BaseWizardStep
    :title="$t('wizard.preferences')"
    :description="$t('wizard.preferences_desc')"
    step-container="bg-white border border-gray-200 border-solid mb-8 md:w-full p-8 rounded w-full"
  >
    <form action="" @submit.prevent="next">
      <div>
        <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
          <BaseInputGroup
            :label="$t('wizard.currency')"
            :error="
              v$.currentPreferences.currency.$error &&
              v$.currentPreferences.currency.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            required
          >
            <BaseMultiselect
              v-model="currentPreferences.currency"
              :content-loading="isFetchingInitialData"
              :options="globalStore.currencies"
              label="name"
              value-prop="id"
              :searchable="true"
              track-by="name"
              :placeholder="$tc('settings.currencies.select_currency')"
              :invalid="v$.currentPreferences.currency.$error"
              class="w-full"
            >
            </BaseMultiselect>
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('settings.preferences.default_language')"
            :error="
              v$.currentPreferences.language.$error &&
              v$.currentPreferences.language.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            required
          >
            <BaseMultiselect
              v-model="currentPreferences.language"
              :content-loading="isFetchingInitialData"
              :options="globalStore.languages"
              label="name"
              value-prop="code"
              :placeholder="$tc('settings.preferences.select_language')"
              class="w-full"
              track-by="name"
              :searchable="true"
              :invalid="v$.currentPreferences.language.$error"
            />
          </BaseInputGroup>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 md:mb-6">
          <BaseInputGroup
            :label="$t('wizard.date_format')"
            :error="
              v$.currentPreferences.carbon_date_format.$error &&
              v$.currentPreferences.carbon_date_format.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            required
          >
            <BaseMultiselect
              v-model="currentPreferences.carbon_date_format"
              :content-loading="isFetchingInitialData"
              :options="globalStore.dateFormats"
              label="display_date"
              value-prop="carbon_format_value"
              :placeholder="$tc('settings.preferences.select_date_format')"
              track-by="display_date"
              searchable
              :invalid="v$.currentPreferences.carbon_date_format.$error"
              class="w-full"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('wizard.time_zone')"
            :error="
              v$.currentPreferences.time_zone.$error &&
              v$.currentPreferences.time_zone.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            required
          >
            <BaseMultiselect
              v-model="currentPreferences.time_zone"
              :content-loading="isFetchingInitialData"
              :options="globalStore.timeZones"
              label="key"
              value-prop="value"
              :placeholder="$tc('settings.preferences.select_time_zone')"
              track-by="key"
              :searchable="true"
              :invalid="v$.currentPreferences.time_zone.$error"
            />
          </BaseInputGroup>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
          <BaseInputGroup
            :label="$t('wizard.fiscal_year')"
            :error="
              v$.currentPreferences.fiscal_year.$error &&
              v$.currentPreferences.fiscal_year.$errors[0].$message
            "
            :content-loading="isFetchingInitialData"
            required
          >
            <BaseMultiselect
              v-model="currentPreferences.fiscal_year"
              :content-loading="isFetchingInitialData"
              :options="globalStore.fiscalYears"
              label="key"
              value-prop="value"
              :placeholder="$tc('settings.preferences.select_financial_year')"
              :invalid="v$.currentPreferences.fiscal_year.$error"
              track-by="key"
              :searchable="true"
              class="w-full"
            />
          </BaseInputGroup>
        </div>

        <BaseButton
          :loading="isSaving"
          :disabled="isSaving"
          :content-loading="isFetchingInitialData"
          class="mt-4"
        >
          <template #left="slotProps">
            <BaseIcon name="SaveIcon" :class="slotProps.class" />
          </template>
          {{ $t('wizard.save_cont') }}
        </BaseButton>
      </div>
    </form>
  </BaseWizardStep>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { ref, computed, onMounted, reactive } from 'vue'
import { required, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import Ls from '@/scripts/services/ls.js'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useRouter } from 'vue-router'

const emit = defineEmits(['next'])

const isSaving = ref(false)
let isFetchingInitialData = ref(false)

let currentPreferences = reactive({
  currency: 1,
  language: 'en',
  carbon_date_format: 'd M Y',
  time_zone: 'UTC',
  fiscal_year: '1-12',
})

const { tm, t } = useI18n()
const router = useRouter()

isFetchingInitialData.value = true

const options = reactive([
  {
    title: tm('settings.customization.invoices.allow'),
    value: 'allow',
  },
  {
    title: tm(
      'settings.customization.invoices.disable_on_invoice_partial_paid'
    ),
    value: 'disable_on_invoice_partial_paid',
  },
  {
    title: tm('settings.customization.invoices.disable_on_invoice_paid'),
    value: 'disable_on_invoice_paid',
  },
  {
    title: tm('settings.customization.invoices.disable_on_invoice_sent'),
    value: 'disable_on_invoice_sent',
  },
])

const dialogStore = useDialogStore()
const globalStore = useGlobalStore()
const companyStore = useCompanyStore()
const userStore = useUserStore()
const notificationStore = useNotificationStore()

let fiscalYears = {
  key: 'fiscal_years',
}
let data = {
  key: 'languages',
}

isFetchingInitialData.value = true
Promise.all([
  globalStore.fetchCurrencies(),
  globalStore.fetchDateFormats(),
  globalStore.fetchTimeZones(),
  globalStore.fetchCountries(),
  globalStore.fetchConfig(fiscalYears),
  globalStore.fetchConfig(data),
]).then(([res1]) => {
  isFetchingInitialData.value = false
})

const rules = computed(() => {
  return {
    currentPreferences: {
      currency: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      language: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      carbon_date_format: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      time_zone: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      fiscal_year: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
  }
})

const v$ = useVuelidate(rules, { currentPreferences })

async function next() {
  v$.value.currentPreferences.$touch()
  if (v$.value.$invalid) {
    return true
  }

  dialogStore
    .openDialog({
      title: t('general.do_you_wish_to_continue'),
      message: t('wizard.currency_set_alert'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then(async (res) => {
      if (res) {
        let data = {
          settings: {
            ...currentPreferences,
          },
        }

        isSaving.value = true

        delete data.settings.discount_per_item

        let res = await companyStore.updateCompanySettings({
          data,
        })

        if (res.data) {
          isSaving.value = false

          let data = {
            settings: {
              language: currentPreferences.language,
            },
          }

          let res1 = await userStore.updateUserSettings(data)

          if (res1.data) {
            emit('next', 'COMPLETED')
            notificationStore.showNotification({
              type: 'success',
              message: 'Login Successful',
            })
            router.push('/admin/dashboard')
          }

          Ls.set('auth.token', res.data.token)
        }

        return true
      }

      isSaving.value = false
      return true
    })
}
</script>

<template>
  <form action="" class="relative" @submit.prevent="updatePreferencesData">
    <BaseSettingCard
      :title="$t('settings.menu_title.preferences')"
      :description="$t('settings.preferences.general_settings')"
    >
      <BaseInputGrid class="mt-5">
        <BaseInputGroup
          :content-loading="isFetchingInitialData"
          :label="$tc('settings.preferences.currency')"
          :help-text="$t('settings.preferences.company_currency_unchangeable')"
          :error="v$.currency.$error && v$.currency.$errors[0].$message"
          required
        >
          <BaseMultiselect
            v-model="settingsForm.currency"
            :content-loading="isFetchingInitialData"
            :options="globalStore.currencies"
            label="name"
            value-prop="id"
            :searchable="true"
            track-by="name"
            :invalid="v$.currency.$error"
            disabled
            class="w-full"
          >
          </BaseMultiselect>
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.preferences.default_language')"
          :content-loading="isFetchingInitialData"
          :error="v$.language.$error && v$.language.$errors[0].$message"
          required
        >
          <BaseMultiselect
            v-model="settingsForm.language"
            :content-loading="isFetchingInitialData"
            :options="globalStore.config.languages"
            label="name"
            value-prop="code"
            class="w-full"
            track-by="name"
            :searchable="true"
            :invalid="v$.language.$error"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.preferences.time_zone')"
          :content-loading="isFetchingInitialData"
          :error="v$.time_zone.$error && v$.time_zone.$errors[0].$message"
          required
        >
          <BaseMultiselect
            v-model="settingsForm.time_zone"
            :content-loading="isFetchingInitialData"
            :options="globalStore.timeZones"
            label="key"
            value-prop="value"
            track-by="key"
            :searchable="true"
            :invalid="v$.time_zone.$error"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :label="$tc('settings.preferences.date_format')"
          :content-loading="isFetchingInitialData"
          :error="
            v$.carbon_date_format.$error &&
            v$.carbon_date_format.$errors[0].$message
          "
          required
        >
          <BaseMultiselect
            v-model="settingsForm.carbon_date_format"
            :content-loading="isFetchingInitialData"
            :options="globalStore.dateFormats"
            label="display_date"
            value-prop="carbon_format_value"
            track-by="display_date"
            searchable
            :invalid="v$.carbon_date_format.$error"
            class="w-full"
          />
        </BaseInputGroup>

        <BaseInputGroup
          :content-loading="isFetchingInitialData"
          :error="v$.fiscal_year.$error && v$.fiscal_year.$errors[0].$message"
          :label="$tc('settings.preferences.fiscal_year')"
          required
        >
          <BaseMultiselect
            v-model="settingsForm.fiscal_year"
            :content-loading="isFetchingInitialData"
            :options="globalStore.config.fiscal_years"
            label="key"
            value-prop="value"
            :invalid="v$.fiscal_year.$error"
            track-by="key"
            :searchable="true"
            class="w-full"
          />
        </BaseInputGroup>
      </BaseInputGrid>

      <BaseButton
        :content-loading="isFetchingInitialData"
        :disabled="isSaving"
        :loading="isSaving"
        type="submit"
        class="mt-6"
      >
        <template #left="slotProps">
          <BaseIcon name="SaveIcon" :class="slotProps.class" />
        </template>
        {{ $tc('settings.company_info.save') }}
      </BaseButton>

      <BaseDivider class="mt-6 mb-2" />

      <ul>
        <form @submit.prevent="submitData">
          <BaseSwitchSection
            v-model="expirePdfField"
            :title="$t('settings.preferences.expire_public_links')"
            :description="$t('settings.preferences.expire_setting_description')"
          />

          <!--pdf_link_expiry_days -->
          <BaseInputGroup
            v-if="expirePdfField"
            :content-loading="isFetchingInitialData"
            :label="$t('settings.preferences.expire_public_links')"
            class="mt-2 mb-4"
          >
            <BaseInput
              v-model="settingsForm.link_expiry_days"
              :disabled="
                settingsForm.automatically_expire_public_links === 'NO'
              "
              :content-loading="isFetchingInitialData"
              type="number"
            />
          </BaseInputGroup>

          <BaseButton
            :content-loading="isFetchingInitialData"
            :disabled="isDataSaving"
            :loading="isDataSaving"
            type="submit"
            class="mt-6"
          >
            <template #left="slotProps">
              <BaseIcon name="SaveIcon" :class="slotProps.class" />
            </template>
            {{ $tc('general.save') }}
          </BaseButton>
        </form>

        <BaseDivider class="mt-6 mb-2" />

        <BaseSwitchSection
          v-model="discountPerItemField"
          :title="$t('settings.preferences.discount_per_item')"
          :description="$t('settings.preferences.discount_setting_description')"
        />
      </ul>
    </BaseSettingCard>
  </form>
</template>

<script setup>
import { ref, computed, watch, reactive } from 'vue'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useI18n } from 'vue-i18n'
import { required, helpers } from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'

const companyStore = useCompanyStore()
const globalStore = useGlobalStore()
const { t, tm } = useI18n()

let isSaving = ref(false)
let isDataSaving = ref(false)
let isFetchingInitialData = ref(false)

const settingsForm = reactive({ ...companyStore.selectedCompanySettings })

const retrospectiveEditOptions = computed(() => {
  return globalStore.config.retrospective_edits.map((option) => {
    option.title = t(option.key)
    return option
  })
})

watch(
  () => settingsForm.carbon_date_format,
  (val) => {
    if (val) {
      const dateFormatObject = globalStore.dateFormats.find((d) => {
        return d.carbon_format_value === val
      })

      settingsForm.moment_date_format = dateFormatObject.moment_format_value
    }
  }
)

const discountPerItemField = computed({
  get: () => {
    return settingsForm.discount_per_item === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        discount_per_item: value,
      },
    }

    settingsForm.discount_per_item = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

const expirePdfField = computed({
  get: () => {
    return settingsForm.automatically_expire_public_links === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        automatically_expire_public_links: value,
      },
    }

    settingsForm.automatically_expire_public_links = value
  },
})

const rules = computed(() => {
  return {
    currency: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    language: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    carbon_date_format: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    moment_date_format: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    time_zone: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    fiscal_year: {
      required: helpers.withMessage(t('validation.required'), required),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => settingsForm)
)

setInitialData()

async function setInitialData() {
  isFetchingInitialData.value = true
  Promise.all([
    globalStore.fetchCurrencies(),
    globalStore.fetchDateFormats(),
    globalStore.fetchTimeZones(),
  ]).then(([res1]) => {
    isFetchingInitialData.value = false
  })
}

async function updatePreferencesData() {
  v$.value.$touch()
  if (v$.value.$invalid) {
    return
  }

  let data = {
    settings: {
      ...settingsForm,
    },
  }

  isSaving.value = true
  delete data.settings.link_expiry_days
  let res = await companyStore.updateCompanySettings({
    data: data,
    message: 'settings.preferences.updated_message',
  })

  isSaving.value = false
}

async function submitData() {
  isDataSaving.value = true

  let res = await companyStore.updateCompanySettings({
    data: {
      settings: {
        link_expiry_days: settingsForm.link_expiry_days,
        automatically_expire_public_links:
          settingsForm.automatically_expire_public_links,
      },
    },
    message: 'settings.preferences.updated_message',
  })

  isDataSaving.value = false
}
</script>

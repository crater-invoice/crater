<template>
  <BaseCard>
    <h6 class="font-medium text-lg text-left">
      {{ $t('settings.exchange_rate.title') }}
    </h6>
    <p class="mt-2 text-sm leading-snug text-gray-500" style="max-width: 680px">
      {{
        $t('settings.exchange_rate.description', {
          currency: companyStore.selectedCompanyCurrency.name,
        })
      }}
    </p>

    <form action="" @submit.prevent="submitBulkUpdate">
      <ValidateEach
        v-for="(c, i) in exchangeRateStore.bulkCurrencies"
        :key="i"
        :state="c"
        :rules="currencyArrayRules"
      >
        <template #default="{ v }">
          <BaseInputGroup
            class="my-5"
            :label="`${c.code} to ${companyStore.selectedCompanyCurrency.code}`"
            :error="
              v.exchange_rate.$error && v.exchange_rate.$errors[0].$message
            "
            required
          >
            <BaseInput
              v-model="c.exchange_rate"
              :addon="`1 ${c.code} =`"
              :invalid="v.exchange_rate.$error"
              @input="v.exchange_rate.$touch()"
            >
              <template #right>
                <span class="text-gray-500 sm:text-sm">
                  {{ companyStore.selectedCompanyCurrency.code }}
                </span>
              </template>
            </BaseInput>
            <span class="text-gray-400 text-xs mt-2 font-light">
              {{
                $t('settings.exchange_rate.exchange_help_text', {
                  currency: c.code,
                  baseCurrency: companyStore.selectedCompanyCurrency.code,
                })
              }}
            </span>
          </BaseInputGroup>
        </template>
      </ValidateEach>
      <div
        slot="footer"
        class="
          z-0
          flex
          justify-end
          mt-4
          pt-4
          border-t border-gray-200 border-solid border-modal-bg
        "
      >
        <BaseButton :loading="isSaving" variant="primary" type="submit">
          {{ $t('general.save') }}
        </BaseButton>
      </div>
    </form>
  </BaseCard>
</template>

<script setup>
import { useExchangeRateStore } from '@/scripts/admin/stores/exchange-rate'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/scripts/stores/notification'
import { computed, ref } from '@vue/runtime-core'
import { useI18n } from 'vue-i18n'
import useVuelidate from '@vuelidate/core'
import { required, helpers, numeric, decimal } from '@vuelidate/validators'
import { ValidateEach } from '@vuelidate/components'

const exchangeRateStore = useExchangeRateStore()
const notificationStore = useNotificationStore()
const companyStore = useCompanyStore()

const { t, tm } = useI18n()
let isSaving = ref(false)
let isLoading = ref(false)

const currencyArrayRules = {
  exchange_rate: {
    required: helpers.withMessage(t('validation.required'), required),
    decimal: helpers.withMessage(t('validation.valid_exchange_rate'), decimal),
  },
}
const v = useVuelidate()

const emit = defineEmits(['update'])

async function submitBulkUpdate() {
  v.value.$touch()
  if (v.value.$invalid) {
    return true
  }
  isSaving.value = true
  let data = exchangeRateStore.bulkCurrencies.map((_c) => {
    return {
      id: _c.id,
      exchange_rate: _c.exchange_rate,
    }
  })
  let res = await exchangeRateStore.updateBulkExchangeRate({ currencies: data })
  if (res.data.success) {
    emit('update', res.data.success)
  }
  isSaving.value = false
}
</script>

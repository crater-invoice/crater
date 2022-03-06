<template>
  <BaseModal
    :show="modalActive"
    @close="closeExchangeRateModal"
    @open="fetchInitialData"
  >
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}

        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeExchangeRateModal"
        />
      </div>
    </template>

    <form @submit.prevent="submitExchangeRate">
      <div class="px-4 md:px-8 py-8 overflow-y-auto sm:p-6">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$tc('settings.exchange_rate.driver')"
            :content-loading="isFetchingInitialData"
            required
            :error="
              v$.currentExchangeRate.driver.$error &&
              v$.currentExchangeRate.driver.$errors[0].$message
            "
            :help-text="driverSite"
          >
            <BaseMultiselect
              v-model="exchangeRateStore.currentExchangeRate.driver"
              :options="driversLists"
              :content-loading="isFetchingInitialData"
              value-prop="value"
              :can-deselect="true"
              label="key"
              :searchable="true"
              :invalid="v$.currentExchangeRate.driver.$error"
              track-by="key"
              @update:modelValue="resetCurrency"
              @input="v$.currentExchangeRate.driver.$touch()"
            />
          </BaseInputGroup>
          <BaseInputGroup
            v-if="isCurrencyConverter"
            required
            :label="$t('settings.exchange_rate.server')"
            :content-loading="isFetchingInitialData"
            :error="
              v$.currencyConverter.type.$error &&
              v$.currencyConverter.type.$errors[0].$message
            "
          >
            <BaseMultiselect
              v-model="exchangeRateStore.currencyConverter.type"
              :content-loading="isFetchingInitialData"
              value-prop="value"
              searchable
              :options="serverOptions"
              :invalid="v$.currencyConverter.type.$error"
              label="value"
              track-by="value"
              @update:modelValue="resetCurrency"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('settings.exchange_rate.key')"
            required
            :content-loading="isFetchingInitialData"
            :error="
              v$.currentExchangeRate.key.$error &&
              v$.currentExchangeRate.key.$errors[0].$message
            "
          >
            <BaseInput
              v-model="exchangeRateStore.currentExchangeRate.key"
              :content-loading="isFetchingInitialData"
              type="text"
              name="key"
              :loading="isFetchingCurrencies"
              loading-position="right"
              :invalid="v$.currentExchangeRate.key.$error"
            />
          </BaseInputGroup>

          <BaseInputGroup
            v-if="exchangeRateStore.supportedCurrencies.length"
            :label="$t('settings.exchange_rate.currency')"
            :content-loading="isFetchingInitialData"
            :error="
              v$.currentExchangeRate.currencies.$error &&
              v$.currentExchangeRate.currencies.$errors[0].$message
            "
            :help-text="$t('settings.exchange_rate.currency_help_text')"
          >
            <BaseMultiselect
              v-model="exchangeRateStore.currentExchangeRate.currencies"
              :content-loading="isFetchingInitialData"
              value-prop="code"
              mode="tags"
              searchable
              :options="exchangeRateStore.supportedCurrencies"
              :invalid="v$.currentExchangeRate.currencies.$error"
              label="code"
              track-by="code"
              open-direction="top"
              @input="v$.currentExchangeRate.currencies.$touch()"
            />
          </BaseInputGroup>
          <!--  For Currency Converter  -->

          <BaseInputGroup
            v-if="isDedicatedServer"
            :label="$t('settings.exchange_rate.url')"
            :content-loading="isFetchingInitialData"
            :error="
              v$.currencyConverter.url.$error &&
              v$.currencyConverter.url.$errors[0].$message
            "
          >
            <BaseInput
              v-model="exchangeRateStore.currencyConverter.url"
              :content-loading="isFetchingInitialData"
              type="url"
              :invalid="v$.currencyConverter.url.$error"
              @input="v$.currencyConverter.url.$touch()"
            />
          </BaseInputGroup>

          <BaseSwitch
            v-model="exchangeRateStore.currentExchangeRate.active"
            class="flex"
            :label-right="$t('settings.exchange_rate.active')"
          />
        </BaseInputGrid>

        <BaseInfoAlert
          v-if="
            currenciesAlredayInUsed.length &&
            exchangeRateStore.currentExchangeRate.active
          "
          class="mt-5"
          :title="$t('settings.exchange_rate.currency_in_used')"
          :lists="[currenciesAlredayInUsed.toString()]"
          :actions="['Remove']"
          @hide="dismiss"
          @Remove="removeUsedSelectedCurrencies"
        />
      </div>
      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          class="mr-3"
          variant="primary-outline"
          type="button"
          :disabled="isSaving"
          @click="closeExchangeRateModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>
        <BaseButton
          :loading="isSaving"
          :disabled="isSaving || isFetchingCurrencies"
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
            exchangeRateStore.isEdit ? $t('general.update') : $t('general.save')
          }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useExchangeRateStore } from '@/scripts/admin/stores/exchange-rate'
import { useModalStore } from '@/scripts/stores/modal'
import useVuelidate from '@vuelidate/core'
import { debounce } from 'lodash'

import { useI18n } from 'vue-i18n'
import {
  required,
  minLength,
  helpers,
  requiredIf,
  url,
} from '@vuelidate/validators'

const { t } = useI18n()

let isSaving = ref(false)
let isFetchingInitialData = ref(false)
let isFetchingCurrencies = ref(false)
let currenciesAlredayInUsed = ref([])
let currenctPorivderOldCurrencies = ref([])
const modalStore = useModalStore()
const exchangeRateStore = useExchangeRateStore()

let serverOptions = ref([])

const rules = computed(() => {
  return {
    currentExchangeRate: {
      key: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      driver: {
        required: helpers.withMessage(t('validation.required'), required),
      },
      currencies: {
        required: helpers.withMessage(t('validation.required'), required),
      },
    },
    currencyConverter: {
      type: {
        required: helpers.withMessage(
          t('validation.required'),
          requiredIf(isCurrencyConverter)
        ),
      },
      url: {
        required: helpers.withMessage(
          t('validation.required'),
          requiredIf(isDedicatedServer)
        ),
        url: helpers.withMessage(t('validation.invalid_url'), url),
      },
    },
  }
})
const driversLists = computed(() => {
  return exchangeRateStore.drivers.map((item) => {
    return Object.assign({}, item, {
      key: t(item.key),
    })
  })
})

const modalActive = computed(() => {
  return (
    modalStore.active &&
    modalStore.componentName === 'ExchangeRateProviderModal'
  )
})

const modalTitle = computed(() => {
  return modalStore.title
})

const isCurrencyConverter = computed(() => {
  return exchangeRateStore.currentExchangeRate.driver === 'currency_converter'
})

const isDedicatedServer = computed(() => {
  return (
    exchangeRateStore.currencyConverter &&
    exchangeRateStore.currencyConverter.type === 'DEDICATED'
  )
})

const driverSite = computed(() => {
  switch (exchangeRateStore.currentExchangeRate.driver) {
    case 'currency_converter':
      return `https://www.currencyconverterapi.com`

    case 'currency_freak':
      return 'https://currencyfreaks.com'

    case 'currency_layer':
      return 'https://currencylayer.com'

    case 'open_exchange_rate':
      return 'https://openexchangerates.org'

    default:
      return ''
  }
})
const v$ = useVuelidate(
  rules,
  computed(() => exchangeRateStore)
)

function dismiss() {
  currenciesAlredayInUsed.value = []
}
function removeUsedSelectedCurrencies() {
  const { currencies } = exchangeRateStore.currentExchangeRate
  currenciesAlredayInUsed.value.forEach((uc) => {
    currencies.forEach((c, i) => {
      if (c === uc) {
        currencies.splice(i, 1)
      }
    })
  })
  currenciesAlredayInUsed.value = []
}

function resetCurrency() {
  exchangeRateStore.currentExchangeRate.key = null
  exchangeRateStore.currentExchangeRate.currencies = []
  exchangeRateStore.supportedCurrencies = []
}

function resetModalData() {
  exchangeRateStore.supportedCurrencies = []
  currenctPorivderOldCurrencies.value = []
  exchangeRateStore.currentExchangeRate = {
    id: null,
    name: '',
    driver: '',
    key: '',
    active: true,
    currencies: [],
  }

  exchangeRateStore.currencyConverter = {
    type: '',
    url: '',
  }
  currenciesAlredayInUsed.value = []
}

async function fetchInitialData() {
  exchangeRateStore.currentExchangeRate.driver = 'currency_converter'
  let params = {}
  if (exchangeRateStore.isEdit) {
    params.provider_id = exchangeRateStore.currentExchangeRate.id
  }
  isFetchingInitialData.value = true
  await exchangeRateStore.fetchDefaultProviders()
  await exchangeRateStore.fetchActiveCurrency(params)

  currenctPorivderOldCurrencies.value =
    exchangeRateStore.currentExchangeRate.currencies

  isFetchingInitialData.value = false
}

watch(
  () => isCurrencyConverter.value,
  (newVal, oldValue) => {
    if (newVal) {
      fetchServers()
    }
  },
  { immediate: true }
)

watch(
  () => exchangeRateStore.currentExchangeRate.key,
  (newVal, oldValue) => {
    if (newVal) {
      fetchCurrencies()
    }
  }
)

watch(
  () => exchangeRateStore?.currencyConverter?.type,
  (newVal, oldValue) => {
    if (newVal) {
      fetchCurrencies()
    }
  }
)

fetchCurrencies = debounce(fetchCurrencies, 500)

function validate() {
  v$.value.$touch()
  checkingIsActiveCurrencies()
  if (
    v$.value.$invalid ||
    (currenciesAlredayInUsed.value.length &&
      exchangeRateStore.currentExchangeRate.active)
  ) {
    return true
  }
  return false
}

async function submitExchangeRate() {
  if (validate()) {
    return true
  }
  let data = {
    ...exchangeRateStore.currentExchangeRate,
  }
  if (isCurrencyConverter.value) {
    data.driver_config = {
      ...exchangeRateStore.currencyConverter,
    }
    if (!isDedicatedServer.value) {
      data.driver_config.url = ''
    }
  }
  const action = exchangeRateStore.isEdit
    ? exchangeRateStore.updateProvider
    : exchangeRateStore.addProvider
  isSaving.value = true

  await action(data)
    .then((res) => {
      isSaving.value = false
      modalStore.refreshData ? modalStore.refreshData() : ''
      closeExchangeRateModal()
    })
    .catch((err) => {
      isSaving.value = false
    })
}

async function fetchServers() {
  let res = await exchangeRateStore.getCurrencyConverterServers()
  serverOptions.value = res.data.currency_converter_servers
  exchangeRateStore.currencyConverter.type = 'FREE'
}

function fetchCurrencies() {
  const { driver, key } = exchangeRateStore.currentExchangeRate
  if (driver && key) {
    isFetchingCurrencies.value = true
    let data = {
      driver: driver,
      key: key,
    }
    if (
      isCurrencyConverter.value &&
      !exchangeRateStore.currencyConverter.type
    ) {
      isFetchingCurrencies.value = false
      return
    }
    if (exchangeRateStore?.currencyConverter?.type) {
      data.type = exchangeRateStore.currencyConverter.type
    }

    exchangeRateStore
      .fetchCurrencies(data)
      .then((res) => {
        isFetchingCurrencies.value = false
      })
      .catch((err) => {
        isFetchingCurrencies.value = false
      })
  }
}

function checkingIsActiveCurrencies(showError = true) {
  currenciesAlredayInUsed.value = []
  const { currencies } = exchangeRateStore.currentExchangeRate

  if (currencies.length && exchangeRateStore.activeUsedCurrencies?.length) {
    currencies.forEach((curr) => {
      if (exchangeRateStore.activeUsedCurrencies.includes(curr)) {
        currenciesAlredayInUsed.value.push(curr)
      }
    })
  }
}

function closeExchangeRateModal() {
  modalStore.closeModal()
  setTimeout(() => {
    resetModalData()
    v$.value.$reset()
  }, 300)
}
</script>

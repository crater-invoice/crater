<template>
  <ExchangeRateProviderModal />
  <BaseCard>
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="text-lg font-medium text-left">
          {{ $t('settings.menu_title.exchange_rate') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-left text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.exchange_rate.providers_description') }}
        </p>
      </div>
      <div class="mt-4 lg:mt-0 lg:ml-2">
        <BaseButton
          variant="primary-outline"
          size="lg"
          @click="addExchangeRate"
        >
          <template #left="slotProps">
            <PlusIcon :class="slotProps.class" />
          </template>
          {{ $t('settings.exchange_rate.new_driver') }}
        </BaseButton>
      </div>
    </div>

    <BaseTable ref="table" class="mt-16" :data="fetchData" :columns="drivers">
      <template #cell-driver="{ row }">
        <span class="capitalize">{{ row.data.driver.replace('_', ' ') }}</span>
      </template>
      <template #cell-active="{ row }">
        <BaseBadge
          :bg-color="
            utils.getBadgeStatusColor(row.data.active ? 'YES' : 'NO').bgColor
          "
          :color="
            utils.getBadgeStatusColor(row.data.active ? 'YES' : 'NO').color
          "
        >
          {{ row.data.active ? 'YES' : 'NO' }}
        </BaseBadge>
      </template>
      <template #cell-actions="{ row }">
        <BaseDropdown>
          <template #activator>
            <div class="inline-block">
              <DotsHorizontalIcon class="w-5 text-gray-500" />
            </div>
          </template>

          <BaseDropdownItem @click="editExchangeRate(row.data.id)">
            <PencilIcon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.edit') }}
          </BaseDropdownItem>

          <BaseDropdownItem @click="removeExchangeRate(row.data.id)">
            <TrashIcon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </template>
    </BaseTable>
  </BaseCard>
</template>

<script setup>
import { useExchangeRateStore } from '@/scripts/admin/stores/exchange-rate'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useModalStore } from '@/scripts/stores/modal'
import { useDialogStore } from '@/scripts/stores/dialog'
import { SaveIcon } from '@heroicons/vue/outline'
import { ref, computed, inject, reactive } from 'vue'
import ExchangeRateProviderModal from '@/scripts/admin/components/modal-components/ExchangeRateProviderModal.vue'
import { useI18n } from 'vue-i18n'
import {
  PlusIcon,
  DotsHorizontalIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/outline'
import BaseTable from '@/scripts/components/base/base-table/BaseTable.vue'

// store

const { tm, t } = useI18n()
const companyStore = useCompanyStore()
const exchangeRateStore = useExchangeRateStore()
const modalStore = useModalStore()
const dialogStore = useDialogStore()
//created

// local state

let table = ref('')
const utils = inject('utils')
const drivers = computed(() => {
  return [
    {
      key: 'driver',
      label: t('settings.exchange_rate.driver'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'key',
      label: t('settings.exchange_rate.key'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'active',
      label: t('settings.exchange_rate.active'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'actions',
      label: '',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

async function fetchData({ page, sort }) {
  let data = reactive({
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  })

  let response = await exchangeRateStore.fetchProviders(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: 5,
    },
  }
}
async function updateRate() {
  await exchangeRateStore.updateExchangeRate(
    exchangeRateStore.currentExchangeRate.rate
  )
}

function addExchangeRate() {
  modalStore.openModal({
    title: t('settings.exchange_rate.new_driver'),
    componentName: 'ExchangeRateProviderModal',
    size: 'md',
    refreshData: table.value && table.value.refresh,
  })
}

function editExchangeRate(data) {
  exchangeRateStore.fetchProvider(data)
  modalStore.openModal({
    title: t('settings.exchange_rate.edit_driver'),
    componentName: 'ExchangeRateProviderModal',
    size: 'md',
    data: data,
    refreshData: table.value && table.value.refresh,
  })
}

function removeExchangeRate(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.exchange_rate.exchange_rate_confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await exchangeRateStore.deleteExchangeRate(id)
        table.value && table.value.refresh()
      }
    })
}
</script>

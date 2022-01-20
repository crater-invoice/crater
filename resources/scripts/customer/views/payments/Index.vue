<template>
  <BasePage>
    <BasePageHeader :title="$t('payments.title')">
      <BaseBreadcrumb slot="breadcrumbs">
        <BaseBreadcrumbItem
          :title="$t('general.home')"
          :to="`/${globalStore.companySlug}/customer/dashboard`"
        />

        <BaseBreadcrumbItem :title="$tc('payments.payment', 2)" to="#" active />
      </BaseBreadcrumb>

      <template #actions>
        <BaseButton
          v-show="paymentStore.totalPayments"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <template #right="slotProps">
            <BaseIcon
              v-if="!showFilters"
              :class="slotProps.class"
              name="FilterIcon"
            />
            <BaseIcon v-else :class="slotProps.class" name="XIcon" />
          </template>
        </BaseButton>
      </template>
    </BasePageHeader>

    <BaseFilterWrapper v-show="showFilters" @clear="clearFilter">
      <BaseInputGroup :label="$t('payments.payment_number')" class="px-3">
        <BaseInput
          v-model="filters.payment_number"
          :placeholder="$t('payments.payment_number')"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$t('payments.payment_mode')" class="px-3">
        <BaseMultiselect
          v-model="filters.payment_mode"
          value-prop="id"
          track-by="name"
          :filter-results="false"
          label="name"
          resolve-on-load
          :delay="100"
          searchable
          :options="searchPayment"
          :placeholder="$t('payments.payment_mode')"
        />
      </BaseInputGroup>
    </BaseFilterWrapper>

    <BaseEmptyPlaceholder
      v-if="showEmptyScreen"
      :title="$t('payments.no_payments')"
      :description="$t('payments.list_of_payments')"
    >
      <CapsuleIcon class="mt-5 mb-4" />
    </BaseEmptyPlaceholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <BaseTable
        ref="table"
        :data="fetchData"
        :columns="paymentColumns"
        :placeholder-count="paymentStore.totalPayments >= 20 ? 10 : 5"
        class="mt-10"
      >
        <template #cell-payment_date="{ row }">
          {{ row.data.formatted_payment_date }}
        </template>

        <template #cell-payment_number="{ row }">
          <router-link
            :to="{
              path: `payments/${row.data.id}/view`,
            }"
            class="font-medium text-primary-500"
          >
            {{ row.data.payment_number }}
          </router-link>
        </template>

        <template #cell-payment_mode="{ row }">
          <span>
            {{
              row.data.payment_method
                ? row.data.payment_method.name
                : $t('payments.not_selected')
            }}
          </span>
        </template>

        <template #cell-invoice_number="{ row }">
          <span>
            {{
              row.data.invoice?.invoice_number
                ? row.data.invoice?.invoice_number
                : $t('payments.no_invoice')
            }}
          </span>
        </template>

        <template #cell-amount="{ row }">
          <div v-html="utils.formatMoney(row.data.amount, currency)" />
        </template>

        <template #cell-actions="{ row }">
          <BaseDropdown>
            <template #activator>
              <BaseIcon name="DotsHorizontalIcon" class="w-5 text-gray-500" />
            </template>
            <router-link :to="`payments/${row.data.id}/view`">
              <BaseDropdownItem>
                <BaseIcon name="EyeIcon" class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </BaseDropdownItem>
            </router-link>
          </BaseDropdown>
        </template>
      </BaseTable>
    </div>
  </BasePage>
</template>

<script setup>
import { debouncedWatch } from '@vueuse/core'
import BaseTable from '@/scripts/components/base/base-table/BaseTable.vue'
import CapsuleIcon from '@/scripts/components/icons/empty/CapsuleIcon.vue'
import { ref, reactive, inject, computed } from 'vue'
import BaseDropdownItem from '@/scripts/components/base/BaseDropdownItem.vue'
import BaseDropdown from '@/scripts/components/base/BaseDropdown.vue'
import { useI18n } from 'vue-i18n'
import { usePaymentStore } from '@/scripts/customer/stores/payment'
import { useGlobalStore } from '@/scripts/customer/stores/global'
import { useRoute } from 'vue-router'

const { tm, t } = useI18n()
let showFilters = ref(false)
let sortedBy = ref('created_at')
let isFetchingInitialData = ref(true)
let table = ref(null)
const filters = reactive({
  payment_mode: '',
  payment_number: '',
})

//Utils
const utils = inject('utils')

//Store
const route = useRoute()
const paymentStore = usePaymentStore()
const globalStore = useGlobalStore()

// Computed Props

const showEmptyScreen = computed(() => {
  return !paymentStore.totalPayments && !isFetchingInitialData.value
})

const currency = computed(() => {
  return globalStore.currency
})

// Payment Table columns Data

const paymentColumns = computed(() => {
  return [
    {
      key: 'payment_date',
      label: t('payments.date'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    { key: 'payment_number', label: t('payments.payment_number') },
    { key: 'payment_mode', label: t('payments.payment_mode') },
    { key: 'invoice_number', label: t('invoices.invoice_number') },
    { key: 'amount', label: t('payments.amount') },
    {
      key: 'actions',
      label: '',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

// Created

debouncedWatch(
  filters,
  () => {
    setFilters()
  },
  { debounce: 500 }
)

// Methods

async function searchPayment(search) {
  let res = await paymentStore.fetchPaymentModes(
    search,
    globalStore.companySlug
  )
  return res.data.data
}

async function fetchData({ page, filter, sort }) {
  let data = {
    payment_method_id:
      filters.payment_mode !== null ? filters.payment_mode : '',
    payment_number: filters.payment_number,
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  isFetchingInitialData.value = true
  let response = await paymentStore.fetchPayments(data, globalStore.companySlug)

  isFetchingInitialData.value = false

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: 10,
    },
  }
}

function refreshTable() {
  table.value.refresh()
}

function setFilters() {
  refreshTable()
}

function clearFilter() {
  filters.customer = ''
  filters.payment_mode = ''
  filters.payment_number = ''
}

function toggleFilter() {
  if (showFilters.value) {
    clearFilter()
  }

  showFilters.value = !showFilters.value
}
</script>

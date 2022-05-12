<template>
  <BasePage>
    <SendInvoiceModal />

    <BasePageHeader :title="$t('recurring_invoices.title')">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />

        <BaseBreadcrumbItem
          :title="$tc('recurring_invoices.invoice', 2)"
          to="#"
          active
        />
      </BaseBreadcrumb>

      <template #actions>
        <BaseButton
          v-show="recurringInvoiceStore.totalRecurringInvoices"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <template #right="slotProps">
            <BaseIcon
              v-if="!showFilters"
              name="FilterIcon"
              :class="slotProps.class"
            />
            <BaseIcon v-else name="XIcon" :class="slotProps.class" />
          </template>
        </BaseButton>

        <router-link
          v-if="userStore.hasAbilities(abilities.CREATE_RECURRING_INVOICE)"
          to="recurring-invoices/create"
        >
          <BaseButton variant="primary" class="ml-4">
            <template #left="slotProps">
              <BaseIcon name="PlusIcon" :class="slotProps.class" />
            </template>
            {{ $t('recurring_invoices.new_invoice') }}
          </BaseButton>
        </router-link>
      </template>
    </BasePageHeader>

    <BaseFilterWrapper v-show="showFilters" @clear="clearFilter">
      <BaseInputGroup :label="$tc('customers.customer', 1)">
        <BaseCustomerSelectInput
          v-model="filters.customer_id"
          :placeholder="$t('customers.type_or_click')"
          value-prop="id"
          label="name"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$t('recurring_invoices.status')">
        <BaseMultiselect
          v-model="filters.status"
          :options="statusList"
          searchable
          :placeholder="$t('general.select_a_status')"
          @update:modelValue="setActiveTab"
          @remove="clearStatusSearch()"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$t('general.from')">
        <BaseDatePicker
          v-model="filters.from_date"
          :calendar-button="true"
          calendar-button-icon="calendar"
        />
      </BaseInputGroup>

      <div
        class="hidden w-8 h-0 mx-4 border border-gray-400 border-solid xl:block"
        style="margin-top: 1.5rem"
      />

      <BaseInputGroup :label="$t('general.to')">
        <BaseDatePicker
          v-model="filters.to_date"
          :calendar-button="true"
          calendar-button-icon="calendar"
        />
      </BaseInputGroup>
    </BaseFilterWrapper>

    <BaseEmptyPlaceholder
      v-show="showEmptyScreen"
      :title="$t('recurring_invoices.no_invoices')"
      :description="$t('recurring_invoices.list_of_invoices')"
    >
      <MoonwalkerIcon class="mt-5 mb-4" />

      <template
        v-if="userStore.hasAbilities(abilities.CREATE_RECURRING_INVOICE)"
        #actions
      >
        <BaseButton
          variant="primary-outline"
          @click="$router.push('/admin/recurring-invoices/create')"
        >
          <template #left="slotProps">
            <BaseIcon name="PlusIcon" :class="slotProps.class" />
          </template>
          {{ $t('recurring_invoices.add_new_invoice') }}
        </BaseButton>
      </template>
    </BaseEmptyPlaceholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <!-- Tabs -->
        <BaseTabGroup
          class="-mb-5"
          :default-index="currentStatusIndex"
          @change="setStatusFilter"
        >
          <BaseTab :title="$t('recurring_invoices.all')" filter="ALL" />
          <BaseTab :title="$t('recurring_invoices.active')" filter="ACTIVE" />
          <BaseTab :title="$t('recurring_invoices.on_hold')" filter="ON_HOLD" />
        </BaseTabGroup>

        <BaseDropdown
          v-if="recurringInvoiceStore.selectedRecurringInvoices.length"
          class="absolute float-right"
        >
          <template #activator>
            <span
              class="
                flex
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <BaseIcon name="ChevronDownIcon" class="h-5" />
            </span>
          </template>

          <BaseDropdownItem @click="removeMultipleRecurringInvoices()">
            <BaseIcon name="TrashIcon" class="mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </div>

      <BaseTable
        ref="table"
        :data="fetchData"
        :columns="invoiceColumns"
        :placeholder-count="
          recurringInvoiceStore.totalRecurringInvoices >= 20 ? 10 : 5
        "
        class="mt-10"
      >
        <!-- Select All Checkbox -->
        <template #header>
          <div class="absolute items-center left-6 top-2.5 select-none">
            <BaseCheckbox
              v-model="recurringInvoiceStore.selectAllField"
              variant="primary"
              @change="recurringInvoiceStore.selectAllRecurringInvoices"
            />
          </div>
        </template>

        <template #cell-checkbox="{ row }">
          <div class="relative block">
            <BaseCheckbox
              :id="row.id"
              v-model="selectField"
              :value="row.data.id"
            />
          </div>
        </template>

        <!-- Starts at  -->
        <template #cell-starts_at="{ row }">
          {{ row.data.formatted_starts_at }}
        </template>

        <!-- Customer  -->
        <template #cell-customer="{ row }">
          <router-link :to="{ path: `recurring-invoices/${row.data.id}/view` }">
            <BaseText
              :text="row.data.customer.name"
              :length="30"
              tag="span"
              class="font-medium text-primary-500 flex flex-col"
            />

            <BaseText
              :text="
                row.data.customer.contact_name
                  ? row.data.customer.contact_name
                  : ''
              "
              :length="30"
              tag="span"
              class="text-xs text-gray-400"
            />
          </router-link>
        </template>

        <!-- Frequency  -->
        <template #cell-frequency="{ row }">
          {{ getFrequencyLabel(row.data.frequency) }}
        </template>

        <!-- Status  -->
        <template #cell-status="{ row }">
          <BaseRecurringInvoiceStatusBadge
            :status="row.data.status"
            class="px-3 py-1"
          >
            {{ row.data.status }}
          </BaseRecurringInvoiceStatusBadge>
        </template>

        <!-- Amount  -->
        <template #cell-total="{ row }">
          <BaseFormatMoney
            :amount="row.data.total"
            :currency="row.data.customer.currency"
          />
        </template>

        <!-- Actions -->
        <template v-if="canViewActions" #cell-actions="{ row }">
          <RecurringInvoiceIndexDropdown :row="row.data" :table="table" />
        </template>
      </BaseTable>
    </div>
  </BasePage>
</template>

<script setup>
import { computed, onUnmounted, reactive, ref, watch, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useCustomerStore } from '@/scripts/admin/stores/customer'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'
import { useUserStore } from '@/scripts/admin/stores/user'
import { debouncedWatch } from '@vueuse/core'

import SendInvoiceModal from '@/scripts/admin/components/modal-components/SendInvoiceModal.vue'
import RecurringInvoiceIndexDropdown from '@/scripts/admin/components/dropdowns/RecurringInvoiceIndexDropdown.vue'
import MoonwalkerIcon from '@/scripts/components/icons/empty/MoonwalkerIcon.vue'
import abilities from '@/scripts/admin/stub/abilities'

const recurringInvoiceStore = useRecurringInvoiceStore()
const customerStore = useCustomerStore()
const dialogStore = useDialogStore()
const notificationStore = useNotificationStore()
const userStore = useUserStore()

const table = ref(null)
const { t } = useI18n()
const showFilters = ref(false)
const statusList = ref(['ACTIVE', 'ON_HOLD', 'ALL'])
const isRequestOngoing = ref(true)
const activeTab = ref('recurring-invoices.all')
const router = useRouter()

let filters = reactive({
  customer_id: '',
  status: '',
  from_date: '',
  to_date: '',
})

const showEmptyScreen = computed(
  () => !recurringInvoiceStore.totalRecurringInvoices && !isRequestOngoing.value
)

const selectField = computed({
  get: () => recurringInvoiceStore.selectedRecurringInvoices,
  set: (value) => {
    return recurringInvoiceStore.selectRecurringInvoice(value)
  },
})

const invoiceColumns = computed(() => {
  return [
    {
      key: 'checkbox',
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'starts_at',
      label: t('recurring_invoices.starts_at'),
      thClass: 'extra',
      tdClass: 'font-medium',
    },
    { key: 'customer', label: t('invoices.customer') },
    { key: 'frequency', label: t('recurring_invoices.frequency.title') },
    { key: 'status', label: t('invoices.status') },
    { key: 'total', label: t('invoices.total') },
    {
      key: 'actions',
      label: t('recurring_invoices.action'),
      tdClass: 'text-right text-sm font-medium',
      thClass: 'text-right',
      sortable: false,
    },
  ]
})

debouncedWatch(
  filters,
  () => {
    setFilters()
  },
  { debounce: 500 }
)

onUnmounted(() => {
  if (recurringInvoiceStore.selectAllField) {
    recurringInvoiceStore.selectAllRecurringInvoices()
  }
})

const currentStatusIndex = computed(() => {
  return statusList.value.findIndex((status) => status === filters.status)
})

function canViewActions() {
  return userStore.hasAbilities([
    abilities.DELETE_RECURRING_INVOICE,
    abilities.EDIT_RECURRING_INVOICE,
    abilities.VIEW_RECURRING_INVOICE,
  ])
}

function getFrequencyLabel(frequencyFormat) {
  const frequencyObj = recurringInvoiceStore.frequencies.find((frequency) => {
    return frequency.value === frequencyFormat
  })

  return frequencyObj ? frequencyObj.label : `CUSTOM: ${frequencyFormat}`
}

function refreshTable() {
  table.value && table.value.refresh()
}

async function fetchData({ page, filter, sort }) {
  let data = {
    customer_id: filters.customer_id,
    status: filters.status,
    from_date: filters.from_date,
    to_date: filters.to_date,
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  isRequestOngoing.value = true

  let response = await recurringInvoiceStore.fetchRecurringInvoices(data)

  isRequestOngoing.value = false

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

function setStatusFilter(val) {
  if (activeTab.value == val.title) {
    return true
  }

  activeTab.value = val.title

  switch (val.title) {
    case t('recurring_invoices.active'):
      filters.status = 'ACTIVE'
      break
    case t('recurring_invoices.on_hold'):
      filters.status = 'ON_HOLD'
      break
    case t('recurring_invoices.all'):
      filters.status = 'ALL'
      break
  }
}

function setFilters() {
  recurringInvoiceStore.$patch((state) => {
    state.selectedRecurringInvoices = []
    state.selectAllField = false
  })

  refreshTable()
}

function clearFilter() {
  filters.customer_id = ''
  filters.status = ''
  filters.from_date = ''
  filters.to_date = ''
  filters.invoice_number = ''
  activeTab.value = t('general.all')
}

async function removeMultipleRecurringInvoices(id = null) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('invoices.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await recurringInvoiceStore
          .deleteMultipleRecurringInvoices(id)
          .then((res) => {
            if (res.data.success) {
              refreshTable()
              recurringInvoiceStore.$patch((state) => {
                state.selectedRecurringInvoices = []
                state.selectAllField = false
              })
              notificationStore.showNotification({
                type: 'success',
                message: t('recurring_invoices.deleted_message', 2),
              })
            } else if (res.data.error) {
              notificationStore.showNotification({
                type: 'error',
                message: res.data.message,
              })
            }
          })
      }
    })
}

function toggleFilter() {
  if (showFilters.value) {
    clearFilter()
  }

  showFilters.value = !showFilters.value
}

async function clearStatusSearch(removedOption, id) {
  filters.status = ''
  refreshTable()
}

function setActiveTab(val) {
  switch (val) {
    case 'ACTIVE':
      activeTab.value = t('recurring_invoices.active')
      break
    case 'ON_HOLD':
      activeTab.value = t('recurring_invoices.on_hold')
      break
    case 'ALL':
      activeTab.value = t('recurring_invoices.all')
      break
  }
}
</script>

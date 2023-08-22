<template>
  <BasePage>
    <SendInvoiceModal />
    <BasePageHeader :title="$t('invoices.title')">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />
        <BaseBreadcrumbItem :title="$tc('invoices.invoice', 2)" to="#" active />
      </BaseBreadcrumb>

      <template #actions>
        <BaseButton
          v-show="invoiceStore.invoiceTotalCount"
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
          v-if="userStore.hasAbilities(abilities.CREATE_INVOICE)"
          to="invoices/create"
        >
          <BaseButton variant="primary" class="ml-4">
            <template #left="slotProps">
              <BaseIcon name="PlusIcon" :class="slotProps.class" />
            </template>
            {{ $t('invoices.new_invoice') }}
          </BaseButton>
        </router-link>
      </template>
    </BasePageHeader>

    <BaseFilterWrapper
      v-show="showFilters"
      :row-on-xl="true"
      @clear="clearFilter"
    >
      <BaseInputGroup :label="$tc('customers.customer', 1)">
        <BaseCustomerSelectInput
          v-model="filters.customer_id"
          :placeholder="$t('customers.type_or_click')"
          value-prop="id"
          label="name"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$t('invoices.status')">
        <BaseMultiselect
          v-model="filters.status"
          :groups="true"
          :options="invoiceStatus"
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

      <BaseInputGroup :label="$t('general.to')" class="mt-2">
        <BaseDatePicker
          v-model="filters.to_date"
          :calendar-button="true"
          calendar-button-icon="calendar"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$t('invoices.invoice_number')">
        <BaseInput v-model="filters.invoice_number">
          <template #left="slotProps">
            <BaseIcon name="HashtagIcon" :class="slotProps.class" />
          </template>
        </BaseInput>
      </BaseInputGroup>
    </BaseFilterWrapper>

    <BaseEmptyPlaceholder
      v-show="showEmptyScreen"
      :title="$t('invoices.no_invoices')"
      :description="$t('invoices.list_of_invoices')"
    >
      <MoonwalkerIcon class="mt-5 mb-4" />
      <template
        v-if="userStore.hasAbilities(abilities.CREATE_INVOICE)"
        #actions
      >
        <BaseButton
          variant="primary-outline"
          @click="$router.push('/admin/invoices/create')"
        >
          <template #left="slotProps">
            <BaseIcon name="PlusIcon" :class="slotProps.class" />
          </template>
          {{ $t('invoices.add_new_invoice') }}
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
          dark:border-gray-600
        "
      >
        <!-- Tabs -->
        <BaseTabGroup
          class="-mb-5"
          :selected-index="selectedIndex"
          @change="changeTabStatus"
        >
          <BaseTab
            :title="invoiceTabStatus[0].title"
            :tab-status="invoiceTabStatus[0].value"
          />
          <BaseTab
            :title="invoiceTabStatus[1].title"
            :tab-status="invoiceTabStatus[1].value"
          />
          <BaseTab
            :title="invoiceTabStatus[2].title"
            :tab-status="invoiceTabStatus[2].value"
          />
          <BaseTab
            :title="invoiceTabStatus[3].title"
            :tab-status="invoiceTabStatus[3].value"
          />
        </BaseTabGroup>

        <BaseDropdown
          v-if="
            invoiceStore.selectedInvoices.length &&
            userStore.hasAbilities(abilities.DELETE_INVOICE)
          "
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
              <BaseIcon name="ChevronDownIcon" />
            </span>
          </template>

          <BaseDropdownItem @click="removeMultipleInvoices">
            <BaseIcon name="TrashIcon" class="mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </div>

      <BaseTable
        ref="table"
        :data="fetchData"
        :columns="invoiceColumns"
        :placeholder-count="invoiceStore.invoiceTotalCount >= 20 ? 10 : 5"
        class="mt-10"
      >
        <!-- Select All Checkbox -->
        <template #header>
          <div class="absolute items-center left-6 top-2.5 select-none">
            <BaseCheckbox
              v-model="invoiceStore.selectAllField"
              variant="primary"
              @change="invoiceStore.selectAllInvoices"
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

        <template #cell-name="{ row }">
          <BaseText :text="row.data.customer.name" :length="30" />
        </template>

        <!-- Invoice Number  -->
        <template #cell-invoice_number="{ row }">
          <router-link
            :to="{ path: `invoices/${row.data.id}/view` }"
            class="font-medium text-primary-500 dark:text-primary-400"
          >
            {{ row.data.invoice_number }}
          </router-link>
        </template>

        <!-- Invoice date  -->
        <template #cell-invoice_date="{ row }">
          {{ row.data.formatted_invoice_date }}
        </template>

        <!-- Invoice Total  -->
        <template #cell-total="{ row }">
          <BaseFormatMoney
            :amount="row.data.total"
            :currency="row.data.customer.currency"
          />
        </template>

        <!-- Invoice status  -->
        <template #cell-status="{ row }">
          <BaseInvoiceStatusBadge :status="row.data.status" class="px-3 py-1">
            {{ row.data.status }}
          </BaseInvoiceStatusBadge>
        </template>

        <!-- Due Amount + Paid Status  -->
        <template #cell-due_amount="{ row }">
          <div class="flex justify-between">
            <BaseFormatMoney
              :amount="row.data.due_amount"
              :currency="row.data.currency"
            />

            <BasePaidStatusBadge
              v-if="row.data.overdue"
              status="OVERDUE"
              class="px-1 py-0.5 ml-2"
            >
              {{ $t('invoices.overdue') }}
            </BasePaidStatusBadge>

            <BasePaidStatusBadge
              :status="row.data.paid_status"
              class="px-1 py-0.5 ml-2"
            >
              {{ row.data.paid_status }}
            </BasePaidStatusBadge>
          </div>
        </template>

        <!-- Actions -->
        <template v-if="hasAtleastOneAbility()" #cell-actions="{ row }">
          <InvoiceDropdown :row="row.data" :table="table" />
        </template>
      </BaseTable>
    </div>
  </BasePage>
</template>

<script setup>
import { computed, onUnmounted, reactive, ref, watch, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useUserStore } from '@/scripts/admin/stores/user'
import abilities from '@/scripts/admin/stub/abilities'
import { debouncedWatch } from '@vueuse/core'

import MoonwalkerIcon from '@/scripts/components/icons/empty/MoonwalkerIcon.vue'
import InvoiceDropdown from '@/scripts/admin/components/dropdowns/InvoiceIndexDropdown.vue'
import SendInvoiceModal from '@/scripts/admin/components/modal-components/SendInvoiceModal.vue'
// Stores
const invoiceStore = useInvoiceStore()
const dialogStore = useDialogStore()
const notificationStore = useNotificationStore()

const { t } = useI18n()

// Local State
const utils = inject('$utils')
const table = ref(null)
const showFilters = ref(false)

const invoiceStatus = ref([
  {
    label: 'Status',
    options: ['DRAFT', 'SENT', 'VIEWED', 'COMPLETED'],
  },
  {
    label: 'Paid Status',
    options: ['UNPAID', 'PAID', 'PARTIALLY_PAID'],
  },
  ,
])

const invoiceTabStatus = {
  0: {
    title: t('general.all'),
    value: '',
  },
  1: {
    title: t('general.draft'),
    value: 'DRAFT',
  },
  2: {
    title: t('general.sent'),
    value: 'SENT',
  },
  3: {
    title: t('general.due'),
    value: 'DUE',
  },
}
const isRequestOngoing = ref(true)
const router = useRouter()
const userStore = useUserStore()
const selectedIndex = ref(0)

let filters = reactive({
  customer_id: '',
  status: '',
  from_date: '',
  to_date: '',
  invoice_number: '',
  tab_status: '',
})

const showEmptyScreen = computed(
  () => !invoiceStore.invoiceTotalCount && !isRequestOngoing.value
)

const selectField = computed({
  get: () => invoiceStore.selectedInvoices,
  set: (value) => {
    return invoiceStore.selectInvoice(value)
  },
})

const invoiceColumns = computed(() => {
  return [
    {
      key: 'checkbox',
      thClass: 'extra w-10',
      tdClass: 'font-medium text-gray-900',
      placeholderClass: 'w-10',
      sortable: false,
    },
    {
      key: 'invoice_date',
      label: t('invoices.date'),
      thClass: 'extra',
      tdClass: 'font-medium',
    },
    { key: 'invoice_number', label: t('invoices.number') },
    { key: 'name', label: t('invoices.customer') },
    { key: 'status', label: t('invoices.status') },
    {
      key: 'due_amount',
      label: t('dashboard.recent_invoices_card.amount_due'),
    },
    {
      key: 'total',
      label: t('invoices.total'),
      tdClass: 'font-medium text-gray-900',
    },

    {
      key: 'actions',
      label: t('invoices.action'),
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
  if (invoiceStore.selectAllField) {
    invoiceStore.selectAllInvoices()
  }
})

function hasAtleastOneAbility() {
  return userStore.hasAbilities([
    abilities.DELETE_INVOICE,
    abilities.EDIT_INVOICE,
    abilities.VIEW_INVOICE,
    abilities.SEND_INVOICE,
  ])
}

async function clearStatusSearch(removedOption, id) {
  filters.status = ''
  refreshTable()
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
    invoice_number: filters.invoice_number,
    tab_status: filters.tab_status,
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  isRequestOngoing.value = true

  let response = await invoiceStore.fetchInvoices(data)

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

function changeTabStatus(val, index) {
  filters.tab_status = val['tab-status']
  selectedIndex.value = index
}

function setFilters() {
  invoiceStore.$patch((state) => {
    state.selectedInvoices = []
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
}

async function removeMultipleInvoices() {
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
        await invoiceStore.deleteMultipleInvoices().then((res) => {
          if (res.data.success) {
            refreshTable()

            invoiceStore.$patch((state) => {
              state.selectedInvoices = []
              state.selectAllField = false
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

function setActiveTab(val) {
  switch (val) {
    case 'DRAFT':
      selectedIndex.value = 1
      break

    case 'SENT':
    case 'VIEWED':
    case 'COMPLETED':
    case 'PAID':
      selectedIndex.value = 2
      break

    case 'UNPAID':
    case 'PARTIALLY_PAID':
      selectedIndex.value = 3
      break
  }
  filters.tab_status = invoiceTabStatus[selectedIndex.value].value
}
</script>

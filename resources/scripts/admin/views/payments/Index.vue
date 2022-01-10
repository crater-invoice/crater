<template>
  <BasePage class="payments">
    <SendPaymentModal />
    <BasePageHeader :title="$t('payments.title')">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />
        <BaseBreadcrumbItem :title="$tc('payments.payment', 2)" to="#" active />
      </BaseBreadcrumb>

      <template #actions>
        <BaseButton
          v-show="paymentStore.paymentTotalCount"
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
            <BaseIcon v-else name="XIcon" :class="slotProps.class" />
          </template>
        </BaseButton>

        <BaseButton
          v-if="userStore.hasAbilities(abilities.CREATE_PAYMENT)"
          variant="primary"
          class="ml-4"
          @click="$router.push('/admin/payments/create')"
        >
          <template #left="slotProps">
            <BaseIcon name="PlusIcon" :class="slotProps.class" />
          </template>

          {{ $t('payments.add_payment') }}
        </BaseButton>
      </template>
    </BasePageHeader>

    <BaseFilterWrapper :show="showFilters" class="mt-3" @clear="clearFilter">
      <BaseInputGroup :label="$t('payments.customer')">
        <BaseCustomerSelectInput
          v-model="filters.customer_id"
          :placeholder="$t('customers.type_or_click')"
          value-prop="id"
          label="name"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$t('payments.payment_number')">
        <BaseInput v-model="filters.payment_number">
          <template #left="slotProps">
            <BaseIcon name="HashtagIcon" :class="slotProps.class" />
          </template>
        </BaseInput>
      </BaseInputGroup>

      <BaseInputGroup :label="$t('payments.payment_mode')">
        <BaseMultiselect
          v-model="filters.payment_mode"
          value-prop="id"
          track-by="name"
          :filter-results="false"
          label="name"
          resolve-on-load
          :delay="500"
          searchable
          :options="searchPayment"
        />
      </BaseInputGroup>
    </BaseFilterWrapper>

    <BaseEmptyPlaceholder
      v-if="showEmptyScreen"
      :title="$t('payments.no_payments')"
      :description="$t('payments.list_of_payments')"
    >
      <CapsuleIcon class="mt-5 mb-4" />

      <template
        v-if="userStore.hasAbilities(abilities.CREATE_PAYMENT)"
        #actions
      >
        <BaseButton
          variant="primary-outline"
          @click="$router.push('/admin/payments/create')"
        >
          <template #left="slotProps">
            <BaseIcon name="PlusIcon" :class="slotProps.class" />
          </template>
          {{ $t('payments.add_new_payment') }}
        </BaseButton>
      </template>
    </BaseEmptyPlaceholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <!-- Multiple Select Actions -->
      <div class="relative flex items-center justify-end h-5">
        <BaseDropdown v-if="paymentStore.selectedPayments.length">
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
          <BaseDropdownItem @click="removeMultiplePayments">
            <BaseIcon name="TrashIcon" class="mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </div>

      <BaseTable
        ref="tableComponent"
        :data="fetchData"
        :columns="paymentColumns"
        :placeholder-count="paymentStore.paymentTotalCount >= 20 ? 10 : 5"
        class="mt-3"
      >
        <!-- Select All Checkbox -->
        <template #header>
          <div class="absolute items-center left-6 top-2.5 select-none">
            <BaseCheckbox
              v-model="selectAllFieldStatus"
              variant="primary"
              @change="paymentStore.selectAllPayments"
            />
          </div>
        </template>

        <template #cell-status="{ row }">
          <div class="relative block">
            <BaseCheckbox
              :id="row.id"
              v-model="selectField"
              :value="row.data.id"
              variant="primary"
            />
          </div>
        </template>

        <template #cell-payment_date="{ row }">
          {{ row.data.formatted_payment_date }}
        </template>

        <template #cell-payment_number="{ row }">
          <router-link
            :to="{ path: `payments/${row.data.id}/view` }"
            class="font-medium text-primary-500"
          >
            {{ row.data.payment_number }}
          </router-link>
        </template>

        <template #cell-name="{ row }">
          <BaseText :text="row.data.customer.name" :length="30" tag="span" />
        </template>

        <template #cell-payment_mode="{ row }">
          <span>
            {{ row.data.payment_method ? row.data.payment_method.name : '-' }}
          </span>
        </template>

        <template #cell-invoice_number="{ row }">
          <span>
            {{
              row?.data?.invoice?.invoice_number
                ? row?.data?.invoice?.invoice_number
                : '-'
            }}
          </span>
        </template>

        <template #cell-amount="{ row }">
          <BaseFormatMoney
            :amount="row.data.amount"
            :currency="row.data.customer.currency"
          />
        </template>

        <template v-if="hasAtleastOneAbility()" #cell-actions="{ row }">
          <PaymentDropdown :row="row.data" :table="tableComponent" />
        </template>
      </BaseTable>
    </div>
  </BasePage>
</template>

<script setup>
import { debouncedWatch } from '@vueuse/core'

import { ref, reactive, computed, onUnmounted } from 'vue'

import { useI18n } from 'vue-i18n'
import { useDialogStore } from '@/scripts/stores/dialog'
import { usePaymentStore } from '@/scripts/admin/stores/payment'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useUserStore } from '@/scripts/admin/stores/user'
import abilities from '@/scripts/admin/stub/abilities'
import CapsuleIcon from '@/scripts/components/icons/empty/CapsuleIcon.vue'
import PaymentDropdown from '@/scripts/admin/components/dropdowns/PaymentIndexDropdown.vue'
import SendPaymentModal from '@/scripts/admin/components/modal-components/SendPaymentModal.vue'

const { t } = useI18n()
let showFilters = ref(false)
let isFetchingInitialData = ref(true)
let tableComponent = ref(null)

const filters = reactive({
  customer: '',
  payment_mode: '',
  payment_number: '',
})

const paymentStore = usePaymentStore()
const companyStore = useCompanyStore()
const dialogStore = useDialogStore()
const userStore = useUserStore()

const showEmptyScreen = computed(() => {
  return !paymentStore.paymentTotalCount && !isFetchingInitialData.value
})

const paymentColumns = computed(() => {
  return [
    {
      key: 'status',
      sortable: false,
      thClass: 'extra w-10',
      tdClass: 'text-left text-sm font-medium extra',
    },
    {
      key: 'payment_date',
      label: t('payments.date'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    { key: 'payment_number', label: t('payments.payment_number') },
    { key: 'name', label: t('payments.customer') },
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

const selectField = computed({
  get: () => paymentStore.selectedPayments,
  set: (value) => {
    return paymentStore.selectPayment(value)
  },
})

const selectAllFieldStatus = computed({
  get: () => paymentStore.selectAllField,
  set: (value) => {
    return paymentStore.setSelectAllState(value)
  },
})

debouncedWatch(
  filters,
  () => {
    setFilters()
  },
  { debounce: 500 }
)

onUnmounted(() => {
  if (paymentStore.selectAllField) {
    paymentStore.selectAllPayments()
  }
})

paymentStore.fetchPaymentModes({ limit: 'all' })

async function searchPayment(search) {
  let res = await paymentStore.fetchPaymentModes({ search })
  return res.data.data
}

function hasAtleastOneAbility() {
  return userStore.hasAbilities([
    abilities.DELETE_PAYMENT,
    abilities.EDIT_PAYMENT,
    abilities.VIEW_PAYMENT,
    abilities.SEND_PAYMENT,
  ])
}

async function fetchData({ page, filter, sort }) {
  let data = {
    customer_id: filters.customer_id,
    payment_method_id:
      filters.payment_mode !== null ? filters.payment_mode : '',
    payment_number: filters.payment_number,
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  isFetchingInitialData.value = true

  let response = await paymentStore.fetchPayments(data)

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
  tableComponent.value && tableComponent.value.refresh()
}

function setFilters() {
  refreshTable()
}

function clearFilter() {
  filters.customer_id = ''
  filters.payment_mode = ''
  filters.payment_number = ''
}

function toggleFilter() {
  if (showFilters.value) {
    clearFilter()
  }

  showFilters.value = !showFilters.value
}

function removeMultiplePayments() {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('payments.confirm_delete', 2),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then((res) => {
      if (res) {
        paymentStore.deleteMultiplePayments().then((response) => {
          if (response.data.success) {
            refreshTable()
          }
        })
      }
    })
}
</script>

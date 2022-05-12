<script setup>
import { useI18n } from 'vue-i18n'
import { computed, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import { debounce } from 'lodash'

import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'

import LoadingIcon from '@/scripts/components/icons/LoadingIcon.vue'

const recurringInvoiceStore = useRecurringInvoiceStore()

const { t } = useI18n()
const route = useRoute()
const isLoading = ref(false)

const invoiceList = ref(null)
const currentPageNumber = ref(1)
const lastPageNumber = ref(1)
const invoiceListSection = ref(null)

const searchData = reactive({
  orderBy: null,
  orderByField: null,
  searchText: null,
})

const getOrderBy = computed(() => {
  if (searchData.orderBy === 'asc' || searchData.orderBy == null) {
    return true
  }
  return false
})

function hasActiveUrl(id) {
  return route.params.id == id
}

async function loadRecurringInvoices(pageNumber, fromScrollListener = false) {
  if (isLoading.value) {
    return
  }

  let params = {}
  if (
    searchData.searchText !== '' &&
    searchData.searchText !== null &&
    searchData.searchText !== undefined
  ) {
    params.search = searchData.searchText
  }

  if (searchData.orderBy !== null && searchData.orderBy !== undefined) {
    params.orderBy = searchData.orderBy
  }

  if (
    searchData.orderByField !== null &&
    searchData.orderByField !== undefined
  ) {
    params.orderByField = searchData.orderByField
  }

  isLoading.value = true
  let response = await recurringInvoiceStore.fetchRecurringInvoices({
    page: pageNumber,
    ...params,
  })
  isLoading.value = false

  invoiceList.value = invoiceList.value ? invoiceList.value : []
  invoiceList.value = [...invoiceList.value, ...response.data.data]

  currentPageNumber.value = pageNumber ? pageNumber : 1
  lastPageNumber.value = response.data.meta.last_page
  let invoiceFound = invoiceList.value.find((inv) => inv.id == route.params.id)

  if (
    fromScrollListener == false &&
    !invoiceFound &&
    currentPageNumber.value < lastPageNumber.value &&
    Object.keys(params).length === 0
  ) {
    loadRecurringInvoices(++currentPageNumber.value)
  }

  if (invoiceFound) {
    setTimeout(() => {
      if (fromScrollListener == false) {
        scrollToRecurringInvoice()
      }
    }, 500)
  }
}

function scrollToRecurringInvoice() {
  const el = document.getElementById(`recurring-invoice-${route.params.id}`)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
    el.classList.add('shake')
    addScrollListener()
  }
}

function addScrollListener() {
  invoiceListSection.value.addEventListener('scroll', (ev) => {
    if (
      ev.target.scrollTop > 0 &&
      ev.target.scrollTop + ev.target.clientHeight >
        ev.target.scrollHeight - 200
    ) {
      if (currentPageNumber.value < lastPageNumber.value) {
        loadRecurringInvoices(++currentPageNumber.value, true)
      }
    }
  })
}

async function onSearched() {
  invoiceList.value = []
  loadRecurringInvoices()
}

function sortData() {
  if (searchData.orderBy === 'asc') {
    searchData.orderBy = 'desc'
    onSearched()
    return true
  }
  searchData.orderBy = 'asc'
  onSearched()
  return true
}

loadRecurringInvoices()
onSearched = debounce(onSearched, 500)
</script>

<template>
  <!-- sidebar -->
  <div
    class="
      fixed
      top-0
      left-0
      hidden
      h-full
      pt-16
      pb-[6.4rem]
      ml-56
      bg-white
      xl:ml-64
      w-88
      xl:block
    "
  >
    <div
      class="
        flex
        items-center
        justify-between
        px-4
        pt-8
        pb-2
        border border-gray-200 border-solid
        height-full
      "
    >
      <div class="mb-6">
        <BaseInput
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          type="text"
          variant="gray"
          @input="onSearched()"
        >
          <template #right>
            <BaseIcon name="SearchIcon" class="h-5 text-gray-400" />
          </template>
        </BaseInput>
      </div>

      <div class="flex mb-6 ml-3" role="group" aria-label="First group">
        <BaseDropdown class="ml-3" position="bottom-start">
          <template #activator>
            <BaseButton size="md" variant="gray">
              <BaseIcon name="FilterIcon" class="h-5" />
            </BaseButton>
          </template>
          <div
            class="
              px-2
              py-1
              pb-2
              mb-1 mb-2
              text-sm
              border-b border-gray-200 border-solid
            "
          >
            {{ $t('general.sort_by') }}
          </div>

          <BaseDropdownItem class="flex px-1 py-2 cursor-pointer">
            <BaseInputGroup class="-mt-3 font-normal">
              <BaseRadio
                id="filter_next_invoice_date"
                v-model="searchData.orderByField"
                :label="$t('recurring_invoices.next_invoice_date')"
                size="sm"
                name="filter"
                value="next_invoice_at"
                @update:modelValue="onSearched"
              />
            </BaseInputGroup>
          </BaseDropdownItem>

          <BaseDropdownItem class="flex px-1 py-2 cursor-pointer">
            <BaseInputGroup class="-mt-3 font-normal">
              <BaseRadio
                id="filter_start_date"
                v-model="searchData.orderByField"
                :label="$t('recurring_invoices.starts_at')"
                value="starts_at"
                size="sm"
                name="filter"
                @update:modelValue="onSearched"
              />
            </BaseInputGroup>
          </BaseDropdownItem>
        </BaseDropdown>

        <BaseButton class="ml-1" size="md" variant="gray" @click="sortData">
          <BaseIcon v-if="getOrderBy" name="SortAscendingIcon" class="h-5" />
          <BaseIcon v-else name="SortDescendingIcon" class="h-5" />
        </BaseButton>
      </div>
    </div>

    <div
      ref="invoiceListSection"
      class="
        h-full
        overflow-y-scroll
        border-l border-gray-200 border-solid
        base-scroll
      "
    >
      <div v-for="(invoice, index) in invoiceList" :key="index">
        <router-link
          v-if="invoice"
          :id="'recurring-invoice-' + invoice.id"
          :to="`/admin/recurring-invoices/${invoice.id}/view`"
          :class="[
            'flex justify-between side-invoice p-4 cursor-pointer hover:bg-gray-100 items-center border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid':
                hasActiveUrl(invoice.id),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <BaseText
              :text="invoice.customer.name"
              :length="30"
              class="
                pr-2
                mb-2
                text-sm
                not-italic
                font-normal
                leading-5
                text-black
                capitalize
                truncate
              "
            />

            <div
              class="
                mt-1
                mb-2
                text-xs
                not-italic
                font-medium
                leading-5
                text-gray-600
              "
            >
              {{ invoice.invoice_number }}
            </div>
            <BaseRecurringInvoiceStatusBadge
              :status="invoice.status"
              class="px-1 text-xs"
            >
              {{ invoice.status }}
            </BaseRecurringInvoiceStatusBadge>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <BaseFormatMoney
              class="
                block
                mb-2
                text-xl
                not-italic
                font-semibold
                leading-8
                text-right text-gray-900
              "
              :amount="invoice.total"
              :currency="invoice.customer.currency"
            />

            <div
              class="
                text-sm
                not-italic
                font-normal
                leading-5
                text-right text-gray-600
                est-date
              "
            >
              {{ invoice.formatted_starts_at }}
            </div>
          </div>
        </router-link>
      </div>
      <div v-if="isLoading" class="flex justify-center p-4 items-center">
        <LoadingIcon class="h-6 m-1 animate-spin text-primary-400" />
      </div>
      <p
        v-if="!invoiceList?.length && !isLoading"
        class="flex justify-center px-4 mt-5 text-sm text-gray-600"
      >
        {{ $t('invoices.no_matching_invoices') }}
      </p>
    </div>
  </div>
</template>

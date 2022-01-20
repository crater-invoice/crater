<script setup>
import { useI18n } from 'vue-i18n'
import { computed, reactive, ref, watch, inject } from 'vue'
import InvoiceDropdown from '@/scripts/admin/components/dropdowns/InvoiceIndexDropdown.vue'
import { useRoute, useRouter } from 'vue-router'
import { debounce } from 'lodash'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import { useModalStore } from '@/scripts/stores/modal'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useDialogStore } from '@/scripts/stores/dialog'
import SendInvoiceModal from '@/scripts/admin/components/modal-components/SendInvoiceModal.vue'
import LoadingIcon from '@/scripts/components/icons/LoadingIcon.vue'
import abilities from '@/scripts/admin/stub/abilities'

const modalStore = useModalStore()
const invoiceStore = useInvoiceStore()
const notificationStore = useNotificationStore()
const userStore = useUserStore()
const dialogStore = useDialogStore()

const { t } = useI18n()
const utils = inject('$utils')
const id = ref(null)
const count = ref(null)
const invoiceData = ref(null)
const currency = ref(null)
const route = useRoute()
const router = useRouter()
const status = ref([
  'DRAFT',
  'SENT',
  'VIEWED',
  'EXPIRED',
  'ACCEPTED',
  'REJECTED',
])
const isMarkAsSent = ref(false)
const isSendingEmail = ref(false)
const isRequestOnGoing = ref(false)
const isSearching = ref(false)
const isLoading = ref(false)

const searchData = reactive({
  orderBy: null,
  orderByField: null,
  searchText: null,
})

const pageTitle = computed(() => invoiceData.value.invoice_number)

const getOrderBy = computed(() => {
  if (searchData.orderBy === 'asc' || searchData.orderBy == null) {
    return true
  }
  return false
})

const getOrderName = computed(() => {
  if (getOrderBy.value) {
    return t('general.ascending')
  }
  return t('general.descending')
})

const shareableLink = computed(() => {
  return `/invoices/pdf/${invoiceData.value.unique_hash}`
})

const getCurrentInvoiceId = computed(() => {
  if (invoiceData.value && invoiceData.value.id) {
    return invoice.value.id
  }
  return null
})

watch(route, (to, from) => {
  if (to.name === 'invoices.view') {
    loadInvoice()
  }
})

async function onMarkAsSent() {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('invoices.invoice_mark_as_sent'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'primary',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (response) => {
      isMarkAsSent.value = false
      if (response) {
        await invoiceStore.markAsSent({
          id: invoiceData.value.id,
          status: 'SENT',
        })
        invoiceData.value.status = 'SENT'
        isMarkAsSent.value = true
      }
      isMarkAsSent.value = false
    })
}

async function onSendInvoice(id) {
  modalStore.openModal({
    title: t('invoices.send_invoice'),
    componentName: 'SendInvoiceModal',
    id: invoiceData.value.id,
    data: invoiceData.value,
  })
}

function hasActiveUrl(id) {
  return route.params.id == id
}

async function loadInvoices() {
  isLoading.value = true
  await invoiceStore.fetchInvoices()
  isLoading.value = false

  setTimeout(() => {
    scrollToInvoice()
  }, 500)
}

function scrollToInvoice() {
  const el = document.getElementById(`invoice-${route.params.id}`)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
    el.classList.add('shake')
  }
}

async function loadInvoice() {
  let response = await invoiceStore.fetchInvoice(route.params.id)
  if (response.data) {
    invoiceData.value = { ...response.data.data }
  }
}

async function onSearched() {
  let data = ''
  if (
    searchData.searchText !== '' &&
    searchData.searchText !== null &&
    searchData.searchText !== undefined
  ) {
    data += `search=${searchData.searchText}&`
  }

  if (searchData.orderBy !== null && searchData.orderBy !== undefined) {
    data += `orderBy=${searchData.orderBy}&`
  }
  if (
    searchData.orderByField !== null &&
    searchData.orderByField !== undefined
  ) {
    data += `orderByField=${searchData.orderByField}`
  }
  isSearching.value = true
  let response = await invoiceStore.searchInvoice(data)
  isSearching.value = false
  if (response.data) {
    invoiceStore.invoices = response.data.data
  }
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

loadInvoices()
loadInvoice()
onSearched = debounce(onSearched, 500)
</script>

<template>
  <SendInvoiceModal />

  <BasePage v-if="invoiceData" class="xl:pl-96 xl:ml-8">
    <BasePageHeader :title="pageTitle">
      <template #actions>
        <div class="text-sm mr-3">
          <BaseButton
            v-if="
              invoiceData.status === 'DRAFT' &&
              userStore.hasAbilities(abilities.EDIT_INVOICE)
            "
            :disabled="isMarkAsSent"
            variant="primary-outline"
            @click="onMarkAsSent"
          >
            {{ $t('invoices.mark_as_sent') }}
          </BaseButton>
        </div>

        <BaseButton
          v-if="
            invoiceData.status === 'DRAFT' &&
            userStore.hasAbilities(abilities.SEND_INVOICE)
          "
          :disabled="isSendingEmail"
          variant="primary"
          class="text-sm"
          @click="onSendInvoice"
        >
          {{ $t('invoices.send_invoice') }}
        </BaseButton>

        <!-- Record Payment  -->
        <router-link
          v-if="userStore.hasAbilities(abilities.CREATE_PAYMENT)"
          :to="`/admin/payments/${$route.params.id}/create`"
        >
          <BaseButton
            v-if="
              invoiceData.status === 'SENT' ||
              invoiceData.status === 'OVERDUE' ||
              invoiceData.status === 'VIEWED'
            "
            variant="primary"
          >
            {{ $t('invoices.record_payment') }}
          </BaseButton>
        </router-link>

        <!-- Invoice Dropdown  -->
        <InvoiceDropdown
          class="ml-3"
          :row="invoiceData"
          :load-data="loadInvoices"
        />
      </template>
    </BasePageHeader>

    <!-- sidebar -->
    <div
      class="
        fixed
        top-0
        left-0
        hidden
        h-full
        pt-16
        pb-4
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
                <BaseIcon name="FilterIcon" />
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
                  id="filter_invoice_date"
                  v-model="searchData.orderByField"
                  :label="$t('reports.invoices.invoice_date')"
                  size="sm"
                  name="filter"
                  value="invoice_date"
                  @update:modelValue="onSearched"
                />
              </BaseInputGroup>
            </BaseDropdownItem>

            <BaseDropdownItem class="flex px-1 py-2 cursor-pointer">
              <BaseInputGroup class="-mt-3 font-normal">
                <BaseRadio
                  id="filter_due_date"
                  v-model="searchData.orderByField"
                  :label="$t('invoices.due_date')"
                  value="due_date"
                  size="sm"
                  name="filter"
                  @update:modelValue="onSearched"
                />
              </BaseInputGroup>
            </BaseDropdownItem>

            <BaseDropdownItem class="flex px-1 py-2 cursor-pointer">
              <BaseInputGroup class="-mt-3 font-normal">
                <BaseRadio
                  id="filter_invoice_number"
                  v-model="searchData.orderByField"
                  :label="$t('invoices.invoice_number')"
                  value="invoice_number"
                  size="sm"
                  name="filter"
                  @update:modelValue="onSearched"
                />
              </BaseInputGroup>
            </BaseDropdownItem>
          </BaseDropdown>

          <BaseButton class="ml-1" size="md" variant="gray" @click="sortData">
            <BaseIcon v-if="getOrderBy" name="SortAscendingIcon" />
            <BaseIcon v-else name="SortDescendingIcon" />
          </BaseButton>
        </div>
      </div>

      <div
        v-if="invoiceStore && invoiceStore.invoices"
        class="
          h-full
          pb-32
          overflow-y-scroll
          border-l border-gray-200 border-solid
          base-scroll
        "
      >
        <div v-for="(invoice, index) in invoiceStore.invoices" :key="index">
          <router-link
            v-if="invoice && !isLoading"
            :id="'invoice-' + invoice.id"
            :to="`/admin/invoices/${invoice.id}/view`"
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
              <BaseEstimateStatusBadge
                :status="invoice.status"
                class="px-1 text-xs"
              >
                {{ invoice.status }}
              </BaseEstimateStatusBadge>
            </div>

            <div class="flex-1 whitespace-nowrap right">
              <BaseFormatMoney
                class="
                  mb-2
                  text-xl
                  not-italic
                  font-semibold
                  leading-8
                  text-right text-gray-900
                  block
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
                {{ invoice.formatted_invoice_date }}
              </div>
            </div>
          </router-link>
        </div>
        <div class="flex justify-center p-4 items-center">
          <LoadingIcon
            v-if="isLoading"
            class="h-6 m-1 animate-spin text-primary-400"
          />
        </div>
        <p
          v-if="!invoiceStore.invoices.length && !isLoading"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('invoices.no_matching_invoices') }}
        </p>
      </div>
    </div>

    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden"
      style="height: 75vh"
    >
      <iframe
        :src="`${shareableLink}`"
        class="
          flex-1
          border border-gray-400 border-solid
          bg-white
          rounded-md
          frame-style
        "
      />
    </div>
  </BasePage>
</template>

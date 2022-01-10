<template>
  <SendPaymentModal />
  <BasePage class="xl:pl-96">
    <BasePageHeader :title="pageTitle">
      <template #actions>
        <BaseButton
          v-if="userStore.hasAbilities(abilities.SEND_PAYMENT)"
          :disabled="isSendingEmail"
          :content-loading="isFetching"
          variant="primary"
          @click="onPaymentSend"
        >
          {{ $t('payments.send_payment_receipt') }}
        </BaseButton>

        <PaymentDropdown
          :content-loading="isFetching"
          class="ml-3"
          :row="payment"
        />
      </template>
    </BasePageHeader>

    <!-- Sidebar -->
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
          pb-6
          border border-gray-200 border-solid
        "
      >
        <BaseInput
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          type="text"
          @input="onSearch"
        >
          <BaseIcon name="SearchIcon" class="h-5" />
        </BaseInput>

        <div class="flex ml-3" role="group" aria-label="First group">
          <BaseDropdown
            position="bottom-start"
            width-class="w-50"
            position-class="left-0"
          >
            <template #activator>
              <BaseButton variant="gray">
                <BaseIcon name="FilterIcon" />
              </BaseButton>
            </template>

            <div
              class="
                px-4
                py-1
                pb-2
                mb-2
                text-sm
                border-b border-gray-200 border-solid
              "
            >
              {{ $t('general.sort_by') }}
            </div>

            <div class="px-2">
              <BaseDropdownItem class="pt-3 rounded-md hover:rounded-md">
                <BaseInputGroup class="-mt-3 font-normal">
                  <BaseRadio
                    id="filter_invoice_number"
                    v-model="searchData.orderByField"
                    :label="$t('invoices.title')"
                    size="sm"
                    name="filter"
                    value="invoice_number"
                    @update:modelValue="onSearch"
                  />
                </BaseInputGroup>
              </BaseDropdownItem>
            </div>

            <div class="px-2">
              <BaseDropdownItem class="pt-3 rounded-md hover:rounded-md">
                <BaseInputGroup class="-mt-3 font-normal">
                  <BaseRadio
                    v-model="searchData.orderByField"
                    :label="$t('payments.date')"
                    size="sm"
                    name="filter"
                    value="payment_date"
                    @update:modelValue="onSearch"
                  />
                </BaseInputGroup>
              </BaseDropdownItem>
            </div>

            <div class="px-2">
              <BaseDropdownItem class="pt-3 rounded-md hover:rounded-md">
                <BaseInputGroup class="-mt-3 font-normal">
                  <BaseRadio
                    id="filter_payment_number"
                    v-model="searchData.orderByField"
                    :label="$t('payments.payment_number')"
                    size="sm"
                    name="filter"
                    value="payment_number"
                    @update:modelValue="onSearch"
                  />
                </BaseInputGroup>
              </BaseDropdownItem>
            </div>
          </BaseDropdown>

          <BaseButton class="ml-1" size="md" variant="gray" @click="sortData">
            <BaseIcon v-if="getOrderBy" name="SortAscendingIcon" />
            <BaseIcon v-else name="SortDescendingIcon" />
          </BaseButton>
        </div>
      </div>

      <div
        v-if="paymentStore && paymentStore.payments"
        class="
          h-full
          pb-32
          overflow-y-scroll
          border-l border-gray-200 border-solid
        "
      >
        <div v-for="(payment, index) in paymentStore.payments" :key="index">
          <router-link
            v-if="payment && !isLoading"
            :id="'payment-' + payment.id"
            :to="`/admin/payments/${payment.id}/view`"
            :class="[
              'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100 border-l-4 border-transparent',
              {
                'bg-gray-100 border-l-4 border-primary-500 border-solid':
                  hasActiveUrl(payment.id),
              },
            ]"
            style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
          >
            <div class="flex-2">
              <BaseText
                :text="payment?.customer?.name"
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
                  mb-1
                  text-xs
                  not-italic
                  font-medium
                  leading-5
                  text-gray-500
                  capitalize
                "
              >
                {{ payment?.payment_number }}
              </div>

              <div
                class="
                  mb-1
                  text-xs
                  not-italic
                  font-medium
                  leading-5
                  text-gray-500
                  capitalize
                "
              >
                {{ payment?.invoice_number }}
              </div>
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
                :amount="payment?.amount"
                :currency="payment.customer?.currency"
              />

              <div class="text-sm text-right text-gray-500 non-italic">
                {{ payment.formatted_payment_date }}
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
          v-if="!paymentStore?.payments?.length && !isLoading"
          class="flex justify-center px-4 mt-5 text-sm text-gray-600"
        >
          {{ $t('payments.no_matching_payments') }}
        </p>
      </div>
    </div>

    <!-- pdf -->
    <div
      class="flex flex-col min-h-0 mt-8 overflow-hidden"
      style="height: 75vh"
    >
      <iframe
        v-if="shareableLink"
        :src="shareableLink"
        class="flex-1 border border-gray-400 border-solid rounded-md"
      />
    </div>
  </BasePage>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { debounce } from 'lodash'
import { ref, reactive, computed, inject, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useDialogStore } from '@/scripts/stores/dialog'
import { usePaymentStore } from '@/scripts/admin/stores/payment'
import { useModalStore } from '@/scripts/stores/modal'
import PaymentDropdown from '@/scripts/admin/components/dropdowns/PaymentIndexDropdown.vue'
import moment from 'moment'
import { useUserStore } from '@/scripts/admin/stores/user'
import SendPaymentModal from '@/scripts/admin/components/modal-components/SendPaymentModal.vue'
import abilities from '@/scripts/admin/stub/abilities'
import LoadingIcon from '@/scripts/components/icons/LoadingIcon.vue'

const route = useRoute()
const router = useRouter()

const { t } = useI18n()
let id = ref(null)
let count = ref(null)
let payment = reactive({})
let currency = ref(null)
let searchData = reactive({
  orderBy: null,
  orderByField: null,
  searchText: null,
})

let isSearching = ref(false)
let isSendingEmail = ref(false)
let isMarkingAsSent = ref(false)
let isLoading = ref(false)
let isFetching = ref(false)

const $utils = inject('utils')

const paymentStore = usePaymentStore()
const modalStore = useModalStore()
const userStore = useUserStore()

const pageTitle = computed(() => {
  return payment.payment_number || ''
})

const getOrderBy = computed(() => {
  if (searchData.orderBy === 'asc' || searchData.orderBy == null) {
    return true
  }
  return false
})

const getOrderName = computed(() =>
  getOrderBy.value ? t('general.ascending') : t('general.descending')
)

const shareableLink = computed(() => {
  return payment.unique_hash ? `/payments/pdf/${payment.unique_hash}` : false
})

const paymentDate = computed(() => {
  return moment(paymentStore?.selectedPayment?.payment_date).format(
    'YYYY/MM/DD'
  )
})

watch(route, () => {
  loadPayment()
})

loadPayments()
loadPayment()

onSearch = debounce(onSearch, 500)

function hasActiveUrl(id) {
  return route.params.id == id
}

function hasAbilities() {
  return userStore.hasAbilities([
    abilities.DELETE_PAYMENT,
    abilities.EDIT_PAYMENT,
    abilities.VIEW_PAYMENT,
  ])
}

const dialogStore = useDialogStore()

async function loadPayments() {
  isLoading.value = true
  await paymentStore.fetchPayments({ limit: 'all' })
  isLoading.value = false

  setTimeout(() => {
    scrollToPayment()
  }, 500)
}

async function loadPayment() {
  if (!route.params.id) return

  isFetching.value = true

  let response = await paymentStore.fetchPayment(route.params.id)

  if (response.data) {
    isFetching.value = false
    Object.assign(payment, response.data.data)
  }
}

function scrollToPayment() {
  const el = document.getElementById(`payment-${route.params.id}`)

  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
    el.classList.add('shake')
  }
}

async function onSearch() {
  let data = {}

  if (
    searchData.searchText !== '' &&
    searchData.searchText !== null &&
    searchData.searchText !== undefined
  ) {
    data.search = searchData.searchText
  }

  if (searchData.orderBy !== null && searchData.orderBy !== undefined) {
    data.orderBy = searchData.orderBy
  }

  if (
    searchData.orderByField !== null &&
    searchData.orderByField !== undefined
  ) {
    data.orderByField = searchData.orderByField
  }

  isSearching.value = true
  try {
    let response = await paymentStore.searchPayment(data)
    isSearching.value = false

    if (response.data.data) {
      paymentStore.payments = response.data.data
    }
  } catch (error) {
    isSearching.value = false
  }
}

function sortData() {
  if (searchData.orderBy === 'asc') {
    searchData.orderBy = 'desc'
    onSearch()
    return true
  }
  searchData.orderBy = 'asc'
  onSearch()
  return true
}

async function onPaymentSend() {
  modalStore.openModal({
    title: t('payments.send_payment'),
    componentName: 'SendPaymentModal',
    id: payment.id,
    data: payment,
    variant: 'lg',
  })
}
</script>

<template>
  <base-page v-if="payment" class="xl:pl-96">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <sw-button
          :disabled="isSendingEmail"
          variant="primary"
          @click="onPaymentSend"
        >
          {{ $t('payments.send_payment_receipt') }}
        </sw-button>
        <sw-dropdown class="ml-3">
          <sw-button slot="activator" variant="primary" class="h-10">
            <dots-horizontal-icon class="h-5" />
          </sw-button>

          <sw-dropdown-item @click="copyPdfUrl">
            <link-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.copy_pdf_url') }}
          </sw-dropdown-item>

          <sw-dropdown-item
            :to="`/admin/payments/${$route.params.id}/edit`"
            tag-name="router-link"
          >
            <pencil-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.edit') }}
          </sw-dropdown-item>

          <sw-dropdown-item @click="removePayment($route.params.id)">
            <trash-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </template>
    </sw-page-header>

    <!-- sidebar -->
    <div
      class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-88 xl:block"
    >
      <div
        class="flex items-center justify-between px-4 pt-8 pb-2 border border-gray-200 border-solid height-full"
      >
        <sw-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          type="text"
          class="mb-6"
          variant="gray"
          @input="onSearch"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>

        <div class="flex mb-6 ml-3" role="group" aria-label="First group">
          <sw-dropdown position="bottom-start">
            <sw-button slot="activator" size="md" variant="gray-light">
              <filter-icon class="h-5" />
            </sw-button>

            <div
              class="px-2 pb-2 mb-1 text-sm border-b border-gray-200 border-solid"
            >
              {{ $t('general.sort_by') }}
            </div>

            <sw-dropdown-item class="flex cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_invoice_number"
                  :label="$t('invoices.title')"
                  v-model="searchData.orderByField"
                  size="sm"
                  name="filter"
                  value="invoice_number"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_payment_date"
                  :label="$t('payments.date')"
                  v-model="searchData.orderByField"
                  size="sm"
                  name="filter"
                  value="payment_date"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex cursor-pointer">
              <sw-input-group class="-mt-3 font-normal">
                <sw-radio
                  id="filter_payment_number"
                  :label="$t('payments.payment_number')"
                  v-model="searchData.orderByField"
                  size="sm"
                  name="filter"
                  value="payment_number"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>
          </sw-dropdown>

          <sw-button
            v-tooltip.top-center="{ content: getOrderName }"
            class="ml-1"
            size="md"
            variant="gray-light"
            @click="sortData"
          >
            <sort-ascending-icon v-if="getOrderBy" class="h-5" />
            <sort-descending-icon v-else class="h-5" />
          </sw-button>
        </div>
      </div>

      <base-loader v-if="isSearching" :show-bg-overlay="true" />

      <div
        v-else
        class="h-full pb-32 overflow-y-scroll border-l border-gray-200 border-solid sw-scroll"
      >
        <router-link
          v-for="(payment, index) in payments"
          :to="`/admin/payments/${payment.id}/view`"
          :id="'payment-' + payment.id"
          :key="index"
          :class="[
            'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100 border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid': hasActiveUrl(
                payment.id
              ),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
              {{ payment.user.name }}
            </div>

            <div
              class="mb-1 text-xs not-italic font-medium leading-5 text-gray-500 capitalize"
            >
              {{ payment.payment_number }}
            </div>

            <div
              class="mb-1 text-xs not-italic font-medium leading-5 text-gray-500 capitalize"
            >
              {{ payment.invoice_number }}
            </div>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="$utils.formatMoney(payment.amount, payment.user.currency)"
            />

            <div class="text-sm text-right text-gray-500 non-italic">
              {{ payment.formattedPaymentDate }}
            </div>
          </div>
        </router-link>

        <p
          v-if="!payments.length"
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
        :src="`${shareableLink}`"
        class="flex-1 border border-gray-400 border-solid rounded-md"
      />
    </div>
  </base-page>
</template>
<script>
import {
  DotsHorizontalIcon,
  FilterIcon,
  SortAscendingIcon,
  SortDescendingIcon,
  SearchIcon,
  LinkIcon,
  TrashIcon,
  PencilIcon,
} from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
const _ = require('lodash')
export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    TrashIcon,
    PencilIcon,
    LinkIcon,
  },
  data() {
    return {
      id: null,
      count: null,
      payments: [],
      payment: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      isRequestOnGoing: false,
      isSearching: false,
      isSendingEmail: false,
      isMarkingAsSent: false,
    }
  },
  computed: {
    pageTitle() {
      return this.payment.payment_number
    },
    getOrderBy() {
      if (
        this.searchData.orderBy === 'asc' ||
        this.searchData.orderBy == null
      ) {
        return true
      }
      return false
    },
    getOrderName() {
      if (this.getOrderBy) {
        return this.$t('general.ascending')
      }
      return this.$t('general.descending')
    },
    shareableLink() {
      return `/payments/pdf/${this.payment.unique_hash}`
    },
  },

  watch: {
    $route(to, from) {
      this.loadPayment()
    },
  },

  created() {
    this.loadPayments()
    this.loadPayment()
    this.onSearch = _.debounce(this.onSearch, 500)
  },

  methods: {
    ...mapActions('payment', [
      'fetchPayments',
      'fetchPayment',
      'sendEmail',
      'deletePayment',
      'searchPayment',
    ]),
    ...mapActions('modal', ['openModal']),
    ...mapActions('notification', ['showNotification']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadPayments() {
      let response = await this.fetchPayments({ limit: 'all' })
      if (response.data) {
        this.payments = response.data.payments.data
      }
      setTimeout(() => {
        this.scrollToPayment()
      }, 500)
    },
    scrollToPayment() {
      const el = document.getElementById(`payment-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadPayment() {
      let response = await this.fetchPayment(this.$route.params.id)

      if (response.data) {
        this.payment = response.data.payment
      }
    },
    async onSearch() {
      let data = ''
      if (
        this.searchData.searchText !== '' &&
        this.searchData.searchText !== null &&
        this.searchData.searchText !== undefined
      ) {
        data += `search=${this.searchData.searchText}&`
      }

      if (
        this.searchData.orderBy !== null &&
        this.searchData.orderBy !== undefined
      ) {
        data += `orderBy=${this.searchData.orderBy}&`
      }

      if (
        this.searchData.orderByField !== null &&
        this.searchData.orderByField !== undefined
      ) {
        data += `orderByField=${this.searchData.orderByField}`
      }
      this.isSearching = true
      let response = await this.searchPayment(data)
      this.isSearching = false
      if (response.data) {
        this.payments = response.data.payments.data
      }
    },
    sortData() {
      if (this.searchData.orderBy === 'asc') {
        this.searchData.orderBy = 'desc'
        this.onSearch()
        return true
      }
      this.searchData.orderBy = 'asc'
      this.onSearch()
      return true
    },
    async onPaymentSend() {
      this.openModal({
        title: this.$t('payments.send_payment'),
        componentName: 'SendPaymentModal',
        id: this.payment.id,
        data: this.payment,
        variant: 'lg',
      })
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/payments/pdf/${this.payment.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)
      this.showNotification({
        type: 'success',
        message: this.$t('general.copied_pdf_url_clipboard'),
      })
    },
    async removePayment(id) {
      this.id = id
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: 'you will not be able to recover this payment!',
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let request = await this.deletePayment({ ids: [id] })
          if (request.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('payments.deleted_message', 1),
            })
            this.$router.push('/admin/payments')
          } else if (request.data.error) {
            this.showNotification({
              type: 'error',
              message: request.data.message,
            })
          }
        }
      })
    },
  },
}
</script>

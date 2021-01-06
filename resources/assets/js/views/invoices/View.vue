<template>
  <base-page v-if="invoice" class="xl:pl-96">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <div class="mr-3 text-sm">
          <sw-button
            v-if="invoice.status === 'DRAFT'"
            :disabled="isMarkingAsSent"
            variant="primary-outline"
            @click="onMarkAsSent"
          >
            {{ $t('invoices.mark_as_sent') }}
          </sw-button>
        </div>
        <sw-button
          v-if="invoice.status === 'DRAFT'"
          :disabled="isSendingEmail"
          variant="primary"
          class="text-sm"
          @click="onSendInvoice"
        >
          {{ $t('invoices.send_invoice') }}
        </sw-button>
        <sw-button
          v-if="
            invoice.status === 'SENT' ||
            invoice.status === 'OVERDUE' ||
            invoice.status === 'VIEWED'
          "
          :to="`/admin/payments/${$route.params.id}/create`"
          tag-name="router-link"
          variant="primary"
          class="text-sm"
        >
          {{ $t('payments.record_payment') }}
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
            :to="`/admin/invoices/${$route.params.id}/edit`"
            tag-name="router-link"
          >
            <pencil-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.edit') }}
          </sw-dropdown-item>

          <sw-dropdown-item @click="removeInvoice($route.params.id)">
            <trash-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </template>
    </sw-page-header>

    <!-- sidebar -->
    <div
      class="fixed top-0 left-0 hidden h-full pt-16 pb-5 ml-56 bg-white xl:ml-64 w-88 xl:block"
    >
      <div
        class="flex items-center justify-between px-4 pt-8 pb-2 border border-gray-200 border-solid height-full"
      >
        <sw-input
          v-model="searchData.searchText"
          :placeholder="$t('general.search')"
          class="mb-6"
          type="text"
          variant="gray"
          @input="onSearch"
        >
          <search-icon slot="rightIcon" class="h-5" />
        </sw-input>

        <div class="flex mb-6 ml-3" role="group" aria-label="First group">
          <sw-dropdown
            :close-on-select="false"
            align="left"
            position="bottom-start"
          >
            <sw-button slot="activator" size="md" variant="gray-light">
              <filter-icon class="h-5" />
            </sw-button>

            <div class="px-2 py-1 mb-2 border-b border-gray-200 border-solid">
              {{ $t('general.sort_by') }}
            </div>

            <sw-dropdown-item class="flex px-1 py-1 cursor-pointer">
              <sw-input-group class="-mt-2 text-sm font-normal">
                <sw-radio
                  id="filter_invoice_date"
                  v-model="searchData.orderByField"
                  :label="$t('invoices.invoice_date')"
                  name="filter"
                  size="sm"
                  value="invoice_date"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-1 cursor-pointer">
              <sw-input-group class="-mt-2 font-normal">
                <sw-radio
                  id="filter_due_date"
                  :label="$t('invoices.due_date')"
                  v-model="searchData.orderByField"
                  name="filter"
                  size="sm"
                  value="due_date"
                  @change="onSearch"
                />
              </sw-input-group>
            </sw-dropdown-item>

            <sw-dropdown-item class="flex px-1 py-1 cursor-pointer">
              <sw-input-group class="-mt-2 font-normal">
                <sw-radio
                  id="filter_invoice_number"
                  v-model="searchData.orderByField"
                  :label="$t('invoices.invoice_number')"
                  size="sm"
                  type="radio"
                  name="filter"
                  value="invoice_number"
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
          v-for="(invoice, index) in invoices"
          :to="`/admin/invoices/${invoice.id}/view`"
          :id="'invoice-' + invoice.id"
          :key="index"
          :class="[
            'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100  border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid': hasActiveUrl(
                invoice.id
              ),
            },
          ]"
          style="border-bottom: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div class="flex-2">
            <div
              class="pr-2 mb-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
            >
              {{ invoice.user.name }}
            </div>

            <div
              class="mt-1 mb-2 text-xs not-italic font-medium leading-5 text-gray-600"
            >
              {{ invoice.invoice_number }}
            </div>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(invoice.status).bgColor"
              :color="$utils.getBadgeStatusColor(invoice.status).color"
              :font-size="$utils.getBadgeStatusColor(invoice.status).fontSize"
              class="px-1 text-xs"
            >
              {{ invoice.status }}
            </sw-badge>
          </div>

          <div class="flex-1 whitespace-nowrap right">
            <div
              class="mb-2 text-xl not-italic font-semibold leading-8 text-right text-gray-900"
              v-html="
                $utils.formatMoney(invoice.due_amount, invoice.user.currency)
              "
            />
            <div
              class="text-sm not-italic font-normal leading-5 text-right text-gray-600"
            >
              {{ invoice.formattedInvoiceDate }}
            </div>
          </div>
        </router-link>

        <p
          v-if="!invoices.length"
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
        class="flex-1 border border-gray-400 border-solid rounded-md frame-style"
      />
    </div>
  </base-page>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
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

const _ = require('lodash')
export default {
  components: {
    DotsHorizontalIcon,
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
    LinkIcon,
    PencilIcon,
    TrashIcon,
  },
  data() {
    return {
      id: null,
      count: null,
      invoices: [],
      invoice: null,
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
      return this.invoice.invoice_number
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
      return `/invoices/pdf/${this.invoice.unique_hash}`
    },
    getCurrentInvoiceId() {
      if (this.invoice && this.invoice.id) {
        return this.invoice.id
      }
      return null
    },
  },
  watch: {
    $route(to, from) {
      this.loadInvoice()
    },
  },
  created() {
    this.loadInvoices()
    this.loadInvoice()
    this.onSearch = _.debounce(this.onSearch, 500)
  },
  methods: {
    ...mapActions('invoice', [
      'fetchInvoices',
      'getRecord',
      'searchInvoice',
      'markAsSent',
      'sendEmail',
      'deleteInvoice',
      'selectInvoice',
      'fetchInvoice',
    ]),

    ...mapActions('modal', ['openModal']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadInvoices() {
      let response = await this.fetchInvoices({ limit: 'all' })
      if (response.data) {
        this.invoices = response.data.invoices.data
      }
      setTimeout(() => {
        this.scrollToInvoice()
      }, 500)
    },
    scrollToInvoice() {
      const el = document.getElementById(`invoice-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },
    async loadInvoice() {
      let response = await this.fetchInvoice(this.$route.params.id)

      if (response.data) {
        this.invoice = response.data.invoice
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
      let response = await this.searchInvoice(data)
      this.isSearching = false
      if (response.data) {
        this.invoices = response.data.invoices.data
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
    async onMarkAsSent() {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: this.$t('invoices.invoice_mark_as_sent'),
          icon: '/assets/icon/check-circle-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            this.isMarkingAsSent = true
            let response = await this.markAsSent({
              id: this.invoice.id,
              status: 'SENT',
            })
            this.isMarkingAsSent = false
            if (response.data) {
              this.invoice.status = 'SENT'
              window.toastr['success'](
                this.$tc('invoices.marked_as_sent_message')
              )
            }
          }
        })
    },
    async onSendInvoice() {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: this.invoice.id,
        data: this.invoice,
      })
    },
    copyPdfUrl() {
      let pdfUrl = `${window.location.origin}/invoices/pdf/${this.invoice.unique_hash}`

      let response = this.$utils.copyTextToClipboard(pdfUrl)

      window.toastr['success'](this.$t('general.copied_pdf_url_clipboard'))
    },
    async removeInvoice(id) {
      window
        .swal({
          title: this.$t('general.are_you_sure'),
          text: 'you will not be able to recover this invoice!',
          icon: '/assets/icon/trash-solid.svg',
          buttons: true,
          dangerMode: true,
        })
        .then(async (value) => {
          if (value) {
            let request = await this.deleteInvoice({ ids: [id] })
            if (request.data.success) {
              window.toastr['success'](this.$tc('invoices.deleted_message', 1))
              this.$router.push('/admin/invoices')
            } else if (request.data.error) {
              window.toastr['error'](request.data.message)
            }
          }
        })
    },
  },
}
</script>

<template>
  <base-page>
    <sw-page-header :title="$t('invoices.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
        <sw-breadcrumb-item :title="$tc('invoices.invoice', 2)" to="#" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalInvoices"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="/admin/invoices/create"
          class="ml-4"
          size="lg"
          variant="primary"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('invoices.new_invoice') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper
        v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >
        <sw-input-group :label="$tc('customers.customer', 1)" class="mt-2">
          <base-customer-select
            ref="customerSelect"
            @select="onSelectCustomer"
            @deselect="clearCustomerSearch"
          />
        </sw-input-group>

        <sw-input-group :label="$t('invoices.status')" class="mt-2 xl:mx-8">
          <sw-select
            v-model="filters.status"
            :options="status"
            :group-select="false"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('general.select_a_status')"
            :allow-empty="false"
            group-values="options"
            group-label="label"
            track-by="name"
            label="name"
            @remove="clearStatusSearch()"
            @select="setActiveTab"
          />
        </sw-input-group>

        <sw-input-group :label="$t('general.from')" class="mt-2">
          <base-date-picker
            v-model="filters.from_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <div
          class="hidden w-8 h-0 mx-4 border border-gray-400 border-solid xl:block"
          style="margin-top: 3.5rem"
        />

        <sw-input-group :label="$t('general.to')" class="mt-2">
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('invoices.invoice_number')"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.invoice_number">
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('invoices.no_invoices')"
      :description="$t('invoices.list_of_invoices')"
    >
      <moon-walker-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/invoices/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('invoices.new_invoice') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative">
      <div class="relative mt-5">
        <p class="absolute right-0 m-0 text-sm" style="top: 50px">
          {{ $t('general.showing') }}: <b>{{ invoices.length }}</b>

          {{ $t('general.of') }} <b>{{ totalInvoices }}</b>
        </p>

        <sw-tabs :active-tab="activeTab" @update="setStatusFilter">
          <sw-tab-item :title="$t('general.due')" filter="DUE" />
          <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />
          <sw-tab-item :title="$t('general.all')" filter="" />
        </sw-tabs>

        <sw-transition type="fade">
          <sw-dropdown
            v-if="selectedInvoices.length"
            class="absolute float-right"
            style="margin-top: -35px"
          >
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleInvoices">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div
        v-show="invoices && invoices.length"
        class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12"
      >
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllInvoices"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllInvoices"
        />
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="relative block">
            <sw-checkbox
              :id="row.id"
              v-model="selectField"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.date')"
          sort-as="invoice_date"
          show="formattedInvoiceDate"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.number')"
          show="invoice_number"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.number') }}</span>
            <router-link
              :to="{ path: `invoices/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.invoice_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.customer')"
          width="20%"
          show="name"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.status')"
          sort-as="status"
        >
          <template slot-scope="row">
            <span> {{ $t('invoices.status') }}</span>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
            >
              {{ $utils.getStatusTranslation(row.status.replace('_', ' ')) }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.paid_status')"
          sort-as="paid_status"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.paid_status') }}</span>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
            >
              {{
                $utils.getStatusTranslation(row.paid_status.replace('_', ' '))
              }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.amount_due')"
          sort-as="due_amount"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.amount_due') }}</span>

            <div
              v-html="$utils.formatMoney(row.due_amount, row.user.currency)"
            />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.action') }}</span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`invoices/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`invoices/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status == 'DRAFT'"
                @click="sendInvoice(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.send_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status === 'SENT' || row.status === 'VIEWED'"
                @click="sendInvoice(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.resend_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status == 'DRAFT'"
                @click="markInvoiceAsSent(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.mark_as_sent') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="
                  row.status === 'SENT' ||
                  row.status === 'VIEWED' ||
                  row.status === 'OVERDUE'
                "
                :to="`/admin/payments/${row.id}/create`"
                tag-name="router-link"
              >
                <credit-card-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('payments.record_payment') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="onCloneInvoice(row.id)">
                <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.clone_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeInvoice(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import MoonWalkerIcon from '@/components/icon/MoonwalkerIcon'
import moment from 'moment'

import {
  PencilIcon,
  DocumentDuplicateIcon,
  CreditCardIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PlusIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  XCircleIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    MoonWalkerIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    DotsHorizontalIcon,
    PencilIcon,
    DocumentDuplicateIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    CreditCardIcon,
    HashtagIcon,
  },

  data() {
    return {
      showFilters: false,
      currency: null,

      status: [
        {
          label: 'Status',
          isDisable: true,
          options: [
            { name: 'DRAFT', value: 'DRAFT' },
            { name: 'DUE', value: 'DUE' },
            { name: 'SENT', value: 'SENT' },
            { name: 'VIEWED', value: 'VIEWED' },
            { name: 'OVERDUE', value: 'OVERDUE' },
            { name: 'COMPLETED', value: 'COMPLETED' },
          ],
        },
        {
          label: 'Paid Status',
          options: [
            { name: 'UNPAID', value: 'UNPAID' },
            { name: 'PAID', value: 'PAID' },
            { name: 'PARTIALLY PAID', value: 'PARTIALLY_PAID' },
          ],
        },
      ],

      isRequestOngoing: true,
      activeTab: this.$t('general.due'),
      filters: {
        customer: '',
        status: { name: 'DUE', value: 'DUE' },
        from_date: '',
        to_date: '',
        invoice_number: '',
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalInvoices && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('invoice', [
      'selectedInvoices',
      'totalInvoices',
      'invoices',
      'selectAllField',
    ]),

    selectField: {
      get: function () {
        return this.selectedInvoices
      },
      set: function (val) {
        this.selectInvoice(val)
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    },
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllInvoices()
    }
  },

  methods: {
    ...mapActions('invoice', [
      'fetchInvoices',
      'getRecord',
      'selectInvoice',
      'resetSelectedInvoices',
      'selectAllInvoices',
      'deleteInvoice',
      'deleteMultipleInvoices',
      'sendEmail',
      'markAsSent',
      'setSelectAllState',
      'cloneInvoice',
    ]),
    ...mapActions('customer', ['fetchCustomers']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('notification', ['showNotification']),

    async sendInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: invoice.id,
        data: invoice,
        variant: 'lg',
      })
    },

    async markInvoiceAsSent(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            class="w-6 h-6"
            focusable="false"
            data-prefix="fas"
            data-icon="check-circle"
            class="svg-inline--fa fa-check-circle fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#55547A"
              d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.mark_as_sent_successfully'),
            })
          }
        }
      })
    },

    async onCloneInvoice(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_clone'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            class="w-6 h-6"
            focusable="false"
            data-prefix="fas"
            data-icon="check-circle"
            class="svg-inline--fa fa-check-circle fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#55547A"
              d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let response = await this.cloneInvoice({ id })

          this.refreshTable()

          if (response.data) {
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.cloned_successfully'),
            })
            this.$router.push(
              `/admin/invoices/${response.data.invoice.id}/edit`
            )
          }
        }
      })
    },

    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.due'):
          this.filters.status = {
            name: 'DUE',
            value: 'DUE',
          }
          break

        case this.$t('general.draft'):
          this.filters.status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break

        default:
          this.filters.status = {
            name: '',
            value: '',
          }
          break
      }
      // this.refreshTable()
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        status: this.filters.status.value,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        invoice_number: this.filters.invoice_number,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchInvoices(data)
      this.isRequestOngoing = false
      this.currency = response.data.currency

      return {
        data: response.data.invoices.data,
        pagination: {
          totalPages: response.data.invoices.last_page,
          currentPage: page,
          count: response.data.invoices.count,
        },
      }
    },

    setFilters() {
      this.resetSelectedInvoices()
      this.refreshTable()
    },

    clearFilter() {
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }

      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        invoice_number: '',
      }

      this.activeTab = this.$t('general.all')
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    onSelectCustomer(customer) {
      this.filters.customer = customer
    },

    async removeInvoice(id) {
      this.id = id
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.deleteInvoice({ ids: [id] })

          if (res.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.deleted_message'),
            })
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'payment_attached') {
            this.showNotification({
              type: 'error',
              message:
                (this.$t('invoices.payment_attached_message'),
                this.$t('general.action_failed')),
            })
            return true
          }

          this.showNotification({
            type: 'error',
            message: res.data.error,
          })

          return true
        }
        this.resetSelectedInvoices()
      })
    },

    async removeMultipleInvoices() {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete', 2),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.deleteMultipleInvoices()

          if (res.data.error === 'payment_attached') {
            this.showNotification({
              type: 'error',
              message:
                (this.$t('invoices.payment_attached_message'),
                this.$t('general.action_failed')),
            })
            return true
          }

          if (res.data) {
            this.$refs.table.refresh()
            this.resetSelectedInvoices()
            this.showNotification({
              type: 'success',
              message: this.$tc('invoices.deleted_message', 2),
            })
          } else if (res.data.error) {
            this.showNotification({
              type: 'error',
              message: res.data.message,
            })
          }
        }
      })
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },

    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },
    setActiveTab(val) {
      switch (val.value) {
        case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          break
        case 'DUE':
          this.activeTab = this.$t('general.due')
          break
        default:
          this.activeTab = this.$t('general.all')
          break
      }
    },
  },
}
</script>

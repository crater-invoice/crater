<template>
  <base-page>
    <sw-page-header :title="$t('estimates.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />

        <sw-breadcrumb-item
          :title="$tc('estimates.estimate', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalEstimates"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="estimates/create"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('estimates.new_estimate') }}
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

        <sw-input-group :label="$t('estimates.status')" class="mt-2 xl:mx-8">
          <sw-select
            v-model="filters.status"
            :options="status"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_a_status')"
            @select="setActiveTab"
            @remove="clearStatusSearch()"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('general.from')"
          color="black-light"
          class="mt-2"
        >
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

        <sw-input-group
          :label="$t('general.to')"
          color="black-light"
          class="mt-2"
        >
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('estimates.estimate_number')"
          color="black-light"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.estimate_number">
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
      :title="$t('estimates.no_estimates')"
      :description="$t('estimates.list_of_estimates')"
    >
      <moon-walker-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/estimates/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('estimates.add_new_estimate') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative">
      <div class="relative mt-5">
        <p class="absolute right-0 m-0 text-sm" style="top: 50px">
          {{ $t('general.showing') }}: <b>{{ estimates.length }}</b>
          {{ $t('general.of') }} <b>{{ totalEstimates }}</b>
        </p>

        <!-- Tabs -->
        <sw-tabs
          :active-tab="activeTab"
          class="mb-10"
          @update="setStatusFilter"
        >
          <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />
          <sw-tab-item :title="$t('general.sent')" filter="SENT" />
          <sw-tab-item :title="$t('general.all')" filter="" />
        </sw-tabs>

        <sw-transition type="fade">
          <sw-dropdown
            v-if="selectedEstimates.length"
            class="absolute float-right"
            style="margin-top: -70px"
          >
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleEstimates">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div
        v-show="estimates && estimates.length"
        class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-6"
      >
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllEstimates"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllEstimates"
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
          <div slot-scope="row" class="flex items-center">
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
          :label="$t('estimates.date')"
          sort-as="estimate_date"
          show="formattedEstimateDate"
        />

        <sw-table-column
          :sortable="true"
          :label="$tc('estimates.estimate', 1)"
          show="estimate_number"
        >
          <template slot-scope="row">
            <span>{{ $tc('estimates.estimate', 1) }}</span>
            <router-link
              :to="{ path: `estimates/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.estimate_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('estimates.customer')"
          sort-as="name"
          show="name"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('estimates.status')"
          show="status"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.status') }}</span>
            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
              class="px-3 py-1"
            >
              {{ $utils.getStatusTranslation(row.status) }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.total')"
          sort-as="total"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.total') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.action') }} </span>
            <sw-dropdown container-class="w-56">
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`estimates/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeEstimate(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`estimates/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="convertInToinvoice(row.id)">
                <document-text-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.convert_to_invoice') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status !== 'SENT'"
                @click="onMarkAsSent(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_sent') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status !== 'SENT'"
                @click="sendEstimate(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.send_estimate') }}
              </sw-dropdown-item>

              <!-- resend estimte -->
              <sw-dropdown-item
                v-if="row.status == 'SENT' || row.status == 'VIEWED'"
                @click="sendEstimate(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.resend_estimate') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status !== 'ACCEPTED'"
                @click="onMarkAsAccepted(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_accepted') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.status !== 'REJECTED'"
                @click="onMarkAsRejected(row.id)"
              >
                <x-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_rejected') }}
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
import moment from 'moment'

import MoonWalkerIcon from '@/components/icon/MoonwalkerIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  XCircleIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  PencilIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    MoonWalkerIcon,
    DotsHorizontalIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    HashtagIcon,
  },

  data() {
    return {
      showFilters: false,
      currency: null,
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      isRequestOngoing: true,
      activeTab: this.$t('general.draft'),

      filters: {
        customer: '',
        status: 'DRAFT',
        from_date: '',
        to_date: '',
        estimate_number: '',
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalEstimates && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('estimate', [
      'selectedEstimates',
      'totalEstimates',
      'estimates',
      'selectAllField',
    ]),

    selectField: {
      get: function () {
        return this.selectedEstimates
      },
      set: function (val) {
        this.selectEstimate(val)
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
      this.selectAllEstimates()
    }
  },

  methods: {
    ...mapActions('estimate', [
      'fetchEstimates',
      'resetSelectedEstimates',
      'getRecord',
      'selectEstimate',
      'selectAllEstimates',
      'deleteEstimate',
      'deleteMultipleEstimates',
      'markAsSent',
      'convertToInvoice',
      'setSelectAllState',
      'markAsAccepted',
      'markAsRejected',
      'sendEmail',
    ]),

    ...mapActions('modal', ['openModal']),

    ...mapActions('notification', ['showNotification']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id:
          this.filters.customer === ''
            ? this.filters.customer
            : this.filters.customer.id,
        status: this.filters.status,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        estimate_number: this.filters.estimate_number,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchEstimates(data)

      this.isRequestOngoing = false

      this.currency = response.data.currency

      return {
        data: response.data.estimates.data,
        pagination: {
          totalPages: response.data.estimates.last_page,
          currentPage: page,
          count: response.data.estimates.count,
        },
      }
    },

    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.draft'):
          this.filters.status = 'DRAFT'
          break
        case this.$t('general.sent'):
          this.filters.status = 'SENT'
          break
        default:
          this.filters.status = ''
          break
      }
    },

    async onMarkAsAccepted(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
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
            status: 'ACCEPTED',
          }

          let response = await this.markAsAccepted(data)

          if (response.data) {
            this.$refs.table.refresh()
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.marked_as_accepted_message'),
            })
          }
        }
      })
    },

    async onMarkAsRejected(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
        icon: 'error',
        iconHtml: `<svg
            aria-hidden="true"
            focusable="false"
            class="w-6 h-6"
            data-prefix="fas"
            data-icon="times-circle"
            class="svg-inline--fa fa-times-circle fa-w-16"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
          >
            <path
              fill="#DC2626"
              d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          const data = {
            id: id,
            status: 'REJECTED',
          }

          let response = await this.markAsRejected(data)

          if (response.data) {
            this.$refs.table.refresh()
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.marked_as_rejected_message'),
            })
          }
        }
      })
    },

    setFilters() {
      this.resetSelectedEstimates()
      this.$refs.table.refresh()
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
        estimate_number: '',
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

    async removeEstimate(id) {
      this.id = id
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.deleteEstimate({ ids: [this.id] })

          if (res.data.success) {
            this.$refs.table.refresh()
            this.resetSelectedEstimates()
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.deleted_message', 1),
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

    async convertInToinvoice(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            viewBox="0 0 384 512"
            class="w-6 h-6"
            data-prefix="fas"
            data-icon="file-alt"
            class="svg-inline--fa fa-file-alt fa-w-12"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill="#55547A"
              d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.convertToInvoice(id)

          if (res.data) {
            this.showNotification({
              type: 'success',
              message: this.$t('estimates.conversion_message'),
            })
            this.$router.push(`invoices/${res.data.invoice.id}/edit`)
          } else if (res.data.error) {
            this.showNotification({
              type: 'error',
              message: res.data.message,
            })
          }
        }
      })
    },

    async removeMultipleEstimates() {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 2),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let res = await this.deleteMultipleEstimates()

          if (res.data.success) {
            this.$refs.table.refresh()
            this.resetSelectedEstimates()
            this.showNotification({
              type: 'success',
              message: this.$tc('estimates.deleted_message', 2),
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

    async onMarkAsSent(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
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
              message: this.$tc('estimates.mark_as_sent_successfully'),
            })
          }
        }
      })
    },
    async sendEstimate(estimate) {
      this.openModal({
        title: this.$t('estimates.send_estimate'),
        componentName: 'SendEstimateModal',
        id: estimate.id,
        data: estimate,
        variant: 'lg',
      })
    },
    setActiveTab(val) {
      switch (val) {
        case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          break
        case 'SENT':
          this.activeTab = this.$t('general.sent')
          break
        default:
          this.activeTab = this.$t('general.all')
          break
      }
    },
  },
}
</script>

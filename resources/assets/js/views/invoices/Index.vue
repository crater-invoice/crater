<template>
  <div class="invoice-index-page invoices main-content">
    <div class="page-header">
      <h3 class="page-title"> {{ $t('invoices.title') }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link
            slot="item-title"
            to="dashboard">
            {{ $t('general.home') }}
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link
            slot="item-title"
            to="#">
            {{ $tc('invoices.invoice', 2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2 mr-4">
          <base-button
            v-show="totalInvoices || filtersApplied"
            :outline="true"
            :icon="filterIcon"
            size="large"
            color="theme"
            right-icon
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
          </base-button>
        </div>
        <router-link slot="item-title" class="col-xs-2" to="/admin/invoices/create">
          <base-button size="large" icon="plus" color="theme">
            {{ $t('invoices.new_invoice') }}
          </base-button>
        </router-link>
      </div>
    </div>

    <transition name="fade">
      <div v-show="showFilters" class="filter-section">
        <div class="filter-container">
          <div class="filter-customer">
            <label>{{ $tc('customers.customer',1) }} </label>
            <base-customer-select
              ref="customerSelect"
              @select="onSelectCustomer"
              @deselect="clearCustomerSearch"
            />
          </div>
          <div class="filter-status">
            <label>{{ $t('invoices.status') }}</label>
            <base-select
              v-model="filters.status"
              :options="status"
              :group-select="false"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('general.select_a_status')"
              group-values="options"
              group-label="label"
              track-by="name"
              label="name"
              @remove="clearStatusSearch()"
            />
          </div>
          <div class="filter-date">
            <div class="from pr-3">
              <label>{{ $t('general.from') }}</label>
              <base-date-picker
                v-model="filters.from_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
              />
            </div>
            <div class="dashed" />
            <div class="to pl-3">
              <label>{{ $t('general.to') }}</label>
              <base-date-picker
                v-model="filters.to_date"
                :calendar-button="true"
                calendar-button-icon="calendar"
              />
            </div>
          </div>
          <div class="filter-invoice">
            <label>{{ $t('invoices.invoice_number') }}</label>
            <base-input
              v-model="filters.invoice_number"
              icon="hashtag"/>
          </div>
        </div>
        <label class="clear-filter" @click="clearFilter">{{ $t('general.clear_all') }}</label>
      </div>
    </transition>

    <div v-cloak v-show="showEmptyScreen" class="col-xs-1 no-data-info" align="center">
      <moon-walker-icon class="mt-5 mb-4"/>
      <div class="row" align="center">
        <label class="col title">{{ $t('invoices.no_invoices') }}</label>
      </div>
      <div class="row">
        <label class="description col mt-1" align="center">{{ $t('invoices.list_of_invoices') }}</label>
      </div>
      <div class="btn-container">
        <base-button
          :outline="true"
          color="theme"
          class="mt-3"
          size="large"
          @click="$router.push('invoices/create')"
        >
          {{ $t('invoices.new_invoice') }}
        </base-button>
      </div>
    </div>

    <div v-show="!showEmptyScreen" class="table-container">
      <div class="table-actions mt-5">
        <p class="table-stats">{{ $t('general.showing') }}: <b>{{ invoices.length }}</b> {{ $t('general.of') }} <b>{{ totalInvoices }}</b></p>

        <!-- Tabs -->
        <ul class="tabs">
          <li class="tab" @click="getStatus('UNPAID')">
            <a :class="['tab-link', {'a-active': filters.status.value === 'UNPAID'}]" href="#" >{{ $t('general.due') }}</a>
          </li>
          <li class="tab" @click="getStatus('DRAFT')">
            <a :class="['tab-link', {'a-active': filters.status.value === 'DRAFT'}]" href="#">{{ $t('general.draft') }}</a>
          </li>
          <li class="tab" @click="getStatus('')">
            <a :class="['tab-link', {'a-active': filters.status.value === '' || filters.status.value === null || filters.status.value !== 'DRAFT' && filters.status.value !== 'UNPAID'}]" href="#">{{ $t('general.all') }}</a>
          </li>
        </ul>
        <transition name="fade">
          <v-dropdown v-if="selectedInvoices.length" :show-arrow="false">
            <span slot="activator" href="#" class="table-actions-button dropdown-toggle">
              {{ $t('general.actions') }}
            </span>
            <v-dropdown-item>
              <div class="dropdown-item" @click="removeMultipleInvoices">
                <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                {{ $t('general.delete') }}
              </div>
            </v-dropdown-item>
          </v-dropdown>
        </transition>
      </div>
      <div class="custom-control custom-checkbox">
        <input
          id="select-all"
          v-model="selectAllFieldStatus"
          type="checkbox"
          class="custom-control-input"
          @change="selectAllInvoices"
        >
        <label v-show="!isRequestOngoing" for="select-all" class="custom-control-label selectall">
          <span class="select-all-label">{{ $t('general.select_all') }} </span>
        </label>
      </div>

      <table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <template slot-scope="row">
            <div class="custom-control custom-checkbox">
              <input
                :id="row.id"
                v-model="selectField"
                :value="row.id"
                type="checkbox"
                class="custom-control-input"
              >
              <label :for="row.id" class="custom-control-label"/>
            </div>
          </template>
        </table-column>
        <table-column
          :label="$t('invoices.date')"
          sort-as="invoice_date"
          show="formattedInvoiceDate"
        />
        <table-column
          :label="$t('invoices.customer')"
          width="20%"
          show="name"
        />
        <table-column
          :label="$t('invoices.status')"
          sort-as="status"
        >
          <template slot-scope="row" >
            <span> {{ $t('invoices.status') }}</span>
            <span :class="'inv-status-'+row.status.toLowerCase()">{{ (row.status != 'PARTIALLY_PAID')? row.status : row.status.replace('_', ' ') }}</span>
          </template>
        </table-column>
        <table-column
          :label="$t('invoices.paid_status')"
          sort-as="paid_status"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.paid_status') }}</span>
            <span :class="'inv-status-'+row.paid_status.toLowerCase()">{{ (row.paid_status != 'PARTIALLY_PAID')? row.paid_status : row.paid_status.replace('_', ' ') }}</span>
          </template>
        </table-column>
        <table-column
          :label="$t('invoices.number')"
          show="invoice_number"
        />
        <table-column
          :label="$t('invoices.amount_due')"
          sort-as="due_amount"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.amount_due') }}</span>
            <div v-html="$utils.formatMoney(row.due_amount, row.user.currency)"/>
          </template>
        </table-column>
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.action') }}</span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>
                <router-link :to="{path: `invoices/${row.id}/edit`}" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon"/>
                  {{ $t('general.edit') }}
                </router-link>
                <router-link :to="{path: `invoices/${row.id}/view`}" class="dropdown-item">
                  <font-awesome-icon icon="eye" class="dropdown-item-icon" />
                  {{ $t('invoices.view') }}
                </router-link>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status == 'DRAFT'">
                <a class="dropdown-item" href="#/" @click="sendInvoice(row.id)" >
                  <font-awesome-icon icon="paper-plane" class="dropdown-item-icon" />
                  {{ $t('invoices.send_invoice') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status == 'DRAFT'">
                <a class="dropdown-item" href="#/" @click="markInvoiceAsSent(row.id)">
                  <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                  {{ $t('invoices.mark_as_sent') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status === 'SENT' || row.status === 'VIEWED' || row.status === 'OVERDUE'">
                <router-link :to="`/admin/payments/${row.id}/create`" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'credit-card']" class="dropdown-item-icon"/>
                  {{ $t('payments.record_payment') }}
                </router-link>
              </v-dropdown-item>
              <v-dropdown-item>
                <a class="dropdown-item" href="#/" @click="onCloneInvoice(row.id)">
                  <font-awesome-icon icon="copy" class="dropdown-item-icon" />
                  {{ $t('invoices.clone_invoice') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeInvoice(row.id)">
                  <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                  {{ $t('general.delete') }}
                </div>
              </v-dropdown-item>
            </v-dropdown>
          </template>
        </table-column>
      </table-component>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import MoonWalkerIcon from '../../../js/components/icon/MoonwalkerIcon'
import moment from 'moment'

export default {
  components: {
    'moon-walker-icon': MoonWalkerIcon
  },
  data () {
    return {
      showFilters: false,
      currency: null,
      status: [
        {
          label: 'Status',
          isDisable: true,
          options: [
            { name: 'DRAFT', value: 'DRAFT' },
            { name: 'DUE', value: 'UNPAID' },
            { name: 'SENT', value: 'SENT' },
            { name: 'VIEWED', value: 'VIEWED' },
            { name: 'OVERDUE', value: 'OVERDUE' },
            { name: 'COMPLETED', value: 'COMPLETED' }
          ]
        },
        {
          label: 'Paid Status',
          options: [
            { name: 'UNPAID', value: 'UNPAID' },
            { name: 'PAID', value: 'PAID' },
            { name: 'PARTIALLY PAID', value: 'PARTIALLY_PAID' }
          ]
        }
      ],
      filtersApplied: false,
      isRequestOngoing: true,
      filters: {
        customer: '',
        status: { name: 'DUE', value: 'UNPAID' },
        from_date: '',
        to_date: '',
        invoice_number: ''
      }
    }
  },

  computed: {
    showEmptyScreen () {
      return !this.totalInvoices && !this.isRequestOngoing && !this.filtersApplied
    },
    filterIcon () {
      return (this.showFilters) ? 'times' : 'filter'
    },
    ...mapGetters('customer', [
      'customers'
    ]),
    ...mapGetters('invoice', [
      'selectedInvoices',
      'totalInvoices',
      'invoices',
      'selectAllField'
    ]),
    selectField: {
      get: function () {
        return this.selectedInvoices
      },
      set: function (val) {
        this.selectInvoice(val)
      }
    },
    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      }
    }
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true
    }
  },
  created () {
    this.fetchCustomers()
  },
  destroyed () {
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
      'cloneInvoice'
    ]),
    ...mapActions('customer', [
      'fetchCustomers'
    ]),
    async sendInvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_send'),
        icon: '/assets/icon/paper-plane-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id
          }
          let response = await this.sendEmail(data)
          this.refreshTable()
          if (response.data.success) {
            window.toastr['success'](this.$tc('invoices.send_invoice_successfully'))
            return true
          }
          if (response.data.error === 'user_email_does_not_exist') {
            window.toastr['error'](this.$tc('invoices.user_email_does_not_exist'))
            return false
          }
          window.toastr['error'](this.$tc('invoices.something_went_wrong'))
        }
      })
    },
    async markInvoiceAsSent (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id
          }
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](this.$tc('invoices.mark_as_sent_successfully'))
          }
        }
      })
    },
    async onCloneInvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_clone'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id
          }
          let response = await this.cloneInvoice(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](this.$tc('invoices.cloned_successfully'))
            this.$router.push(`/admin/invoices/${response.data.invoice.id}/edit`)
          }
        }
      })
    },
    getStatus (val) {
      this.filters.status = {
        name: val,
        value: val
      }
    },
    refreshTable () {
      this.$refs.table.refresh()
    },
    async fetchData ({ page, filter, sort }) {
      let data = {
        customer_id: this.filters.customer === '' ? this.filters.customer : this.filters.customer.id,
        status: this.filters.status.value,
        from_date: this.filters.from_date === '' ? this.filters.from_date : moment(this.filters.from_date).format('DD/MM/YYYY'),
        to_date: this.filters.to_date === '' ? this.filters.to_date : moment(this.filters.to_date).format('DD/MM/YYYY'),
        invoice_number: this.filters.invoice_number,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page
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
          count: response.data.invoices.count
        }
      }
    },
    setFilters () {
      this.filtersApplied = true
      this.resetSelectedInvoices()
      this.refreshTable()
    },
    clearFilter () {
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(this.filters.customer)
      }
      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        invoice_number: ''
      }

      this.$nextTick(() => {
        this.filtersApplied = false
      })
    },
    toggleFilter () {
      if (this.showFilters && this.filtersApplied) {
        this.clearFilter()
        this.refreshTable()
      }

      this.showFilters = !this.showFilters
    },
    onSelectCustomer (customer) {
      this.filters.customer = customer
    },
    async removeInvoice (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice(this.id)

          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](this.$t('invoices.payment_attached_message'), this.$t('general.action_failed'))
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }

        this.$refs.table.refresh()
        this.filtersApplied = false
        this.resetSelectedInvoices()
      })
    },
    async removeMultipleInvoices () {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteMultipleInvoices()
          if (res.data.error === 'payment_attached') {
            window.toastr['error'](this.$t('invoices.payment_attached_message'), this.$t('general.action_failed'))
            return true
          }
          if (res.data) {
            this.$refs.table.refresh()
            this.filtersApplied = false
            this.resetSelectedInvoices()
            window.toastr['success'](this.$tc('invoices.deleted_message', 2))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async clearCustomerSearch (removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },
    async clearStatusSearch (removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    }
  }
}
</script>

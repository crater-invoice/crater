<template>
  <div class="estimate-index-page estimates main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $t('estimates.title') }}</h3>
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
            {{ $tc('estimates.estimate', 2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2 mr-4">
          <base-button
            v-show="totalEstimates || filtersApplied"
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
        <router-link slot="item-title" class="col-xs-2" to="estimates/create">
          <base-button
            size="large"
            icon="plus"
            color="theme" >
            {{ $t('estimates.new_estimate') }}</base-button>
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
            <label>{{ $t('estimates.status') }}</label>
            <base-select
              v-model="filters.status"
              :options="status"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('general.select_a_status')"
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
          <div class="filter-estimate">
            <label>{{ $t('estimates.estimate_number') }}</label>
            <base-input
              v-model="filters.estimate_number"
              icon="hashtag"/>
          </div>
        </div>
        <label class="clear-filter" @click="clearFilter">{{ $t('general.clear_all') }}</label>
      </div>
    </transition>
    <div v-cloak v-show="showEmptyScreen" class="col-xs-1 no-data-info" align="center">
      <moon-walker-icon class="mt-5 mb-4"/>
      <div class="row" align="center">
        <label class="col title">{{ $t('estimates.no_estimates') }}</label>
      </div>
      <div class="row">
        <label class="description col mt-1" align="center">{{ $t('estimates.list_of_estimates') }}</label>
      </div>
      <div class="btn-container">
        <base-button
          :outline="true"
          color="theme"
          class="mt-3"
          size="large"
          @click="$router.push('estimates/create')"
        >
          {{ $t('estimates.add_new_estimate') }}
        </base-button>
      </div>
    </div>

    <div v-show="!showEmptyScreen" class="table-container">
      <div class="table-actions mt-5">
        <p class="table-stats">{{ $t('general.showing') }}: <b>{{ estimates.length }}</b> {{ $t('general.of') }} <b>{{ totalEstimates }}</b></p>

        <!-- Tabs -->
        <ul class="tabs">
          <li class="tab" @click="getStatus('DRAFT')">
            <a :class="['tab-link', {'a-active': filters.status === 'DRAFT'}]" href="#">{{ $t('general.draft') }}</a>
          </li>
          <li class="tab" @click="getStatus('SENT')">
            <a :class="['tab-link', {'a-active': filters.status === 'SENT'}]" href="#" >{{ $t('general.sent') }}</a>
          </li>
          <li class="tab" @click="getStatus('')">
            <a :class="['tab-link', {'a-active': filters.status === '' || filters.status !== 'DRAFT' && filters.status !== 'SENT'}]" href="#">{{ $t('general.all') }}</a>
          </li>
        </ul>
        <transition name="fade">
          <v-dropdown v-if="selectedEstimates.length" :show-arrow="false">
            <span slot="activator" href="#" class="table-actions-button dropdown-toggle">
              {{ $t('general.actions') }}
            </span>
            <v-dropdown-item>
              <div class="dropdown-item" @click="removeMultipleEstimates">
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
          :disabled="estimates.length <= 0"
          type="checkbox"
          class="custom-control-input"
          @change="selectAllEstimates"
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
              <label :for="row.id" class="custom-control-label" />
            </div>
          </template>
        </table-column>
        <table-column
          :label="$t('estimates.date')"
          sort-as="estimate_date"
          show="formattedEstimateDate" />
        <table-column
          :label="$t('estimates.customer')"
          sort-as="name"
          show="name" />
        <!-- <table-column
          :label="$t('estimates.expiry_date')"
          sort-as="expiry_date"
          show="formattedExpiryDate" /> -->
        <table-column
          :label="$t('estimates.status')"
          show="status" >
          <template slot-scope="row" >
            <span> {{ $t('estimates.status') }}</span>
            <span :class="'est-status-'+row.status.toLowerCase()">{{ row.status }}</span>
          </template>
        </table-column>
        <table-column
          :label="$tc('estimates.estimate', 1)"
          show="estimate_number"/>
        <table-column
          :label="$t('invoices.total')"
          sort-as="total"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.total') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </table-column>
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.action') }} </span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>
                <router-link :to="{path: `estimates/${row.id}/edit`}" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                  {{ $t('general.edit') }}
                </router-link>
              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeEstimate(row.id)">
                  <font-awesome-icon :icon="['fas', 'trash']" class="dropdown-item-icon" />
                  {{ $t('general.delete') }}
                </div>
              </v-dropdown-item>
              <v-dropdown-item>
                <router-link :to="{path: `estimates/${row.id}/view`}" class="dropdown-item">
                  <font-awesome-icon icon="eye" class="dropdown-item-icon" />
                  {{ $t('general.view') }}
                </router-link>
              </v-dropdown-item>
              <v-dropdown-item>
                <a class="dropdown-item" href="#/" @click="convertInToinvoice(row.id)">
                  <font-awesome-icon icon="file-alt" class="dropdown-item-icon" />
                  {{ $t('estimates.convert_to_invoice') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status !== 'SENT'">
                <a class="dropdown-item" href="#/" @click.self="onMarkAsSent(row.id)">
                  <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                  {{ $t('estimates.mark_as_sent') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status !== 'SENT'">
                <a class="dropdown-item" href="#/" @click.self="sendEstimate(row.id)">
                  <font-awesome-icon icon="paper-plane" class="dropdown-item-icon" />
                  {{ $t('estimates.send_estimate') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status !== 'ACCEPTED'">
                <a class="dropdown-item" href="#/" @click.self="onMarkAsAccepted(row.id)">
                  <font-awesome-icon icon="check-circle" class="dropdown-item-icon" />
                  {{ $t('estimates.mark_as_accepted') }}
                </a>
              </v-dropdown-item>
              <v-dropdown-item v-if="row.status !== 'REJECTED'">
                <a class="dropdown-item" href="#/" @click.self="onMarkAsRejected(row.id)">
                  <font-awesome-icon icon="times-circle" class="dropdown-item-icon" />
                  {{ $t('estimates.mark_as_rejected') }}
                </a>
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
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import ObservatoryIcon from '../../components/icon/ObservatoryIcon'
import moment from 'moment'

export default {
  components: {
    'moon-walker-icon': MoonWalkerIcon
  },
  data () {
    return {
      showFilters: false,
      currency: null,
      status: ['DRAFT', 'SENT', 'VIEWED', 'EXPIRED', 'ACCEPTED', 'REJECTED'],
      filtersApplied: false,
      isRequestOngoing: true,
      filters: {
        customer: '',
        status: 'DRAFT',
        from_date: '',
        to_date: '',
        estimate_number: ''
      }
    }
  },

  computed: {
    focus,
    showEmptyScreen () {
      return !this.totalEstimates && !this.isRequestOngoing && !this.filtersApplied
    },
    filterIcon () {
      return (this.showFilters) ? 'times' : 'filter'
    },
    ...mapGetters('customer', [
      'customers'
    ]),
    ...mapGetters('estimate', [
      'selectedEstimates',
      'totalEstimates',
      'estimates',
      'selectAllField'
    ]),
    selectField: {
      get: function () {
        return this.selectedEstimates
      },
      set: function (val) {
        this.selectEstimate(val)
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
      'sendEmail'
    ]),
    ...mapActions('customer', [
      'fetchCustomers'
    ]),
    refreshTable () {
      this.$refs.table.refresh()
    },
    getStatus (val) {
      this.filters.status = val
    },
    async fetchData ({ page, filter, sort }) {
      let data = {
        customer_id: this.filters.customer === '' ? this.filters.customer : this.filters.customer.id,
        status: this.filters.status,
        from_date: this.filters.from_date === '' ? this.filters.from_date : moment(this.filters.from_date).format('DD/MM/YYYY'),
        to_date: this.filters.to_date === '' ? this.filters.to_date : moment(this.filters.to_date).format('DD/MM/YYYY'),
        estimate_number: this.filters.estimate_number,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page
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
          count: response.data.estimates.count
        }
      }
    },
    async onMarkAsAccepted (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id
          }
          let response = await this.markAsAccepted(data)
          this.refreshTable()
          if (response.data) {
            this.filters.status = ''
            this.$refs.table.refresh()
            window.toastr['success'](this.$tc('estimates.marked_as_accepted_message'))
          }
        }
      })
    },
    async onMarkAsRejected (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
        icon: '/assets/icon/times-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id
          }
          let response = await this.markAsRejected(data)
          this.refreshTable()
          if (response.data) {
            this.filters.status = ''
            this.$refs.table.refresh()
            window.toastr['success'](this.$tc('estimates.marked_as_rejected_message'))
          }
        }
      })
    },
    setFilters () {
      this.filtersApplied = true
      this.resetSelectedEstimates()
      this.$refs.table.refresh()
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
        estimate_number: ''
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
    async removeEstimate (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteEstimate(this.id)
          if (res.data.success) {
            this.$refs.table.refresh()
            this.filtersApplied = false
            this.resetSelectedEstimates()
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async convertInToinvoice (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willConvertInToinvoice) => {
        if (willConvertInToinvoice) {
          let res = await this.convertToInvoice(id)
          if (res.data) {
            window.toastr['success'](this.$t('estimates.conversion_message'))
            this.$router.push(`invoices/${res.data.invoice.id}/edit`)
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async removeMultipleEstimates () {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleEstimates()
          if (res.data.success) {
            this.$refs.table.refresh()
            this.resetSelectedEstimates()
            this.filtersApplied = false
            window.toastr['success'](this.$tc('estimates.deleted_message', 2))
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
    },
    async onMarkAsSent (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willMarkAsSent) => {
        if (willMarkAsSent) {
          const data = {
            id: id
          }
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](this.$tc('estimates.mark_as_sent_successfully'))
          }
        }
      })
    },
    async sendEstimate (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_send_estimate'),
        icon: '/assets/icon/paper-plane-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willSendEstimate) => {
        if (willSendEstimate) {
          const data = {
            id: id
          }
          let response = await this.sendEmail(data)
          this.refreshTable()
          if (response.data.success) {
            window.toastr['success'](this.$tc('estimates.send_estimate_successfully'))
            return true
          }
          if (response.data.error === 'user_email_does_not_exist') {
            window.toastr['error'](this.$tc('estimates.user_email_does_not_exist'))
            return true
          }
          window.toastr['error'](this.$tc('estimates.something_went_wrong'))
        }
      })
    }
  }
}
</script>

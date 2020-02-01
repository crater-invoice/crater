<template>
  <div class="payments main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $t('payments.title') }}</h3>
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
            {{ $tc('payments.payment',2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2 mr-4">
          <base-button
            v-show="totalPayments || filtersApplied"
            :outline="true"
            :icon="filterIcon"
            color="theme"
            right-icon
            size="large"
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
          </base-button>
        </div>
        <router-link slot="item-title" class="col-xs-2" to="payments/create">
          <base-button
            color="theme"
            icon="plus"
            size="large"
          >
            {{ $t('payments.add_payment') }}
          </base-button>
        </router-link>
      </div>
    </div>

    <transition name="fade" mode="out-in">
      <div v-show="showFilters" class="filter-section">
        <div class="row">
          <div class="col-md-4">
            <label class="form-label">{{ $t('payments.customer') }}</label>
            <base-customer-select
              ref="customerSelect"
              @select="onSelectCustomer"
              @deselect="clearCustomerSearch"
            />
          </div>
          <div class="col-sm-4">
            <label for="">{{ $t('payments.payment_number') }}</label>
            <base-input
              v-model="filters.payment_number"
              :placeholder="$t(payments.payment_number)"
              name="payment_number"
            />
          </div>
          <div class="col-sm-4">
            <label class="form-label">{{ $t('payments.payment_mode') }}</label>
            <base-select
              v-model="filters.payment_mode"
              :options="paymentModes"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('payments.payment_mode')"
              label="name"
            />
          </div>
        </div>
        <label class="clear-filter" @click="clearFilter">{{ $t('general.clear_all') }}</label>
      </div>
    </transition>

    <div v-cloak v-show="showEmptyScreen" class="col-xs-1 no-data-info" align="center">
      <capsule-icon class="mt-5 mb-4"/>
      <div class="row" align="center">
        <label class="col title">{{ $t('payments.no_payments') }}</label>
      </div>
      <div class="row">
        <label class="description col mt-1" align="center">{{ $t('payments.list_of_payments') }}</label>
      </div>
      <div class="btn-container">
        <base-button
          :outline="true"
          color="theme"
          class="mt-3"
          size="large"
          @click="$router.push('payments/create')"
        >
          {{ $t('payments.add_new_payment') }}
        </base-button>
      </div>
    </div>

    <div v-show="!showEmptyScreen" class="table-container">
      <div class="table-actions mt-5">
        <p class="table-stats">{{ $t('general.showing') }}: <b>{{ payments.length }}</b> {{ $t('general.of') }} <b>{{ totalPayments }}</b></p>

        <transition name="fade">
          <v-dropdown v-if="selectedPayments.length" :show-arrow="false">
            <span slot="activator" href="#" class="table-actions-button dropdown-toggle">
              {{ $t('general.actions') }}
            </span>
            <v-dropdown-item>
              <div class="dropdown-item" @click="removeMultiplePayments">
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
          @change="selectAllPayments"
        >
        <label v-show="!isRequestOngoing" for="select-all" class="custom-control-label selectall">
          <span class="select-all-label">{{ $t('general.select_all') }} </span>
        </label>
      </div>

      <table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
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
          :label="$t('payments.date')"
          sort-as="payment_date"
          show="formattedPaymentDate"
        />
        <table-column
          :label="$t('payments.customer')"
          show="name"
        />
        <table-column
          :label="$t('payments.payment_mode')"
          show="payment_mode"
        />
        <table-column
          :label="$t('payments.payment_number')"
          show="payment_number"
        />
        <table-column
          :label="$t('payments.invoice')"
          sort-as="invoice_id"
          show="invoice.invoice_number"
        />
        <table-column
          :label="$t('payments.amount')"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.amount') }}</span>
            <div v-html="$utils.formatMoney(row.amount, row.user.currency)" />
          </template>
        </table-column>
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>

                <router-link :to="{path: `payments/${row.id}/edit`}" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                  {{ $t('general.edit') }}
                </router-link>

              </v-dropdown-item>
              <v-dropdown-item>

                <router-link :to="{path: `payments/${row.id}/view`}" class="dropdown-item">
                  <font-awesome-icon icon="eye" class="dropdown-item-icon" />
                  {{ $t('general.view') }}
                </router-link>

              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removePayment(row.id)">
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
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import CapsuleIcon from '../../components/icon/CapsuleIcon'
import BaseButton from '../../../js/components/base/BaseButton'

export default {
  components: {
    'capsule-icon': CapsuleIcon,
    'SweetModal': SweetModal,
    'SweetModalTab': SweetModalTab,
    BaseButton
  },
  data () {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      filtersApplied: false,
      isRequestOngoing: true,
      filters: {
        customer: null,
        payment_mode: '',
        payment_number: ''
      }
    }
  },
  computed: {
    showEmptyScreen () {
      return !this.totalPayments && !this.isRequestOngoing && !this.filtersApplied
    },
    filterIcon () {
      return (this.showFilters) ? 'times' : 'filter'
    },
    ...mapGetters('customer', [
      'customers'
    ]),
    ...mapGetters('payment', [
      'selectedPayments',
      'totalPayments',
      'payments',
      'selectAllField',
      'paymentModes'
    ]),
    selectField: {
      get: function () {
        return this.selectedPayments
      },
      set: function (val) {
        this.selectPayment(val)
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
  mounted () {
    this.fetchCustomers()
  },
  destroyed () {
    if (this.selectAllField) {
      this.selectAllPayments()
    }
  },
  methods: {
    ...mapActions('payment', [
      'fetchPayments',
      'selectAllPayments',
      'selectPayment',
      'deletePayment',
      'deleteMultiplePayments',
      'setSelectAllState'
    ]),
    ...mapActions('customer', [
      'fetchCustomers'
    ]),
    async fetchData ({ page, filter, sort }) {
      let data = {
        customer_id: this.filters.customer !== null ? this.filters.customer.id : '',
        payment_number: this.filters.payment_number,
        payment_method_id: this.filters.payment_mode ? this.filters.payment_mode.id : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page
      }

      this.isRequestOngoing = true

      let response = await this.fetchPayments(data)

      this.isRequestOngoing = false

      return {
        data: response.data.payments.data,
        pagination: {
          totalPages: response.data.payments.last_page,
          currentPage: page,
          count: response.data.payments.scount
        }
      }
    },
    refreshTable () {
      this.$refs.table.refresh()
    },
    setFilters () {
      this.filtersApplied = true
      this.refreshTable()
    },
    clearFilter () {
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(this.filters.customer)
      }

      this.filters = {
        customer: null,
        payment_mode: '',
        payment_number: ''
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
    async removePayment (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePayment(this.id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async removeMultiplePayments () {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultiplePayments()
          if (request.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 2))
            this.$refs.table.refresh()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    },
    async clearCustomerSearch (removedOption, id) {
      this.filters.customer = ''
      this.$refs.table.refresh()
    },
    showModel (selectedRow) {
      this.selectedRow = selectedRow
      this.$refs.Delete_modal.open()
    },
    async removeSelectedItems () {
      this.$refs.Delete_modal.close()
      await this.selectedRow.forEach(row => {
        this.deletePayment(this.id)
      })
      this.$refs.table.refresh()
    }
  }
}
</script>

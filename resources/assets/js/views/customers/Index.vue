<template>
  <div class="customer-create main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $t('customers.title') }}</h3>
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
            {{ $tc('customers.customer',2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2 mr-4">
          <base-button
            v-show="totalCustomers || filtersApplied"
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
        <router-link slot="item-title" class="col-xs-2" to="customers/create">
          <base-button
            size="large"
            icon="plus"
            color="theme">
            {{ $t('customers.new_customer') }}
          </base-button>
        </router-link>
      </div>
    </div>

    <transition name="fade">
      <div v-show="showFilters" class="filter-section">
        <div class="row">
          <div class="col-sm-4">
            <label class="form-label">{{ $t('customers.display_name') }}</label>
            <base-input
              v-model="filters.display_name"
              type="text"
              name="name"
              autocomplete="off"
            />
          </div>
          <div class="col-sm-4">
            <label class="form-label">{{ $t('customers.contact_name') }}</label>
            <base-input
              v-model="filters.contact_name"
              type="text"
              name="address_name"
              autocomplete="off"
            />
          </div>
          <div class="col-sm-4">
            <label class="form-label">{{ $t('customers.phone') }}</label>
            <base-input
              v-model="filters.phone"
              type="text"
              name="phone"
              autocomplete="off"
            />
          </div>
          <label class="clear-filter" @click="clearFilter">{{ $t('general.clear_all') }}</label>
        </div>
      </div>
    </transition>

    <div v-cloak v-show="showEmptyScreen" class="col-xs-1 no-data-info" align="center">
      <astronaut-icon class="mt-5 mb-4"/>
      <div class="row" align="center">
        <label class="col title">{{ $t('customers.no_customers') }}</label>
      </div>
      <div class="row">
        <label class="description col mt-1" align="center">{{ $t('customers.list_of_customers') }}</label>
      </div>
      <div class="btn-container">
        <base-button
          :outline="true"
          color="theme"
          class="mt-3"
          size="large"
          @click="$router.push('customers/create')"
        >
          {{ $t('customers.add_new_customer') }}
        </base-button>
      </div>
    </div>

    <div v-show="!showEmptyScreen" class="table-container">
      <div class="table-actions mt-5">
        <p class="table-stats">{{ $t('general.showing') }}: <b>{{ customers.length }}</b> {{ $t('general.of') }} <b>{{ totalCustomers }}</b></p>

        <transition name="fade">
          <v-dropdown v-if="selectedCustomers.length" :show-arrow="false">
            <span slot="activator" href="#" class="table-actions-button dropdown-toggle">
              {{ $t('general.actions') }}
            </span>
            <v-dropdown-item>
              <div class="dropdown-item" @click="removeMultipleCustomers">
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
          @change="selectAllCustomers"
        >
        <label for="select-all" class="custom-control-label selectall">
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
          :label="$t('customers.display_name')"
          show="name"
        />
        <table-column
          :label="$t('customers.contact_name')"
          show="contact_name"
        />
        <table-column
          :label="$t('customers.phone')"
          show="phone"
        />
        <table-column
          :label="$t('customers.amount_due')"
          show="due_amount"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.amount_due') }} </span>
            <div v-html="$utils.formatMoney(row.due_amount, row.currency)"/>
          </template>
        </table-column>
        <table-column
          :label="$t('customers.added_on')"
          sort-as="created_at"
          show="formattedCreatedAt"
        />
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.action') }} </span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>

                <router-link :to="{path: `customers/${row.id}/edit`}" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon"/>
                  {{ $t('general.edit') }}
                </router-link>

              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeCustomer(row.id)">
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
import DotIcon from '../../components/icon/DotIcon'
import AstronautIcon from '../../components/icon/AstronautIcon'
import BaseButton from '../../../js/components/base/BaseButton'
import { request } from 'http'

export default {
  components: {
    DotIcon,
    AstronautIcon,
    SweetModal,
    SweetModalTab,
    BaseButton
  },
  data () {
    return {
      showFilters: false,
      filtersApplied: false,
      isRequestOngoing: true,
      filters: {
        display_name: '',
        contact_name: '',
        phone: ''
      }
    }
  },
  computed: {
    showEmptyScreen () {
      return !this.totalCustomers && !this.isRequestOngoing && !this.filtersApplied
    },
    filterIcon () {
      return (this.showFilters) ? 'times' : 'filter'
    },
    ...mapGetters('customer', [
      'customers',
      'selectedCustomers',
      'totalCustomers',
      'selectAllField'
    ]),
    selectField: {
      get: function () {
        return this.selectedCustomers
      },
      set: function (val) {
        this.selectCustomer(val)
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
  destroyed () {
    if (this.selectAllField) {
      this.selectAllCustomers()
    }
  },
  methods: {
    ...mapActions('customer', [
      'fetchCustomers',
      'selectAllCustomers',
      'selectCustomer',
      'deleteCustomer',
      'deleteMultipleCustomers',
      'setSelectAllState'
    ]),
    refreshTable () {
      this.$refs.table.refresh()
    },
    async fetchData ({ page, filter, sort }) {
      let data = {
        display_name: this.filters.display_name,
        contact_name: this.filters.contact_name,
        phone: this.filters.phone,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page
      }

      this.isRequestOngoing = true
      let response = await this.fetchCustomers(data)
      this.isRequestOngoing = false

      return {
        data: response.data.customers.data,
        pagination: {
          totalPages: response.data.customers.last_page,
          currentPage: page
        }
      }
    },
    setFilters () {
      this.filtersApplied = true
      this.refreshTable()
    },
    clearFilter () {
      this.filters = {
        display_name: '',
        contact_name: '',
        phone: ''
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
    async removeCustomer (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomer(id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message'))
            this.refreshTable()
            return true
          } else if (request.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async removeMultipleCustomers () {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultipleCustomers()
          if (request.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message', 2))
            this.refreshTable()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    }
  }
}
</script>

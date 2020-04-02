<template>
  <div class="expenses main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $t('expenses.title') }}</h3>
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
            {{ $tc('expenses.expense',2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2 mr-4">
          <base-button
            v-show="totalExpenses || filtersApplied"
            :outline="true"
            :icon="filterIcon"
            size="large"
            right-icon
            color="theme"
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
          </base-button>
        </div>
        <router-link slot="item-title" class="col-xs-2" to="expenses/create">
          <base-button size="large" icon="plus" color="theme">
            {{ $t('expenses.add_expense') }}
          </base-button>
        </router-link>
      </div>
    </div>

    <transition name="fade">
      <div v-show="showFilters" class="filter-section">
        <div class="row">
          <div class="col-md-4">
            <label>{{ $t('expenses.category') }}</label>
            <base-select
              v-model="filters.category"
              :options="categories"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.categories.select_a_category')"
              label="name"
              @click="filter = ! filter"
            />
          </div>
          <div class="col-md-4">
            <label>{{ $t('expenses.from_date') }}</label>
            <base-date-picker
              v-model="filters.from_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </div>
          <div class="col-md-4">
            <label>{{ $t('expenses.to_date') }}</label>
            <base-date-picker
              v-model="filters.to_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </div>
        </div>
        <label class="clear-filter" @click="clearFilter">{{ $t('general.clear_all') }}</label>
      </div>
    </transition>

    <div v-cloak v-show="showEmptyScreen" class="col-xs-1 no-data-info" align="center">
      <observatory-icon class="mt-5 mb-4"/>
      <div class="row" align="center">
        <label class="col title">{{ $t('expenses.no_expenses') }}</label>
      </div>
      <div class="row">
        <label class="description col mt-1" align="center">{{ $t('expenses.list_of_expenses') }}</label>
      </div>
      <div class="row">
        <div class="col">
          <base-button
            :outline="true"
            color="theme"
            class="mt-3"
            size="large"
            @click="$router.push('expenses/create')"
          >
            {{ $t('expenses.add_new_expense') }}
          </base-button>
        </div>
      </div>
    </div>

    <div v-show="!showEmptyScreen" class="table-container">

      <div class="table-actions mt-5">
        <p class="table-stats">{{ $t('general.showing') }}: <b>{{ expenses.length }}</b> {{ $t('general.of') }} <b>{{ totalExpenses }}</b></p>
        <transition name="fade">
          <v-dropdown v-if="selectedExpenses.length" :show-arrow="false" theme-light class="action mr-5">
            <span slot="activator" href="#" class="table-actions-button dropdown-toggle">
              {{ $t('general.actions') }}
            </span>
            <v-dropdown-item>
              <div class="dropdown-item" @click="removeMultipleExpenses">
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
          @change="selectAllExpenses"
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
          :label="$tc('expenses.categories.category', 1)"
          sort-as="name"
          show="category.name"
        />
        <table-column
          :label="$t('expenses.date')"
          sort-as="expense_date"
          show="formattedExpenseDate"
        />
        <table-column
          :label="$t('expenses.note')"
          sort-as="expense_date"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.note') }}</span>
            <div class="notes">
              <div class="note">{{ row.notes }}</div>
            </div>
          </template>
        </table-column>
        <table-column
          :label="$t('expenses.amount')"
          sort-as="amount"
          show="category.amount"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.amount') }}</span>
            <div v-html="$utils.formatMoney(row.amount, defaultCurrency)" />
          </template>
        </table-column>
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.action') }}</span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>
                <router-link :to="{path: `expenses/${row.id}/edit`}" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                  {{ $t('general.edit') }}
                </router-link>
              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeExpense(row.id)">
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
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import { mapActions, mapGetters } from 'vuex'
import ObservatoryIcon from '../../components/icon/ObservatoryIcon'
import MultiSelect from 'vue-multiselect'
import moment, { invalid } from 'moment'

export default {
  components: {
    MultiSelect,
    'observatory-icon': ObservatoryIcon,
    'SweetModal': SweetModal,
    'SweetModalTab': SweetModalTab
  },
  data () {
    return {
      showFilters: false,
      filtersApplied: false,
      isRequestOngoing: true,
      filters: {
        category: null,
        from_date: '',
        to_date: ''
      }
    }
  },
  computed: {
    showEmptyScreen () {
      return !this.totalExpenses && !this.isRequestOngoing && !this.filtersApplied
    },
    filterIcon () {
      return (this.showFilters) ? 'times' : 'filter'
    },
    ...mapGetters('category', [
      'categories'
    ]),
    ...mapGetters('expense', [
      'selectedExpenses',
      'totalExpenses',
      'expenses',
      'selectAllField'
    ]),
    ...mapGetters('currency', [
      'defaultCurrency'
    ]),
    selectField: {
      get: function () {
        return this.selectedExpenses
      },
      set: function (val) {
        this.selectExpense(val)
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
      this.selectAllExpenses()
    }
  },
  created () {
    this.fetchCategories()
  },
  methods: {
    ...mapActions('expense', [
      'fetchExpenses',
      'selectExpense',
      'deleteExpense',
      'deleteMultipleExpenses',
      'selectAllExpenses',
      'setSelectAllState'
    ]),
    ...mapActions('category', [
      'fetchCategories'
    ]),
    async fetchData ({ page, filter, sort }) {
      let data = {
        expense_category_id: this.filters.category !== null ? this.filters.category.id : '',
        from_date: this.filters.from_date === '' ? this.filters.from_date : moment(this.filters.from_date).format('DD/MM/YYYY'),
        to_date: this.filters.to_date === '' ? this.filters.to_date : moment(this.filters.to_date).format('DD/MM/YYYY'),
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page
      }

      this.isRequestOngoing = true
      let response = await this.fetchExpenses(data)
      this.isRequestOngoing = false

      return {
        data: response.data.expenses.data,
        pagination: {
          totalPages: response.data.expenses.last_page,
          currentPage: page,
          count: response.data.expenses.count
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
      this.filters = {
        category: null,
        from_date: '',
        to_date: ''
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
    async removeExpense (id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteExpense(id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async removeMultipleExpenses () {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultipleExpenses()
          if (request.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 2))
            this.$refs.table.refresh()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    }
  }
}
</script>

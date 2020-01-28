<template>
  <div class="items main-content">
    <div class="page-header">
      <div class="d-flex flex-row">
        <div>
          <h3 class="page-title">{{ $tc('items.item', 2) }}</h3>
        </div>
      </div>
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
            {{ $tc('items.item', 2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2 mr-4">
          <base-button
            v-show="totalItems || filtersApplied"
            :outline="true"
            :icon="filterIcon"
            color="theme"
            size="large"
            right-icon
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
          </base-button>
        </div>
        <router-link slot="item-title" class="col-xs-2" to="items/create">
          <base-button
            color="theme"
            icon="plus"
            size="large"
          >
            {{ $t('items.add_item') }}
          </base-button>
        </router-link>
      </div>
    </div>

    <transition name="fade">
      <div v-show="showFilters" class="filter-section">
        <div class="row">
          <div class="col-sm-4">
            <label class="form-label"> {{ $tc('items.name') }} </label>
            <base-input
              v-model="filters.name"
              type="text"
              name="name"
              autocomplete="off"
            />
          </div>
          <div class="col-sm-4">
            <label class="form-label"> {{ $tc('items.unit') }} </label>
            <base-select
              v-model="filters.unit"
              :options="itemUnits"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('items.select_a_unit')"
              label="name"
              autocomplete="off"
            />
          </div>
          <div class="col-sm-4">
            <label class="form-label"> {{ $tc('items.price') }} </label>
            <base-input
              v-model="filters.price"
              type="text"
              name="name"
              autocomplete="off"
            />
          </div>
          <label class="clear-filter" @click="clearFilter"> {{ $t('general.clear_all') }}</label>
        </div>
      </div>
    </transition>

    <div v-cloak v-show="showEmptyScreen" class="col-xs-1 no-data-info" align="center">
      <satellite-icon class="mt-5 mb-4"/>
      <div class="row" align="center">
        <label class="col title">{{ $t('items.no_items') }}</label>
      </div>
      <div class="row">
        <label class="description col mt-1" align="center">{{ $t('items.list_of_items') }}</label>
      </div>
      <div class="btn-container">
        <base-button
          :outline="true"
          color="theme"
          class="mt-3"
          size="large"
          @click="$router.push('items/create')"
        >
          {{ $t('items.add_new_item') }}
        </base-button>
      </div>
    </div>

    <div v-show="!showEmptyScreen" class="table-container">
      <div class="table-actions mt-5">
        <p class="table-stats">{{ $t('general.showing') }}: <b>{{ items.length }}</b> {{ $t('general.of') }} <b>{{ totalItems }}</b></p>
        <transition name="fade">
          <v-dropdown v-if="selectedItems.length" :show-arrow="false">
            <span slot="activator" href="#" class="table-actions-button dropdown-toggle">
              {{ $t('general.actions') }}
            </span>
            <v-dropdown-item>
              <div class="dropdown-item" @click="removeMultipleItems">
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
          @change="selectAllItems"
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
              <label :for="row.id" class="custom-control-label"/>
            </div>
          </template>
        </table-column>
        <table-column
          :label="$t('items.name')"
          show="name"
        />
        <table-column
          :label="$t('items.unit')"
          show="unit_name"
        />
        <table-column
          :label="$t('items.price')"
          show="price"
        >
          <template slot-scope="row">
            <span> {{ $t('items.price') }} </span>
            <div v-html="$utils.formatMoney(row.price, defaultCurrency)" />
          </template>
        </table-column>
        <table-column
          :label="$t('items.added_on')"
          sort-as="created_at"
          show="formattedCreatedAt"
        />
        <table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('items.action') }} </span>
            <v-dropdown>
              <a slot="activator" href="#">
                <dot-icon />
              </a>
              <v-dropdown-item>

                <router-link :to="{path: `items/${row.id}/edit`}" class="dropdown-item">
                  <font-awesome-icon :icon="['fas', 'pencil-alt']" class="dropdown-item-icon" />
                  {{ $t('general.edit') }}
                </router-link>

              </v-dropdown-item>
              <v-dropdown-item>
                <div class="dropdown-item" @click="removeItems(row.id)">
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
import DotIcon from '../../components/icon/DotIcon'
import SatelliteIcon from '../../components/icon/SatelliteIcon'
import BaseButton from '../../../js/components/base/BaseButton'

export default {
  components: {
    DotIcon,
    SatelliteIcon,
    BaseButton
  },
  data () {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      filtersApplied: false,
      filters: {
        name: '',
        unit: '',
        price: ''
      }
    }
  },
  computed: {
    ...mapGetters('item', [
      'items',
      'selectedItems',
      'totalItems',
      'selectAllField',
      'itemUnits'
    ]),
    ...mapGetters('currency', [
      'defaultCurrency'
    ]),
    showEmptyScreen () {
      return !this.totalItems && !this.isRequestOngoing && !this.filtersApplied
    },
    filterIcon () {
      return (this.showFilters) ? 'times' : 'filter'
    },
    selectField: {
      get: function () {
        return this.selectedItems
      },
      set: function (val) {
        this.selectItem(val)
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
      this.selectAllItems()
    }
  },
  methods: {
    ...mapActions('item', [
      'fetchItems',
      'selectAllItems',
      'selectItem',
      'deleteItem',
      'deleteMultipleItems',
      'setSelectAllState'
    ]),
    refreshTable () {
      this.$refs.table.refresh()
    },
    async fetchData ({ page, filter, sort }) {
      let data = {
        search: this.filters.name !== null ? this.filters.name : '',
        unit_id: this.filters.unit !== null ? this.filters.unit.id : '',
        price: this.filters.price * 100,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page
      }

      this.isRequestOngoing = true
      let response = await this.fetchItems(data)
      this.isRequestOngoing = false

      return {
        data: response.data.items.data,
        pagination: {
          totalPages: response.data.items.last_page,
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
        name: '',
        unit: '',
        price: ''
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
    async removeItems (id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteItem(this.id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('items.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'item_attached') {
            window.toastr['error'](this.$tc('items.item_attached_message'), this.$t('general.action_failed'))
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    },
    async removeMultipleItems () {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleItems()
          if (res.data.success || res.data.items) {
            window.toastr['success'](this.$tc('items.deleted_message', 2))
            this.$refs.table.refresh()
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    }
  }
}
</script>

<template>
  <div
    class="fixed top-0 left-0 hidden h-full pt-16 pb-4 ml-56 bg-white xl:ml-64 w-88 xl:block"
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
        @input="onSearch()"
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

          <div
            class="px-2 py-1 pb-2 mb-2 text-sm border-b border-gray-200 border-solid"
          >
            {{ $t('general.sort_by') }}
          </div>

          <sw-dropdown-item class="flex cursor-pointer">
            <sw-input-group class="-mt-3 font-normal">
              <sw-radio
                id="filter_create_date"
                :label="$t('customers.create_date')"
                v-model="searchData.orderByField"
                size="sm"
                name="filter"
                value="invoices.created_at"
                @change="onSearch"
              />
            </sw-input-group>
          </sw-dropdown-item>

          <sw-dropdown-item class="flex cursor-pointer">
            <sw-input-group class="-mt-3 font-normal">
              <sw-radio
                id="filter_display_name"
                :label="$t('customers.display_name')"
                v-model="searchData.orderByField"
                size="sm"
                name="filter"
                value="users.name"
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
      class="h-full pb-32 overflow-y-scroll border-l border-gray-200 border-solid sidebar sw-scroll"
    >
      <router-link
        v-for="(customer, index) in customers"
        :to="`/admin/customers/${customer.id}/view`"
        :key="index"
        :id="'customer-' + customer.id"
        :class="[
          'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100 border-l-4 border-transparent',
          {
            'bg-gray-100 border-l-4 border-primary-500 border-solid': hasActiveUrl(
              customer.id
            ),
          },
        ]"
        style="border-top: 1px solid rgba(185, 193, 209, 0.41)"
      >
        <div>
          <div
            class="pr-2 text-sm not-italic font-normal leading-5 text-black capitalize truncate"
          >
            {{ customer.name }}
          </div>
          <div
            v-if="customer.contact_name"
            class="mt-1 text-xs not-italic font-medium leading-5 text-gray-600"
          >
            {{ customer.contact_name }}
          </div>
        </div>
        <div class="flex-1 whitespace-nowrap right">
          <div
            class="text-xl not-italic font-semibold leading-8 text-right text-gray-900"
            v-html="$utils.formatMoney(customer.due_amount, customer.currency)"
          />
        </div>
      </router-link>
      <p
        v-if="!customers.length"
        class="flex justify-center px-4 mt-5 text-sm text-gray-600"
      >
        {{ $t('customers.no_matching_customers') }}
      </p>
    </div>
  </div>
</template>

<script>
import {
  FilterIcon,
  SortAscendingIcon,
  SortDescendingIcon,
  SearchIcon,
} from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
const _ = require('lodash')

export default {
  components: {
    FilterIcon,
    SortAscendingIcon,
    SortDescendingIcon,
    SearchIcon,
  },
  data() {
    return {
      id: null,
      customers: [],
      customer: null,
      currency: null,
      searchData: {
        orderBy: null,
        orderByField: null,
        searchText: null,
      },
      isSearching: false,
    }
  },
  computed: {
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
    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('customer', ['selectedViewCustomer']),
  },

  watch: {
    $route(to, from) {
      this.loadCustomer()
    },
  },

  created() {
    this.loadCustomers()
    this.loadCustomer()
    this.onSearch = _.debounce(this.onSearch, 500)
  },

  methods: {
    ...mapActions('customer', ['fetchCustomers']),

    hasActiveUrl(id) {
      return this.$route.params.id == id
    },

    async loadCustomers() {
      let response = await this.fetchCustomers({
        limit: 'all',
      })

      if (response.data.customers) {
        this.customers = response.data.customers.data
      }

      setTimeout(() => {
        this.scrollToCustomer()
      }, 500)
    },

    scrollToCustomer() {
      const el = document.getElementById(`customer-${this.$route.params.id}`)

      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
        el.classList.add('shake')
      }
    },

    async loadCustomer() {
      this.customer = this.selectedViewCustomer
      this.currency = this.selectedViewCustomer.currency
    },
    async onSearch() {
      let data = {}
      if (
        this.searchData.searchText !== '' &&
        this.searchData.searchText !== null &&
        this.searchData.searchText !== undefined
      ) {
        data.display_name = this.searchData.searchText
      }

      if (
        this.searchData.orderBy !== null &&
        this.searchData.orderBy !== undefined
      ) {
        data.orderBy = this.searchData.orderBy
      }

      if (
        this.searchData.orderByField !== null &&
        this.searchData.orderByField !== undefined
      ) {
        data.orderByField = this.searchData.orderByField
      }

      this.isSearching = true
      try {
        let response = await this.fetchCustomers({ ...data })
        this.isSearching = false
        if (response.data) {
          this.customers = response.data.customers.data
        }
      } catch (error) {
        this.isSearching = false
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
  },
}
</script>

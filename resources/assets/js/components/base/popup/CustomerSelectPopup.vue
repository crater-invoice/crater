<template>
  <div class="customer-select">
    <div class="flex flex-col w-full pb-4">
      <div class="flex px-4 pt-4 pb-2">
        <sw-input
          v-model="search"
          :placeholder="$t('general.search')"
          focus
          type="text"
          icon="search"
          @input="searchCustomer"
        >
          <template v-slot:leftIcon>
            <search-icon class="h-5 m-2 text-gray-500" />
          </template>
        </sw-input>
      </div>

      <div
        v-if="customers.length > 0 && !loading"
        class="relative flex flex-col overflow-auto sw-scroll list"
      >
        <div
          v-for="(customer, index) in customers"
          :key="index"
          class="flex px-6 py-2 border-b border-gray-200 border-solid cursor-pointer hover:cursor-pointer hover:bg-gray-100 last:border-b-0"
          @click="selectNewCustomer(customer.id)"
        >
          <span
            class="flex items-center content-center justify-center w-10 h-10 mr-4 text-xl font-semibold leading-9 text-white bg-gray-400 rounded-full avatar"
            >{{ initGenerator(customer.name) }}</span
          >
          <div class="flex flex-col justify-center">
            <label class="m-0 leading-tight cursor-pointer font-base">{{
              customer.name
            }}</label>
            <label
              class="m-0 text-sm font-medium text-gray-500 cursor-pointer font-base"
              >{{ customer.contact_name }}</label
            >
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center list">
        <refresh-icon class="animate-spin" />
      </div>

      <div
        v-if="customers.length === 0"
        class="flex justify-center p-5 text-gray-400"
      >
        <label class="cursor-pointer">
          {{ $t('customers.no_customers_found') }}
        </label>
      </div>
    </div>

    <button
      type="button"
      class="flex items-center justify-center w-full px-2 py-3 bg-gray-200 border-none outline-none"
      @click="openCustomerModal"
    >
      <user-add-icon class="text-primary-400" />

      <label
        class="m-0 ml-3 text-sm leading-none cursor-pointer font-base text-primary-400"
        >{{ $t('customers.add_new_customer') }}</label
      >
    </button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { UserAddIcon, SearchIcon, RefreshIcon } from '@vue-hero-icons/solid'

export default {
  components: { UserAddIcon, SearchIcon, RefreshIcon },

  props: {
    type: {
      type: String,
      required: true,
    },
    userId: {
      type: Number,
      required: false,
    },
  },

  data() {
    return {
      search: null,
      loading: false,
    }
  },

  computed: {
    ...mapGetters('customer', ['customers']),
  },

  created() {
    this.fetchInitialCustomers()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('customer', ['fetchCustomers']),
    ...mapActions('invoice', {
      setInvoiceCustomer: 'selectCustomer',
    }),
    ...mapActions('estimate', {
      setEstimateCustomer: 'selectCustomer',
    }),

    async fetchInitialCustomers() {
      await this.fetchCustomers({
        filter: {},
        orderByField: '',
        orderBy: '',
        customer_id: this.userId,
      })
    },

    async searchCustomer() {
      let data = {
        display_name: this.search,
        email: '',
        phone: '',
        orderByField: '',
        orderBy: '',
        page: 1,
      }

      this.loading = true

      await this.fetchCustomers(data)

      this.loading = false
    },

    openCustomerModal() {
      this.openModal({
        title: this.$t('customers.add_customer'),
        componentName: 'CustomerModal',
        variant: 'lg',
      })
    },

    initGenerator(name) {
      if (name) {
        let nameSplit = name.split(' ')
        let initials = nameSplit[0].charAt(0).toUpperCase()
        return initials
      }
    },

    selectNewCustomer(id) {
      if (this.type === 'estimate') {
        this.setEstimateCustomer(id)
      } else {
        this.setInvoiceCustomer(id)
      }
    },
  },
}
</script>
<style lang="scss">
.customer-select {
  .list {
    max-height: 173px;
    min-height: 173px;
  }
}
</style>

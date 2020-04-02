<template>
  <div class="customer-select">
    <div class="main">
      <div class="search-bar">
        <base-input
          v-model="search"
          :placeholder="$t('general.search')"
          focus
          type="text"
          icon="search"
          @input="searchCustomer"
        />
      </div>

      <div v-if="(customers.length > 0) && !loading" class="list">
        <div
          v-for="(customer, index) in customers"
          :key="index"
          class="list-item"
          @click="selectNewCustomer(customer.id)"
        >
          <span class="avatar" >{{ initGenerator(customer.name) }}</span>
          <div class="name">
            <label class="title">{{ customer.name }}</label>
            <label class="sub-title">{{ customer.contact_name }}</label>
          </div>
        </div>
      </div>
      <div v-if="loading" class="list flex justify-content-center align-items-center">
        <font-awesome-icon icon="spinner" class="fa-spin"/>
      </div>
      <div v-if="customers.length === 0" class="no-data-label">
        <label> {{ $t('customers.no_customers_found') }} </label>
      </div>
    </div>

    <button type="button" class="list-add-button" @click="openCustomerModal">
      <font-awesome-icon class="icon" icon="user-plus" />
      <label>{{ $t('customers.add_new_customer') }}</label>
    </button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  props: {
    type: {
      type: String,
      required: true
    }
  },
  data () {
    return {
      search: null,
      loading: false
    }
  },
  computed: {
    ...mapGetters('customer', [
      'customers'
    ])
  },
  created () {
    this.fetchInitialCustomers()
  },
  methods: {
    ...mapActions('modal', [
      'openModal'
    ]),
    ...mapActions('customer', [
      'fetchCustomers'
    ]),
    ...mapActions('invoice', {
      setInvoiceCustomer: 'selectCustomer'
    }),
    ...mapActions('estimate', {
      setEstimateCustomer: 'selectCustomer'
    }),
    async fetchInitialCustomers () {
      await this.fetchCustomers({
        filter: {},
        orderByField: '',
        orderBy: ''
      })
    },
    async searchCustomer () {
      let data = {
        display_name: this.search,
        email: '',
        phone: '',
        orderByField: '',
        orderBy: '',
        page: 1
      }

      this.loading = true

      await this.fetchCustomers(data)

      this.loading = false
    },
    openCustomerModal () {
      this.openModal({
        title: this.$t('customers.add_customer'),
        componentName: 'CustomerModal',
        size: 'lg'
      })
    },
    initGenerator (name) {
      if (name) {
        let nameSplit = name.split(' ')
        let initials = nameSplit[0].charAt(0).toUpperCase()
        return initials
      }
    },
    selectNewCustomer (id) {
      if (this.type === 'estimate') {
        this.setEstimateCustomer(id)
      } else {
        this.setInvoiceCustomer(id)
      }
    }
  }
}
</script>

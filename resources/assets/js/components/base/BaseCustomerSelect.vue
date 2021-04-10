<template>
  <div class="item-selector">
    <sw-select
      ref="baseSelect"
      v-model="customerSelect"
      :options="customers"
      :show-labels="false"
      :preserve-search="false"
      :placeholder="$t('customers.type_or_click')"
      label="name"
      class="multi-select-item"
      @close="checkCustomers"
      @value="onTextChange"
      @select="(val) => $emit('select', val)"
      @remove="deselectCustomer"
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data() {
    return {
      customerSelect: null,
      loading: false,
    }
  },
  computed: {
    ...mapGetters('customer', ['customers']),
  },

  created() {
    this.fetchCustomers()
  },

  methods: {
    ...mapActions('customer', ['fetchCustomers']),
    async searchCustomers(search) {
      this.loading = true

      await this.fetchCustomers({ search })

      this.loading = false
    },
    onTextChange(val) {
      this.searchCustomers(val)
    },
    checkCustomers(val) {
      if (!this.customers.length) {
        this.fetchCustomers()
      }
    },
    deselectCustomer() {
      this.customerSelect = null
      this.$emit('deselect')
    },
  },
}
</script>

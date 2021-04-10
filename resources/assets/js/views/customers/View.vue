<template>
  <base-page class="xl:pl-96">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/edit`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.edit') }}
        </sw-button>
        <sw-dropdown position="bottom-end">
          <sw-button slot="activator" class="mr-3" variant="primary">
            {{ $t('customers.new_transaction') }}
          </sw-button>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/estimates/create?customer=${$route.params.id}`"
          >
            <document-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('estimates.new_estimate') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/invoices/create?customer=${$route.params.id}`"
          >
            <document-text-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('invoices.new_invoice') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/payments/create?customer=${$route.params.id}`"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payments.new_payment') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            tag-name="router-link"
            :to="`/admin/expenses/create?customer=${$route.params.id}`"
          >
            <calculator-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('expenses.new_expense') }}
          </sw-dropdown-item>
        </sw-dropdown>
        <sw-dropdown>
          <sw-button slot="activator" variant="primary">
            <dots-horizontal-icon class="h-5 -ml-1 -mr-1" />
          </sw-button>

          <sw-dropdown-item @click="removeCustomer($route.params.id)">
            <trash-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </sw-dropdown-item>
        </sw-dropdown>
      </template>
    </sw-page-header>

    <!-- sidebar -->
    <customer-view-sidebar />

    <!-- Chart -->
    <customer-chart />
  </base-page>
</template>

<script>
import {
  DotsHorizontalIcon,
  TrashIcon,
  DocumentIcon,
  DocumentTextIcon,
  CreditCardIcon,
  CalculatorIcon,
} from '@vue-hero-icons/solid'
import LineChart from '../../components/chartjs/LineChart'
import CustomerViewSidebar from './partials/CustomerViewSidebar'
import CustomerChart from './partials/CustomerChart'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    LineChart,
    DotsHorizontalIcon,
    CustomerViewSidebar,
    DocumentIcon,
    DocumentTextIcon,
    CreditCardIcon,
    CalculatorIcon,
    CustomerChart,
    TrashIcon,
  },
  data() {
    return {
      customer: null,
    }
  },
  computed: {
    ...mapGetters('customer', ['selectedViewCustomer']),
    pageTitle() {
      return this.selectedViewCustomer.customer
        ? this.selectedViewCustomer.customer.name
        : ''
    },
  },
  created() {
    this.fetchViewCustomer({ id: this.$route.params.id })
  },
  methods: {
    ...mapActions('customer', [
      'fetchViewCustomer',
      'selectCustomer',
      'deleteMultipleCustomers',
    ]),

    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let data = [id]
          this.selectCustomer(data)
          let res = await this.deleteMultipleCustomers()
          if (res.data.success) {
            window.toastr['success'](this.$tc('customers.deleted_message'))
            this.$router.push('/admin/customers')
            return true
          } else if (request.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
  },
}
</script>

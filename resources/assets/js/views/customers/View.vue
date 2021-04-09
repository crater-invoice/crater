<template>
  <base-page class="xl:pl-96">
    <sw-page-header :title="pageTitle">
      <template slot="actions">
        <sw-button
          :to="`/admin/customers/${$route.params.id}/edit`"
          tag-name="router-link"
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
            :to="`/admin/estimates/create?customer=${$route.params.id}`"
            tag-name="router-link"
          >
            <document-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('estimates.new_estimate') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            :to="`/admin/invoices/create?customer=${$route.params.id}`"
            tag-name="router-link"
          >
            <document-text-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('invoices.new_invoice') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            :to="`/admin/payments/create?customer=${$route.params.id}`"
            tag-name="router-link"
          >
            <credit-card-icon class="h-5 mr-3 text-gray-600" />
            {{ $t('payments.new_payment') }}
          </sw-dropdown-item>
          <sw-dropdown-item
            :to="`/admin/expenses/create?customer=${$route.params.id}`"
            tag-name="router-link"
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
    ...mapActions('notification', ['showNotification']),

    async removeCustomer(id) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: 'question',
        iconHtml: `<svg
            aria-hidden="true"
            class="w-6 h-6"
            focusable="false"
            data-prefix="fas"
            data-icon="trash"
            class="svg-inline--fa fa-trash fa-w-14"
            role="img"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 448 512"
          >
            <path
              fill="#55547A"
              d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"
            ></path>
          </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let data = [id]
          this.selectCustomer(data)
          let res = await this.deleteMultipleCustomers()
          if (res.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('customers.deleted_message'),
            })
            this.$router.push('/admin/customers')
            return true
          } else if (request.data.error) {
            this.showNotification({
              type: 'error',
              message: res.data.message,
            })
          }
        }
      })
    },
  },
}
</script>

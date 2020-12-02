<template>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
    <!-- Amount Due -->
    <router-link
      slot="item-title"
      class="relative flex justify-between p-3 bg-white rounded shadow hover:bg-gray-100 lg:col-span-3 xl:p-4"
      to="/admin/invoices"
    >
      <div>
        <span
          v-if="getDashboardDataLoaded"
          class="text-xl font-semibold leading-tight text-black xl:text-3xl"
        >
          <span
            v-html="$utils.formatMoney(getTotalDueAmount, defaultCurrency)"
          />
        </span>
        <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
          {{ $t('dashboard.cards.due_amount') }}
        </span>
      </div>
      <div class="flex items-center">
        <dollar-icon class="w-10 h-10 xl:w-12 xl:h-12" />
      </div>
    </router-link>

    <!-- Customers -->
    <router-link
      slot="item-title"
      class="relative flex justify-between p-3 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
      to="/admin/customers"
    >
      <div>
        <span
          v-if="getDashboardDataLoaded"
          class="text-xl font-semibold leading-tight text-black xl:text-3xl"
        >
          {{ getContacts }}
        </span>
        <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
          {{ $t('dashboard.cards.customers') }}
        </span>
      </div>
      <div class="flex items-center">
        <contact-icon class="w-10 h-10 xl:w-12 xl:h-12" />
      </div>
    </router-link>

    <!-- Invoices -->
    <router-link
      slot="item-title"
      class="relative flex justify-between p-3 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
      to="/admin/invoices"
    >
      <div>
        <span
          v-if="getDashboardDataLoaded"
          class="text-xl font-semibold leading-tight text-black xl:text-3xl"
        >
          {{ getInvoices }}
        </span>
        <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
          {{ $t('dashboard.cards.invoices') }}
        </span>
      </div>
      <div class="flex items-center">
        <invoice-icon class="w-10 h-10 xl:w-12 xl:h-12" />
      </div>
    </router-link>

    <!-- Estimates -->
    <router-link
      slot="item-title"
      class="relative flex justify-between p-3 bg-white rounded shadow hover:bg-gray-100 lg:col-span-2 xl:p-4"
      to="/admin/estimates"
    >
      <div>
        <span
          v-if="getDashboardDataLoaded"
          class="text-xl font-semibold leading-tight text-black xl:text-3xl"
        >
          {{ getEstimates }}
        </span>
        <span class="block mt-1 text-sm leading-tight text-gray-500 xl:text-lg">
          {{ $t('dashboard.cards.estimates') }}
        </span>
      </div>
      <div class="flex items-center">
        <estimate-icon class="w-10 h-10 xl:w-12 xl:h-12" />
      </div>
    </router-link>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import DollarIcon from '../../components/icon/DollarIcon'
import ContactIcon from '../../components/icon/ContactIcon'
import InvoiceIcon from '../../components/icon/InvoiceIcon'
import EstimateIcon from '../../components/icon/EstimateIcon'

export default {
  components: {
    DollarIcon,
    ContactIcon,
    InvoiceIcon,
    EstimateIcon,
  },
  data() {
    return {
      ...this.$store.state.dashboard,
    }
  },
  computed: {
    ...mapGetters('user', {
      user: 'currentUser',
    }),
    ...mapGetters('dashboard', [
      'getContacts',
      'getInvoices',
      'getEstimates',
      'getTotalDueAmount',
      'getDashboardDataLoaded',
    ]),
    ...mapGetters('company', ['defaultCurrency']),
  },
}
</script>

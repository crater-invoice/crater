<template>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
    <!-- Amount Due -->
    <DashboardStatsItem
      v-if="userStore.hasAbilities(abilities.VIEW_INVOICE)"
      :icon-component="DollarIcon"
      :loading="!dashboardStore.isDashboardDataLoaded"
      route="/admin/invoices"
      :large="true"
      :label="$t('dashboard.cards.due_amount')"
    >
      <BaseFormatMoney
        :amount="dashboardStore.stats.totalAmountDue"
        :currency="companyStore.selectedCompanyCurrency"
      />
    </DashboardStatsItem>

    <!-- Customers -->
    <DashboardStatsItem
      v-if="userStore.hasAbilities(abilities.VIEW_CUSTOMER)"
      :icon-component="CustomerIcon"
      :loading="!dashboardStore.isDashboardDataLoaded"
      route="/admin/customers"
      :label="(dashboardStore.stats.totalCustomerCount <= 1 ? $tc('dashboard.cards.customers', 1) : $tc('dashboard.cards.customers', 2))"
    >
      {{ dashboardStore.stats.totalCustomerCount }}
    </DashboardStatsItem>

    <!-- Invoices -->
    <DashboardStatsItem
      v-if="userStore.hasAbilities(abilities.VIEW_INVOICE)"
      :icon-component="InvoiceIcon"
      :loading="!dashboardStore.isDashboardDataLoaded"
      route="/admin/invoices"
      :label="(dashboardStore.stats.totalInvoiceCount <= 1 ? $tc('dashboard.cards.invoices', 1) : $tc('dashboard.cards.invoices', 2))"
    >
      {{ dashboardStore.stats.totalInvoiceCount }}
    </DashboardStatsItem>

    <!-- Estimates -->
    <DashboardStatsItem
      v-if="userStore.hasAbilities(abilities.VIEW_ESTIMATE)"
      :icon-component="EstimateIcon"
      :loading="!dashboardStore.isDashboardDataLoaded"
      route="/admin/estimates"
      :label="(dashboardStore.stats.totalEstimateCount  <= 1 ? $tc('dashboard.cards.estimates', 1) : $tc('dashboard.cards.estimates', 2))"
    >
      {{ dashboardStore.stats.totalEstimateCount }}
    </DashboardStatsItem>
  </div>
</template>

<script setup>
import DollarIcon from '@/scripts/components/icons/dashboard/DollarIcon.vue'
import CustomerIcon from '@/scripts/components/icons/dashboard/CustomerIcon.vue'
import InvoiceIcon from '@/scripts/components/icons/dashboard/InvoiceIcon.vue'
import EstimateIcon from '@/scripts/components/icons/dashboard/EstimateIcon.vue'
import abilities from '@/scripts/admin/stub/abilities'
import DashboardStatsItem from './DashboardStatsItem.vue'

import { inject } from 'vue'
import { useDashboardStore } from '@/scripts/admin/stores/dashboard'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useUserStore } from '@/scripts/admin/stores/user'

const utils = inject('utils')

const dashboardStore = useDashboardStore()
const companyStore = useCompanyStore()
const userStore = useUserStore()
</script>

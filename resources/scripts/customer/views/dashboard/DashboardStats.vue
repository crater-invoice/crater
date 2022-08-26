<template>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-9 xl:gap-8">
    <!-- Amount Due -->
    <DashboardStatsItem
      :icon-component="DollarIcon"
      :loading="!globalStore.getDashboardDataLoaded"
      :route="{ name: 'invoices.dashboard' }"
      :large="true"
      :label="$t('dashboard.cards.due_amount')"
    >
      <BaseFormatMoney
        :amount="dashboardStore.totalDueAmount"
        :currency="globalStore.currency"
      />
    </DashboardStatsItem>

    <!-- Invoices -->
    <DashboardStatsItem
      :icon-component="InvoiceIcon"
      :loading="!globalStore.getDashboardDataLoaded"
      :route="{ name: 'invoices.dashboard' }"
      :label="(dashboardStore.invoiceCount <= 1 ? $tc('dashboard.cards.invoices', 1) : $tc('dashboard.cards.invoices', 2))"
    >
      {{ dashboardStore.invoiceCount }}
    </DashboardStatsItem>

    <!-- Estimates -->
    <DashboardStatsItem
      :icon-component="EstimateIcon"
      :loading="!globalStore.getDashboardDataLoaded"
      :route="{ name: 'estimates.dashboard' }"
      :label="(dashboardStore.estimateCount <= 1 ? $tc('dashboard.cards.estimates', 1) : $tc('dashboard.cards.estimates', 2))"
    >
      {{ dashboardStore.estimateCount }}
    </DashboardStatsItem>

    <!-- Payments -->

    <DashboardStatsItem
      :icon-component="PaymentIcon"
      :loading="!globalStore.getDashboardDataLoaded"
      :route="{ name: 'payments.dashboard' }"
      :label="(dashboardStore.paymentCount <= 1 ? $tc('dashboard.cards.payments', 1 ) : $tc('dashboard.cards.payments', 2))"
    >
      {{ dashboardStore.paymentCount }}
    </DashboardStatsItem>
  </div>
</template>

<script setup>
import { inject } from 'vue'
import DollarIcon from '@/scripts/components/icons/dashboard/DollarIcon.vue'
import InvoiceIcon from '@/scripts/components/icons/dashboard/InvoiceIcon.vue'
import PaymentIcon from '@/scripts/components/icons/dashboard/PaymentIcon.vue'
import EstimateIcon from '@/scripts/components/icons/dashboard/EstimateIcon.vue'

import { useGlobalStore } from '@/scripts/customer/stores/global'
import { useDashboardStore } from '@/scripts/customer/stores/dashboard'
import DashboardStatsItem from '@/scripts/customer/views/dashboard/DashboardStatsItem.vue'
//store

const utils = inject('utils')
const globalStore = useGlobalStore()
const dashboardStore = useDashboardStore()
dashboardStore.loadData()
</script>

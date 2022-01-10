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
      :label="$t('dashboard.cards.invoices')"
    >
      {{ dashboardStore.invoiceCount }}
    </DashboardStatsItem>

    <!-- Estimates -->
    <DashboardStatsItem
      :icon-component="EstimateIcon"
      :loading="!globalStore.getDashboardDataLoaded"
      :route="{ name: 'estimates.dashboard' }"
      :label="$t('dashboard.cards.estimates')"
    >
      {{ dashboardStore.estimateCount }}
    </DashboardStatsItem>

    <!-- Payments -->

    <DashboardStatsItem
      :icon-component="PaymentIcon"
      :loading="!globalStore.getDashboardDataLoaded"
      :route="{ name: 'payments.dashboard' }"
      :label="$t('dashboard.cards.payments')"
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

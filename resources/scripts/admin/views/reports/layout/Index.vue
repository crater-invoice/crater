<template>
  <BasePage>
    <BasePageHeader :title="$tc('reports.report', 2)">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="/admin/dashboard" />
        <BaseBreadcrumbItem
          :title="$tc('reports.report', 2)"
          to="/admin/reports"
          active
        />
      </BaseBreadcrumb>
      <template #actions>
        <BaseButton variant="primary" class="ml-4" @click="onDownload">
          <template #left="slotProps">
            <BaseIcon name="DownloadIcon" :class="slotProps.class" />
          </template>
          {{ $t('reports.download_pdf') }}
        </BaseButton>
      </template>
    </BasePageHeader>

    <!-- Tabs -->
    <BaseTabGroup class="p-2">
      <BaseTab
        :title="$t('reports.sales.sales')"
        tab-panel-container="px-0 py-0"
      >
        <SalesReport ref="report" />
      </BaseTab>
      <BaseTab
        :title="$t('reports.profit_loss.profit_loss')"
        tab-panel-container="px-0 py-0"
      >
        <ProfitLossReport ref="report" />
      </BaseTab>
      <BaseTab
        :title="$t('reports.expenses.expenses')"
        tab-panel-container="px-0 py-0"
      >
        <ExpenseReport ref="report" />
      </BaseTab>
      <BaseTab
        :title="$t('reports.taxes.taxes')"
        tab-panel-container="px-0 py-0"
      >
        <TaxReport ref="report" />
      </BaseTab>
    </BaseTabGroup>
  </BasePage>
</template>

<script setup>
import { ref } from 'vue'
import SalesReport from '../SalesReports.vue'
import ExpenseReport from '../ExpensesReport.vue'
import ProfitLossReport from '../ProfitLossReport.vue'
import TaxReport from '../TaxReport.vue'
import { useGlobalStore } from '@/scripts/admin/stores/global'

const globalStore = useGlobalStore()

function onDownload() {
  globalStore.downloadReport()
}
</script>

<template>
  <base-page class="profit-loss-reports reports">
    <sw-page-header :title="$tc('reports.report', 2)">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          :title="$t('general.home')"
          :to="`/admin/dashboard`"
        />
        <sw-breadcrumb-item
          :title="$tc('reports.report', 2)"
          :to="`/admin/reports`"
          active
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button size="lg" variant="primary" @click="onDownload()">
          <download-icon class="h-5 mr-1 -ml-2" />
          {{ $t('reports.download_pdf') }}
        </sw-button>
      </template>
    </sw-page-header>
    <div class="row">
      <!-- Tabs -->
      <sw-tabs>
        <sw-tab-item
          :title="$t('reports.sales.sales')"
          route="/admin/reports/sales"
        >
        </sw-tab-item>

        <sw-tab-item
          :title="$t('reports.profit_loss.profit_loss')"
          route="/admin/reports/profit-loss"
        >
        </sw-tab-item>

        <sw-tab-item
          :title="$t('reports.expenses.expenses')"
          route="/admin/reports/expenses"
        >
        </sw-tab-item>

        <sw-tab-item
          :title="$t('reports.taxes.taxes')"
          route="/admin/reports/taxes"
        >
        </sw-tab-item>
      </sw-tabs>
    </div>
    <transition name="fade" mode="out-in">
      <div
        v-if="activeTab === 'SALES' || 'PROFIT_LOSS' || 'EXPENSES' || 'TAXES'"
      >
        <router-view ref="report" />
      </div>
    </transition>
  </base-page>
</template>

<script>
import { DownloadIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    DownloadIcon,
  },
  data() {
    return {
      activeTab: 'SALES',
    }
  },
  watch: {
    '$route.path'(newValue) {
      if (newValue === '/admin/reports') {
        this.$router.push('/admin/reports/sales')
      }
    },
  },
  created() {
    if (this.$route.path === '/admin/reports') {
      this.$router.push('/admin/reports/sales')
    }
  },
  methods: {
    onDownload() {
      this.$refs.report.downloadReport()
    },
    setActiveTab(val) {
      this.activeTab = val
    },
  },
}
</script>

<style scoped>
.tab {
  padding: 0 !important;
}

.tab-link {
  padding: 10px 30px;
  display: block;
}
</style>

<template>
  <div class="profit-loss-reports reports main-content">
    <div class="page-header">
      <h3 class="page-title"> {{ $tc('reports.report', 2) }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link
            slot="item-title"
            to="dashboard">
            {{ $t('general.home') }}
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link
            slot="item-title"
            to="/admin/reports/sales">
            {{ $tc('reports.report', 2) }}
          </router-link>
        </li>
      </ol>
      <div class="page-actions row">
        <div class="col-xs-2">
          <base-button icon="download" size="large" color="theme" @click="onDownload()">
            {{ $t('reports.download_pdf') }}
          </base-button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <!-- Tabs -->
        <ul class="tabs">
          <li class="tab">
            <router-link class="tab-link" to="/admin/reports/sales">{{ $t('reports.sales.sales') }}</router-link>
          </li>
          <li class="tab">
            <router-link class="tab-link" to="/admin/reports/profit-loss">{{ $t('reports.profit_loss.profit_loss') }}</router-link>
          </li>
          <li class="tab">
            <router-link class="tab-link" to="/admin/reports/expenses">{{ $t('reports.expenses.expenses') }}</router-link>
          </li>
          <li class="tab">
            <router-link class="tab-link" to="/admin/reports/taxes">{{ $t('reports.taxes.taxes') }}</router-link>
          </li>
        </ul>
      </div>
    </div>
    <transition
      name="fade"
      mode="out-in">
      <router-view ref="report"/>
    </transition>
  </div>
</template>

<script>
export default {
  watch: {
    '$route.path' (newValue) {
      if (newValue === '/admin/reports') {
        this.$router.push('/admin/reports/sales')
      }
    }
  },
  created () {
    if (this.$route.path === '/admin/reports') {
      this.$router.push('/admin/reports/sales')
    }
  },
  methods: {
    onDownload () {
      this.$refs.report.downloadReport()
    }
  }

}
</script>

<style scoped>
.tab {
  padding: 0 !important;
}

.tab-link {
  padding: 10px 30px;
  display: block
}
</style>

<template>
  <base-page>
    <dashboard-stats />
    <dashboard-chart />
    <dashboard-table />
  </base-page>
</template>

<script>
import DashboardStats from '../dashboard/DashboardStats'
import DashboardChart from '../dashboard/DashboardChart'
import DashboardTable from '../dashboard/DashboardTable'

export default {
  components: {
    DashboardStats,
    DashboardChart,
    DashboardTable,
  },
}
</script>

<script setup>
import DashboardStats from '../dashboard/DashboardStats.vue'
import DashboardChart from '../dashboard/DashboardChart.vue'
import DashboardTable from '../dashboard/DashboardTable.vue'
import { useUserStore } from '@/scripts/admin/stores/user'
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const userStore = useUserStore()
const router = useRouter()

onMounted(() => {
  if (route.meta.ability && !userStore.hasAbilities(route.meta.ability)) {
    router.push({ name: 'account.settings' })
  } else if (route.meta.isOwner && !userStore.currentUser.is_owner) {
    router.push({ name: 'account.settings' })
  }
})
</script>

<template>
  <BasePage>
    <DashboardStats />
    <DashboardChart />
    <DashboardTable />
  </BasePage>
</template>

<template>
  <div v-if="isAppLoaded" class="h-full">
    <NotificationRoot />

    <SiteHeader />

    <SiteSidebar />

    <ExchangeRateBulkUpdateModal />

    <main
      class="
        mt-16
        pb-16
        h-screen h-screen-ios
        overflow-y-auto
        md:pl-56
        xl:pl-64
        min-h-0
      "
    >
      <router-view />
    </main>
  </div>

  <BaseGlobalLoader v-else />
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { useGlobalStore } from '@/scripts/stores/global'
import { onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/scripts/stores/user'
import { useModalStore } from '@/scripts/stores/modal'
import { useExchangeRateStore } from '@/scripts/stores/exchange-rate'
import { useCompanyStore } from '@/scripts/stores/company'

import SiteHeader from '@/scripts/layouts/partials/TheSiteHeader.vue'
import SiteSidebar from '@/scripts/layouts/partials/TheSiteSidebar.vue'
import NotificationRoot from '@/scripts/components/notifications/NotificationRoot.vue'
import ExchangeRateBulkUpdateModal from '@/scripts/components/modal-components/ExchangeRateBulkUpdateModal.vue'

const globalStore = useGlobalStore()
const route = useRoute()
const userStore = useUserStore()
const router = useRouter()
const modalStore = useModalStore()
const { t } = useI18n()
const exchangeRateStore = useExchangeRateStore()
const companyStore = useCompanyStore()

const isAppLoaded = computed(() => {
  return globalStore.isAppLoaded
})

onMounted(() => {
  globalStore.bootstrap().then((res) => {
    if (route.meta.ability && !userStore.hasAbilities(route.meta.ability)) {
      router.push({ name: 'account.settings' })
    } else if (route.meta.isOwner && !userStore.currentUser.is_owner) {
      router.push({ name: 'account.settings' })
    }

    if (
      res.data.current_company_settings.bulk_exchange_rate_configured === 'NO'
    ) {
      exchangeRateStore.fetchBulkCurrencies().then((res) => {
        if (res.data.currencies.length) {
          modalStore.openModal({
            componentName: 'ExchangeRateBulkUpdateModal',
            size: 'sm',
          })
        } else {
          let data = {
            settings: {
              bulk_exchange_rate_configured: 'YES',
            },
          }
          companyStore.updateCompanySettings({
            data,
          })
        }
      })
    }
  })
})
</script>

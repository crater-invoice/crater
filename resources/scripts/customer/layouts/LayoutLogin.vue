<template>
  <div
    class="flex flex-col justify-center h-full min-h-screen py-12 overflow-hidden bg-gray-100 sm:px-6 lg:px-8 dark:bg-gray-800/80 dark:text-white font-base "
  >
    <NotificationRoot />

    <div class="px-4 sm:mx-auto sm:w-full sm:max-w-md sm:px-0">
      <MainLogo
        v-if="!customerLogo"
        class="block w-48 h-auto max-w-full mx-auto text-primary-400"
      />
      <img
        v-else
        :src="customerLogo"
        class="block w-48 h-auto max-w-full mx-auto text-primary-400"
      />
    </div>

    <div class="px-4 mt-8 sm:mx-auto sm:w-full sm:max-w-md sm:px-0">
      <div class="px-4 py-8 bg-white rounded-lg shadow sm:px-10 dark:from-gray-700/70 dark:to-gray-800/70 dark:bg-transparent dark:backdrop-blur-xl dark:shadow-glass dark:border dark:border-white/10">
      <BaseDarkHighlight/>
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import NotificationRoot from '@/scripts/components/notifications/NotificationRoot.vue'
import MainLogo from '@/scripts/components/icons/MainLogo.vue'

const customerLogo = computed(() => {
  if (window.customer_logo) {
    return window.customer_logo
  }

  return false
})
</script>

<template>
  <div class="h-screen overflow-y-auto text-base dark:bg-gray-800/80 dark:text-white">
    <NotificationRoot />

    <div class="container px-4 mx-auto">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import NotificationRoot from '@/scripts/components/notifications/NotificationRoot.vue'
</script>

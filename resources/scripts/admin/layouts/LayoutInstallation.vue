<template>
  <div class="h-screen overflow-y-auto text-base">
    <NotificationRoot />

    <div class="container mx-auto px-4">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import NotificationRoot from '@/scripts/components/notifications/NotificationRoot.vue'
</script>

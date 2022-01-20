<template>
  <div
    class="
      fixed
      inset-0
      z-50
      flex flex-col
      items-end
      justify-start
      w-full
      px-4
      py-6
      pointer-events-none
      sm:p-6
    "
  >
    <transition-group
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <NotificationItem
        v-for="notification in notifications"
        :key="notification.id"
        :notification="notification"
      />
    </transition-group>
  </div>
</template>

<script>
import NotificationItem from './NotificationItem.vue'
import { computed } from 'vue'
import { useNotificationStore } from '@/scripts/stores/notification'
export default {
  components: {
    NotificationItem,
  },
  setup() {
    const notificationStore = useNotificationStore()

    const notifications = computed(() => {
      return notificationStore.notifications
    })

    return {
      notifications,
    }
  },
}
</script>

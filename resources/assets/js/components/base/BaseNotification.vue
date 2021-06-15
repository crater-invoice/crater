<template>
  <transition
    enter-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2 "
    enter-active-class="transition duration-300 ease-out transform"
    enter-to-class="duration-300 translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition duration-100 ease-in"
    leave-class="duration-200 opacity-100"
    leave-to-class="duration-200 opacity-0"
  >
    <div
      v-if="notificationActive"
      class="fixed inset-0 z-50 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end"
    >
      <div
        :class="success || info ? 'bg-white' : 'bg-red-50'"
        class="w-full max-w-sm rounded-lg shadow-lg cursor-pointer pointer-events-auto"
        @click="hideNotification"
      >
        <div class="overflow-hidden rounded-lg shadow-xs">
          <div class="p-4">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <svg
                  v-if="success"
                  class="w-6 h-6 text-green-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <exclamation-circle-icon
                  v-if="info"
                  class="w-6 h-6 text-blue-400"
                />
                <svg
                  v-if="error"
                  class="w-6 h-6 text-red-400"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="flex-1 w-0 ml-3">
                <p
                  :class="`text-sm leading-5 font-medium ${
                    success || info ? 'text-gray-900' : 'text-red-800'
                  }`"
                >
                  {{
                    notificationTitle ? notificationTitle : success ? 'Success!' : 'Error'
                  }}
                </p>
                <p
                  :class="`mt-1 text-sm leading-5 ${
                    success || info ? 'text-gray-500' : 'text-red-700'
                  }`"
                >
                  {{
                    notificationMessage
                      ? notificationMessage
                      : success
                      ? 'Successful'
                      : 'Something went wrong'
                  }}
                </p>
              </div>
              <div class="flex flex-shrink-0">
                <button
                  :class="
                    success || info
                      ? ' text-gray-400 focus:text-gray-500'
                      : 'text-red-400 focus:text-red-500'
                  "
                  class="inline-flex w-5 h-5 transition duration-150 ease-in-out focus:outline-none"
                  @click="hideNotification"
                >
                  <x-icon />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { XIcon } from '@vue-hero-icons/outline'
import { ExclamationCircleIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    XIcon,
    ExclamationCircleIcon,
  },
  data() {
    return {
      hasFocus: false,
    }
  },
  computed: {
    ...mapGetters('notification', [
      'notificationActive',
      'notificationTitle',
      'notificationType',
      'notificationAutoHide',
      'notificationMessage',
    ]),
    success() {
      return this.notificationType.toLowerCase() === 'success'
    },
    error() {
      return this.notificationType.toLowerCase() === 'error'
    },
    info() {
      return this.notificationType.toLowerCase() === 'info'
    },
  },
  watch: {
    notificationActive(val) {
      if (val && this.notificationAutoHide) {
        window.setTimeout(this.hideNotification, 5000)
      }
    },
  },
  mounted() {
    if (this.notificationActive && this.notificationAutoHide) {
      window.setTimeout(this.hideNotification, 5000)
    }
  },
  methods: {
    ...mapActions('notification', ['showNotification', 'hideNotification']),
  },
}
</script>

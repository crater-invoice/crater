<template>
  <div
    class="
      relative
      flex
      px-4
      py-2
      rounded-lg
      bg-opacity-40 bg-gray-300
      whitespace-nowrap
      flex-col
      mt-1
    "
  >
    <span
      ref="publicUrl"
      class="
        pr-10
        text-sm
        font-medium
        text-black
        truncate
        select-all select-color
      "
    >
      {{ token }}
    </span>
    <svg
      v-tooltip="{ content: $t('general.copy_to_clipboard') }"
      class="
        absolute
        right-0
        h-full
        inset-y-0
        cursor-pointer
        focus:outline-none
        text-primary-500
      "
      width="37"
      viewBox="0 0 37 37"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      @click="copyUrl"
    >
      <rect width="37" height="37" rx="10" fill="currentColor" />
      <path
        d="M16 10C15.7348 10 15.4804 10.1054 15.2929 10.2929C15.1054 10.4804 15 10.7348 15 11C15 11.2652 15.1054 11.5196 15.2929 11.7071C15.4804 11.8946 15.7348 12 16 12H18C18.2652 12 18.5196 11.8946 18.7071 11.7071C18.8946 11.5196 19 11.2652 19 11C19 10.7348 18.8946 10.4804 18.7071 10.2929C18.5196 10.1054 18.2652 10 18 10H16Z"
        fill="white"
      />
      <path
        d="M11 13C11 12.4696 11.2107 11.9609 11.5858 11.5858C11.9609 11.2107 12.4696 11 13 11C13 11.7956 13.3161 12.5587 13.8787 13.1213C14.4413 13.6839 15.2044 14 16 14H18C18.7956 14 19.5587 13.6839 20.1213 13.1213C20.6839 12.5587 21 11.7956 21 11C21.5304 11 22.0391 11.2107 22.4142 11.5858C22.7893 11.9609 23 12.4696 23 13V19H18.414L19.707 17.707C19.8892 17.5184 19.99 17.2658 19.9877 17.0036C19.9854 16.7414 19.8802 16.4906 19.6948 16.3052C19.5094 16.1198 19.2586 16.0146 18.9964 16.0123C18.7342 16.01 18.4816 16.1108 18.293 16.293L15.293 19.293C15.1055 19.4805 15.0002 19.7348 15.0002 20C15.0002 20.2652 15.1055 20.5195 15.293 20.707L18.293 23.707C18.4816 23.8892 18.7342 23.99 18.9964 23.9877C19.2586 23.9854 19.5094 23.8802 19.6948 23.6948C19.8802 23.5094 19.9854 23.2586 19.9877 22.9964C19.99 22.7342 19.8892 22.4816 19.707 22.293L18.414 21H23V24C23 24.5304 22.7893 25.0391 22.4142 25.4142C22.0391 25.7893 21.5304 26 21 26H13C12.4696 26 11.9609 25.7893 11.5858 25.4142C11.2107 25.0391 11 24.5304 11 24V13ZM23 19H25C25.2652 19 25.5196 19.1054 25.7071 19.2929C25.8946 19.4804 26 19.7348 26 20C26 20.2652 25.8946 20.5196 25.7071 20.7071C25.5196 20.8946 25.2652 21 25 21H23V19Z"
        fill="white"
      />
    </svg>
  </div>
</template>

<script setup>
import { useNotificationStore } from '@/scripts/stores/notification'
import { ref } from 'vue'
const notificationStore = useNotificationStore()
import { useI18n } from 'vue-i18n'
const publicUrl = ref('')
const { t } = useI18n()

const props = defineProps({
  token: {
    type: String,
    default: null,
    required: true,
  },
})

function selectText(element) {
  let range
  if (document.selection) {
    // IE
    range = document.body.createTextRange()
    range.moveToElementText(element)
    range.select()
  } else if (window.getSelection) {
    range = document.createRange()
    range.selectNode(element)
    window.getSelection().removeAllRanges()
    window.getSelection().addRange(range)
  }
}

function copyUrl() {
  selectText(publicUrl.value)
  document.execCommand('copy')

  notificationStore.showNotification({
    type: 'success',
    message: t('general.copied_url_clipboard'),
  })
}
</script>

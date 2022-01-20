<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton v-if="route.name === 'users.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit user  -->
    <router-link :to="`/admin/users/${row.id}/edit`">
      <BaseDropdownItem>
        <BaseIcon
          name="PencilIcon"
          class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
        />
        {{ $t('general.edit') }}
      </BaseDropdownItem>
    </router-link>

    <!-- delete user  -->
    <BaseDropdownItem @click="removeUser(row.id)">
      <BaseIcon
        name="TrashIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.delete') }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { useDialogStore } from '@/scripts/stores/dialog'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useRoute, useRouter } from 'vue-router'
import { inject } from 'vue'
import { useUsersStore } from '@/scripts/admin/stores/users'

const props = defineProps({
  row: {
    type: Object,
    default: null,
  },
  table: {
    type: Object,
    default: null,
  },
  loadData: {
    type: Function,
    default: null,
  },
})

const dialogStore = useDialogStore()
const notificationStore = useNotificationStore()
const { t } = useI18n()
const userStore = useUserStore()
const route = useRoute()
const router = useRouter()
const usersStore = useUsersStore()

const $utils = inject('utils')

function removeUser(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('users.confirm_delete', 1),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then((res) => {
      if (res) {
        usersStore.deleteUser({ ids: [id] }).then((res) => {
          if (res) {
            props.loadData && props.loadData()
          }
        })
      }
    })
}
</script>

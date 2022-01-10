<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton v-if="route.name === 'roles.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit role  -->
    <BaseDropdownItem
      v-if="userStore.currentUser.is_owner"
      @click="editRole(row.id)"
    >
      <BaseIcon
        name="PencilIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.edit') }}
    </BaseDropdownItem>

    <!-- delete role  -->
    <BaseDropdownItem
      v-if="userStore.currentUser.is_owner"
      @click="removeRole(row.id)"
    >
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
import { useRoleStore } from '@/scripts/admin/stores/role'
import { useRoute, useRouter } from 'vue-router'
import { inject } from 'vue'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useModalStore } from '@/scripts/stores/modal'

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
const roleStore = useRoleStore()
const route = useRoute()
const userStore = useUserStore()
const modalStore = useModalStore()

const $utils = inject('utils')

async function editRole(id) {
  Promise.all([
    await roleStore.fetchAbilities(),
    await roleStore.fetchRole(id),
  ]).then(() => {
    modalStore.openModal({
      title: t('settings.roles.edit_role'),
      componentName: 'RolesModal',
      size: 'lg',
      refreshData: props.loadData,
    })
  })
}

async function removeRole(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.roles.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await roleStore.deleteRole(id).then((response) => {
          if (response.data) {
            props.loadData && props.loadData()
          }
        })
      }
    })
}
</script>

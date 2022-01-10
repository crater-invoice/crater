<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton v-if="route.name === 'notes.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit note  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.MANAGE_NOTE)"
      @click="editNote(row.id)"
    >
      <BaseIcon
        name="PencilIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.edit') }}
    </BaseDropdownItem>

    <!-- delete note  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.MANAGE_NOTE)"
      @click="removeNote(row.id)"
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
import { useNotesStore } from '@/scripts/admin/stores/note'
import { useRoute } from 'vue-router'
import { inject } from 'vue'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useModalStore } from '@/scripts/stores/modal'
import abilities from '@/scripts/admin/stub/abilities'

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
const noteStore = useNotesStore()
const route = useRoute()
const userStore = useUserStore()
const modalStore = useModalStore()

const $utils = inject('utils')

function editNote(data) {
  noteStore.fetchNote(data)
  modalStore.openModal({
    title: t('settings.customization.notes.edit_note'),
    componentName: 'NoteModal',
    size: 'md',
    refreshData: props.loadData,
  })
}

function removeNote(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.customization.notes.note_confirm_delete'),
      yesLabel: t('general.yes'),
      noLabel: t('general.no'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async () => {
      let response = await noteStore.deleteNote(id)
      if (response.data.success) {
        notificationStore.showNotification({
          type: 'success',
          message: t('settings.customization.notes.deleted_message'),
        })
      } else {
        notificationStore.showNotification({
          type: 'error',
          message: t('settings.customization.notes.already_in_use'),
        })
      }
      props.loadData && props.loadData()
    })
}
</script>

<template>
  <BaseDropdown>
    <template #activator>
      <BaseIcon name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit customField  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.EDIT_CUSTOM_FIELDS)"
      @click="editCustomField(row.id)"
    >
      <BaseIcon
        name="PencilIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.edit') }}
    </BaseDropdownItem>

    <!-- delete customField  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.DELETE_CUSTOM_FIELDS)"
      @click="removeCustomField(row.id)"
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
import { useCustomFieldStore } from '@/scripts/admin/stores/custom-field'
import { useRoute, useRouter } from 'vue-router'
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
const customFieldStore = useCustomFieldStore()
const route = useRoute()
const userStore = useUserStore()
const modalStore = useModalStore()

const $utils = inject('utils')

async function editCustomField(id) {
  await customFieldStore.fetchCustomField(id)

  modalStore.openModal({
    title: t('settings.custom_fields.edit_custom_field'),
    componentName: 'CustomFieldModal',
    size: 'sm',
    data: id,
    refreshData: props.loadData,
  })
}

async function removeCustomField(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.custom_fields.custom_field_confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await customFieldStore.deleteCustomFields(id)
        props.loadData && props.loadData()
      }
    })
}
</script>

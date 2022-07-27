<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton
        v-if="route.name === 'ItemCategories.view'"
        variant="primary"
      >
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit itemCategory  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.EDIT_ITEM)"
      @click="editItemCategory(row.id)"
    >
      <BaseIcon
        name="PencilIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ $t('general.edit') }}
    </BaseDropdownItem>

    <!-- delete itemCategory  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.DELETE_ITEM)"
      @click="removeItemCategory(row.id)"
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
import { useItemCategoryStore } from '@/scripts/admin/stores/item-category'
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
const itemCategoryStore = useItemCategoryStore()
const route = useRoute()
const userStore = useUserStore()
const modalStore = useModalStore()

const $utils = inject('utils')

function editItemCategory(data) {
  itemCategoryStore.fetchCategory(data)
  modalStore.openModal({
    title: t('settings.item_category.edit_category'),
    componentName: 'ItemCategoryModal',
    refreshData: props.loadData,
    size: 'sm',
  })
}

function removeItemCategory(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.item_category.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async () => {
      let response = await itemCategoryStore.deleteCategory(id)
      if (response.data.success) {
        props.loadData && props.loadData()
        return true
      }
      props.loadData && props.loadData()
    })
}
</script>

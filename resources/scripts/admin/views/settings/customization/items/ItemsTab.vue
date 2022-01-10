<template>
  <ItemUnitModal />

  <div class="flex flex-wrap justify-end mt-2 lg:flex-nowrap">
    <BaseButton variant="primary-outline" @click="addItemUnit">
      <template #left="slotProps">
        <BaseIcon :class="slotProps.class" name="PlusIcon" />
      </template>
      {{ $t('settings.customization.items.add_item_unit') }}
    </BaseButton>
  </div>

  <BaseTable ref="table" class="mt-10" :data="fetchData" :columns="columns">
    <template #cell-actions="{ row }">
      <BaseDropdown>
        <template #activator>
          <div class="inline-block">
            <BaseIcon name="DotsHorizontalIcon" class="text-gray-500" />
          </div>
        </template>

        <BaseDropdownItem @click="editItemUnit(row)">
          <BaseIcon
            name="PencilIcon"
            class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
          />

          {{ $t('general.edit') }}
        </BaseDropdownItem>
        <BaseDropdownItem @click="removeItemUnit(row)">
          <BaseIcon
            name="TrashIcon"
            class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
          />
          {{ $t('general.delete') }}
        </BaseDropdownItem>
      </BaseDropdown>
    </template>
  </BaseTable>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useItemStore } from '@/scripts/admin/stores/item'
import { useModalStore } from '@/scripts/stores/modal'
import { useDialogStore } from '@/scripts/stores/dialog'
import ItemUnitModal from '@/scripts/admin/components/modal-components/ItemUnitModal.vue'

const { t } = useI18n()
const table = ref(null)

const itemStore = useItemStore()
const modalStore = useModalStore()
const dialogStore = useDialogStore()

const columns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.customization.items.unit_name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'actions',
      label: '',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

async function fetchData({ page, filter, sort }) {
  let data = {
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }
  let response = await itemStore.fetchItemUnits(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: 5,
    },
  }
}

async function addItemUnit() {
  modalStore.openModal({
    title: t('settings.customization.items.add_item_unit'),
    componentName: 'ItemUnitModal',
    refreshData: table.value.refresh,
    size: 'sm',
  })
}

async function editItemUnit(row) {
  itemStore.fetchItemUnit(row.data.id)
  modalStore.openModal({
    title: t('settings.customization.items.edit_item_unit'),
    componentName: 'ItemUnitModal',
    id: row.data.id,
    data: row.data,
    refreshData: table.value && table.value.refresh,
  })
}

function removeItemUnit(row) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.customization.items.item_unit_confirm_delete'),
      yesLabel: t('general.yes'),
      noLabel: t('general.no'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        await itemStore.deleteItemUnit(row.data.id)
        table.value && table.value.refresh()
      }
    })
}
</script>

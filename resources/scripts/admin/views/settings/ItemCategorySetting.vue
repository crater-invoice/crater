<template>
  <ItemCategoryModal />

  <BaseSettingCard
    :title="$t('settings.item_category.title')"
    :description="$t('settings.item_category.description')"
  >
    <template #action>
      <BaseButton
        variant="primary-outline"
        type="button"
        @click="openCategoryModal"
      >
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />
        </template>

        {{ $t('settings.item_category.add_new_category') }}
      </BaseButton>
    </template>

    <BaseTable
      ref="table"
      :data="fetchData"
      :columns="ItemCategoryColumns"
      class="mt-16"
    >
      <template #cell-description="{ row }">
        <div class="w-64">
          <p class="truncate">{{ row.data.number }}</p>
        </div>
      </template>

      <template #cell-actions="{ row }">
        <ItemCategoryIndexDropdown
          :row="row.data"
          :table="table"
          :load-data="refreshTable"
        />
      </template>
    </BaseTable>
  </BaseSettingCard>
</template>

<script setup>
import { useDialogStore } from '@/scripts/stores/dialog'
import { useItemCategoryStore } from '@/scripts/admin/stores/item-category'
import { useModalStore } from '@/scripts/stores/modal'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import ItemCategoryIndexDropdown from '@/scripts/admin/components/dropdowns/ItemCategoryIndexDropdown.vue'
import ItemCategoryModal from '@/scripts/admin/components/modal-components/ItemCategoryModal.vue'

const categoryStore = useItemCategoryStore()
const dialogStore = useDialogStore()
const modalStore = useModalStore()

const { t } = useI18n()

const table = ref(null)

const ItemCategoryColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.item_category.category_name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'number',
      label: t('settings.item_category.category_number'),
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
    orderByField: sort.fieldName || 'number',
    orderBy: sort.order || 'asc',
    page,
  }

  let response = await categoryStore.fetchCategories(data)
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

function openCategoryModal() {
  modalStore.openModal({
    title: t('settings.item_category.add_category'),
    componentName: 'ItemCategoryModal',
    size: 'sm',
    refreshData: table.value && table.value.refresh,
  })
}

async function refreshTable() {
  table.value && table.value.refresh()
}
</script>

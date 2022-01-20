<template>
  <BaseSettingCard
    :title="$t('settings.menu_title.custom_fields')"
    :description="$t('settings.custom_fields.section_description')"
  >
    <template #action>
      <BaseButton
        v-if="userStore.hasAbilities(abilities.CREATE_CUSTOM_FIELDS)"
        variant="primary-outline"
        @click="addCustomField"
      >
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />

          {{ $t('settings.custom_fields.add_custom_field') }}
        </template>
      </BaseButton>
    </template>

    <CustomFieldModal />

    <BaseTable
      ref="table"
      :data="fetchData"
      :columns="customFieldsColumns"
      class="mt-16"
    >
      <template #cell-name="{ row }">
        {{ row.data.name }}
        <span class="text-xs text-gray-500"> ({{ row.data.slug }})</span>
      </template>

      <template #cell-is_required="{ row }">
        <BaseBadge
          :bg-color="
            utils.getBadgeStatusColor(row.data.is_required ? 'YES' : 'NO')
              .bgColor
          "
          :color="
            utils.getBadgeStatusColor(row.data.is_required ? 'YES' : 'NO').color
          "
        >
          {{
            row.data.is_required
              ? $t('settings.custom_fields.yes')
              : $t('settings.custom_fields.no').replace('_', ' ')
          }}
        </BaseBadge>
      </template>

      <template
        v-if="
          userStore.hasAbilities([
            abilities.DELETE_CUSTOM_FIELDS,
            abilities.EDIT_CUSTOM_FIELDS,
          ])
        "
        #cell-actions="{ row }"
      >
        <CustomFieldDropdown
          :row="row.data"
          :table="table"
          :load-data="refreshTable"
        />
      </template>
    </BaseTable>
  </BaseSettingCard>
</template>

<script setup>
import { computed, ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useModalStore } from '@/scripts/stores/modal'
import { useCustomFieldStore } from '@/scripts/admin/stores/custom-field'
import { useUserStore } from '@/scripts/admin/stores/user'
import CustomFieldDropdown from '@/scripts/admin/components/dropdowns/CustomFieldIndexDropdown.vue'
import CustomFieldModal from '@/scripts/admin/components/modal-components/custom-fields/CustomFieldModal.vue'
import abilities from '@/scripts/admin/stub/abilities'

const modalStore = useModalStore()
const customFieldStore = useCustomFieldStore()
const userStore = useUserStore()

const utils = inject('utils')
const { t } = useI18n()

const table = ref(null)

const customFieldsColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.custom_fields.name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'model_type',
      label: t('settings.custom_fields.model'),
    },
    {
      key: 'type',
      label: t('settings.custom_fields.type'),
    },
    {
      key: 'is_required',
      label: t('settings.custom_fields.required'),
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

  let response = await customFieldStore.fetchCustomFields(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      limit: 5,
      totalCount: response.data.meta.total,
    },
  }
}

function addCustomField() {
  modalStore.openModal({
    title: t('settings.custom_fields.add_custom_field'),
    componentName: 'CustomFieldModal',
    size: 'sm',
    refreshData: table.value && table.value.refresh,
  })
}

async function refreshTable() {
  table.value && table.value.refresh()
}
</script>

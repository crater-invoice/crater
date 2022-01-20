<template>
  <BaseSettingCard
    :title="$t('settings.tax_types.title')"
    :description="$t('settings.tax_types.description')"
  >
    <TaxTypeModal />

    <template v-if="userStore.hasAbilities(abilities.CREATE_TAX_TYPE)" #action>
      <BaseButton type="submit" variant="primary-outline" @click="openTaxModal">
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />
        </template>
        {{ $t('settings.tax_types.add_new_tax') }}
      </BaseButton>
    </template>

    <BaseTable
      ref="table"
      class="mt-16"
      :data="fetchData"
      :columns="taxTypeColumns"
    >
      <template #cell-compound_tax="{ row }">
        <BaseBadge
          :bg-color="
            utils.getBadgeStatusColor(row.data.compound_tax ? 'YES' : 'NO')
              .bgColor
          "
          :color="
            utils.getBadgeStatusColor(row.data.compound_tax ? 'YES' : 'NO')
              .color
          "
        >
          {{ row.data.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
        </BaseBadge>
      </template>

      <template #cell-percent="{ row }"> {{ row.data.percent }} % </template>

      <template v-if="hasAtleastOneAbility()" #cell-actions="{ row }">
        <TaxTypeDropdown
          :row="row.data"
          :table="table"
          :load-data="refreshTable"
        />
      </template>
    </BaseTable>
    <div v-if="userStore.currentUser.is_owner">
      <BaseDivider class="mt-8 mb-2" />

      <BaseSwitchSection
        v-model="taxPerItemField"
        :disabled="salesTaxEnabled"
        :title="$t('settings.tax_types.tax_per_item')"
        :description="$t('settings.tax_types.tax_setting_description')"
      />
    </div>
  </BaseSettingCard>
</template>

<script setup>
import { useTaxTypeStore } from '@/scripts/admin/stores/tax-type'
import { useModalStore } from '@/scripts/stores/modal'
import { computed, reactive, ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useModuleStore } from '@/scripts/admin/stores/module'

import TaxTypeDropdown from '@/scripts/admin/components/dropdowns/TaxTypeIndexDropdown.vue'
import TaxTypeModal from '@/scripts/admin/components/modal-components/TaxTypeModal.vue'
import abilities from '@/scripts/admin/stub/abilities'

const { t } = useI18n()
const utils = inject('utils')

const companyStore = useCompanyStore()
const taxTypeStore = useTaxTypeStore()
const modalStore = useModalStore()
const userStore = useUserStore()
const moduleStore = useModuleStore()

const table = ref(null)
const taxPerItemSetting = ref(companyStore.selectedCompanySettings.tax_per_item)

const taxTypeColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.tax_types.tax_name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'compound_tax',
      label: t('settings.tax_types.compound_tax'),
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'percent',
      label: t('settings.tax_types.percent'),
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

const salesTaxEnabled = computed(() => {
  return (
    companyStore.selectedCompanySettings.sales_tax_us_enabled === 'YES' &&
    moduleStore.salesTaxUSEnabled
  )
})

const taxPerItemField = computed({
  get: () => {
    return taxPerItemSetting.value === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        tax_per_item: value,
      },
    }

    taxPerItemSetting.value = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

function hasAtleastOneAbility() {
  return userStore.hasAbilities([
    abilities.DELETE_TAX_TYPE,
    abilities.EDIT_TAX_TYPE,
  ])
}

async function fetchData({ page, filter, sort }) {
  let data = {
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  let response = await taxTypeStore.fetchTaxTypes(data)

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

async function refreshTable() {
  table.value && table.value.refresh()
}

function openTaxModal() {
  modalStore.openModal({
    title: t('settings.tax_types.add_tax'),
    componentName: 'TaxTypeModal',
    size: 'sm',
    refreshData: table.value && table.value.refresh,
  })
}
</script>

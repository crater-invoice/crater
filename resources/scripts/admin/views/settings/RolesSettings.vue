<template>
  <RolesModal />

  <BaseSettingCard
    :title="$t('settings.roles.title')"
    :description="$t('settings.roles.description')"
  >
    <template v-if="userStore.currentUser.is_owner" #action>
      <BaseButton variant="primary-outline" @click="openRoleModal">
        <template #left="slotProps">
          <BaseIcon name="PlusIcon" :class="slotProps.class" />
        </template>
        {{ $t('settings.roles.add_new_role') }}
      </BaseButton>
    </template>

    <BaseTable
      ref="table"
      :data="fetchData"
      :columns="roleColumns"
      class="mt-14"
    >
      <!-- Added on  -->
      <template #cell-created_at="{ row }">
        {{ row.data.formatted_created_at }}
      </template>

      <template #cell-actions="{ row }">
        <RoleDropdown
          v-if="
            userStore.currentUser.is_owner && row.data.name !== 'super admin'
          "
          :row="row.data"
          :table="table"
          :load-data="refreshTable"
        />
      </template>
    </BaseTable>
  </BaseSettingCard>
</template>

<script setup>
import RoleDropdown from '@/scripts/admin/components/dropdowns/RoleIndexDropdown.vue'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoleStore } from '@/scripts/admin/stores/role'
import { useModalStore } from '@/scripts/stores/modal'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import RolesModal from '@/scripts/admin/components/modal-components/RolesModal.vue'
import abilities from '@/scripts/admin/stub/abilities'

const modalStore = useModalStore()
const roleStore = useRoleStore()
const userStore = useUserStore()
const companyStore = useCompanyStore()

const { t } = useI18n()
const table = ref(null)

const roleColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.roles.role_name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'created_at',
      label: t('settings.roles.added_on'),
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
    company_id: companyStore.selectedCompany.id,
  }

  let response = await roleStore.fetchRoles(data)

  return {
    data: response.data.data,
  }
}

async function refreshTable() {
  table.value && table.value.refresh()
}

async function openRoleModal() {
  await roleStore.fetchAbilities()

  modalStore.openModal({
    title: t('settings.roles.add_role'),
    componentName: 'RolesModal',
    size: 'lg',
    refreshData: table.value && table.value.refresh,
  })
}
</script>

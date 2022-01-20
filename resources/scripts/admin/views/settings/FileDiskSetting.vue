<template>
  <FileDiskModal />

  <BaseSettingCard
    :title="$tc('settings.disk.title', 1)"
    :description="$t('settings.disk.description')"
  >
    <template #action>
      <BaseButton variant="primary-outline" @click="openCreateDiskModal">
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />
        </template>
        {{ $t('settings.disk.new_disk') }}
      </BaseButton>
    </template>

    <BaseTable
      ref="table"
      class="mt-16"
      :data="fetchData"
      :columns="fileDiskColumns"
    >
      <template #cell-set_as_default="{ row }">
        <BaseBadge
          :bg-color="
            utils.getBadgeStatusColor(row.data.set_as_default ? 'YES' : 'NO')
              .bgColor
          "
          :color="
            utils.getBadgeStatusColor(row.data.set_as_default ? 'YES' : 'NO')
              .color
          "
        >
          {{ row.data.set_as_default ? 'Yes' : 'No'.replace('_', ' ') }}
        </BaseBadge>
      </template>

      <template #cell-actions="{ row }">
        <BaseDropdown v-if="isNotSystemDisk(row.data)">
          <template #activator>
            <div class="inline-block">
              <BaseIcon name="DotsHorizontalIcon" class="text-gray-500" />
            </div>
          </template>

          <BaseDropdownItem
            v-if="!row.data.set_as_default"
            @click="setDefaultDiskData(row.data.id)"
          >
            <BaseIcon class="mr-3 tetx-gray-600" name="CheckCircleIcon" />

            {{ $t('settings.disk.set_default_disk') }}
          </BaseDropdownItem>

          <BaseDropdownItem
            v-if="row.data.type !== 'SYSTEM'"
            @click="openEditDiskModal(row.data)"
          >
            <BaseIcon name="PencilIcon" class="mr-3 text-gray-600" />

            {{ $t('general.edit') }}
          </BaseDropdownItem>

          <BaseDropdownItem
            v-if="row.data.type !== 'SYSTEM' && !row.data.set_as_default"
            @click="removeDisk(row.data.id)"
          >
            <BaseIcon name="TrashIcon" class="mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </template>
    </BaseTable>

    <BaseDivider class="mt-8 mb-2" />

    <BaseSwitchSection
      v-model="savePdfToDiskField"
      :title="$t('settings.disk.save_pdf_to_disk')"
      :description="$t('settings.disk.disk_setting_description')"
    />
  </BaseSettingCard>
</template>

<script setup>
import { useDiskStore } from '@/scripts/admin/stores/disk'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useModalStore } from '@/scripts/stores/modal'
import { ref, computed, reactive, onMounted, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import FileDiskModal from '@/scripts/admin/components/modal-components/FileDiskModal.vue'

const utils = inject('utils')

const modelStore = useModalStore()
const diskStore = useDiskStore()
const companyStore = useCompanyStore()
const dialogStore = useDialogStore()
const { t } = useI18n()

let disk = 'local'
let loading = ref(false)
let table = ref('')

const fileDiskColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t('settings.disk.disk_name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'driver',
      label: t('settings.disk.filesystem_driver'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'type',
      label: t('settings.disk.disk_type'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },

    {
      key: 'set_as_default',
      label: t('settings.disk.is_default'),
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

const savePdfToDisk = ref(companyStore.selectedCompanySettings.save_pdf_to_disk)

const savePdfToDiskField = computed({
  get: () => {
    return savePdfToDisk.value === 'YES'
  },
  set: async (newValue) => {
    const value = newValue ? 'YES' : 'NO'

    let data = {
      settings: {
        save_pdf_to_disk: value,
      },
    }

    savePdfToDisk.value = value

    await companyStore.updateCompanySettings({
      data,
      message: 'general.setting_updated',
    })
  },
})

async function fetchData({ page, filter, sort }) {
  let data = reactive({
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  })

  let response = await diskStore.fetchDisks(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
    },
  }
}

function isNotSystemDisk(disk) {
  if (!disk.set_as_default) return true
  if (disk.type == 'SYSTEM' && disk.set_as_default) return false
  return true
}

function openCreateDiskModal() {
  modelStore.openModal({
    title: t('settings.disk.new_disk'),
    componentName: 'FileDiskModal',
    variant: 'lg',
    refreshData: table.value && table.value.refresh,
  })
}

function openEditDiskModal(data) {
  modelStore.openModal({
    title: t('settings.disk.edit_file_disk'),
    componentName: 'FileDiskModal',
    variant: 'lg',
    id: data.id,
    data: data,
    refreshData: table.value && table.value.refresh,
  })
}

function setDefaultDiskData(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.disk.set_default_disk_confirm'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'primary',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        loading.value = true
        let data = reactive({
          set_as_default: true,
          id,
        })
        await diskStore.updateDisk(data).then(() => {
          table.value && table.value.refresh()
        })
      }
    })
}

function removeDisk(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.disk.confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        let response = await diskStore.deleteFileDisk(id)
        if (response.data.success) {
          table.value && table.value.refresh()
          return true
        }
      }
    })
}
</script>

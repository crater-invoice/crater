<template>
  <BackupModal />

  <BaseSettingCard
    :title="$tc('settings.backup.title', 1)"
    :description="$t('settings.backup.description')"
  >
    <template #action>
      <BaseButton variant="primary-outline" @click="onCreateNewBackup">
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />
        </template>
        {{ $t('settings.backup.new_backup') }}
      </BaseButton>
    </template>

    <div class="grid my-14 md:grid-cols-3">
      <BaseInputGroup
        :label="$t('settings.disk.select_disk')"
        :content-loading="isFetchingInitialData"
      >
        <BaseMultiselect
          v-model="filters.selected_disk"
          :content-loading="isFetchingInitialData"
          :options="getDisksOptions"
          track-by="name"
          :placeholder="$t('settings.disk.select_disk')"
          label="name"
          :searchable="true"
          object
          class="w-full"
          value-prop="id"
          @select="refreshTable"
        >
        </BaseMultiselect>
      </BaseInputGroup>
    </div>

    <BaseTable
      ref="table"
      class="mt-10"
      :show-filter="false"
      :data="fetchBackupsData"
      :columns="backupColumns"
    >
      <template #cell-actions="{ row }">
        <BaseDropdown>
          <template #activator>
            <div class="inline-block">
              <BaseIcon name="DotsHorizontalIcon" class="text-gray-500" />
            </div>
          </template>

          <BaseDropdownItem @click="onDownloadBckup(row.data)">
            <BaseIcon name="CloudDownloadIcon" class="mr-3 text-gray-600" />

            {{ $t('general.download') }}
          </BaseDropdownItem>

          <BaseDropdownItem @click="onRemoveBackup(row.data)">
            <BaseIcon name="TrashIcon" class="mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </template>
    </BaseTable>
  </BaseSettingCard>
</template>

<script setup>
import { useBackupStore } from '@/scripts/admin/stores/backup'
import { computed, ref, reactive, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useDiskStore } from '@/scripts/admin/stores/disk'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useModalStore } from '@/scripts/stores/modal'
import BackupModal from '@/scripts/admin/components/modal-components/BackupModal.vue'

const dialogStore = useDialogStore()
const backupStore = useBackupStore()
const modalStore = useModalStore()
const diskStore = useDiskStore()
const { t } = useI18n()

const filters = reactive({
  selected_disk: { driver: 'local' },
})

const table = ref('')
let isFetchingInitialData = ref(true)

const backupColumns = computed(() => {
  return [
    {
      key: 'path',
      label: t('settings.backup.path'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'created_at',
      label: t('settings.backup.created_at'),
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'size',
      label: t('settings.backup.size'),
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

const getDisksOptions = computed(() => {
  return diskStore.disks.map((disk) => {
    return {
      ...disk,
      name: disk.name + ' — ' + '[' + disk.driver + ']',
    }
  })
})

loadDisksData()

function onRemoveBackup(backup) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('settings.backup.backup_confirm_delete'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        let data = {
          disk: filters.selected_disk.driver,
          file_disk_id: filters.selected_disk.id,
          path: backup.path,
        }

        let response = await backupStore.removeBackup(data)

        if (response.data.success || response.data.backup) {
          table.value && table.value.refresh()
          return true
        }
      }
    })
}

function refreshTable() {
  setTimeout(() => {
    table.value.refresh()
  }, 100)
}

async function loadDisksData() {
  isFetchingInitialData.value = true
  let res = await diskStore.fetchDisks({ limit: 'all' })
  if (res.data.error) {
  }
  filters.selected_disk = res.data.data.find((disk) => disk.set_as_default == 0)
  isFetchingInitialData.value = false
}

async function fetchBackupsData({ page, filter, sort }) {
  let data = {
    disk: filters.selected_disk.driver,
    filed_disk_id: filters.selected_disk.id,
  }

  isFetchingInitialData.value = true

  let response = await backupStore.fetchBackups(data)

  isFetchingInitialData.value = false

  return {
    data: response.data.backups,
    pagination: {
      totalPages: 1,
      currentPage: 1,
    },
  }
}

async function onCreateNewBackup() {
  modalStore.openModal({
    title: t('settings.backup.create_backup'),
    componentName: 'BackupModal',
    refreshData: table.value && table.value.refresh,
    size: 'sm',
  })
}

async function onDownloadBckup(backup) {
  isFetchingInitialData.value = true
  window
    .axios({
      method: 'GET',
      url: '/api/v1/download-backup',
      responseType: 'blob',
      params: {
        disk: filters.selected_disk.driver,
        file_disk_id: filters.selected_disk.id,
        path: backup.path,
      },
    })
    .then((response) => {
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', backup.path.split('/')[1])
      document.body.appendChild(link)
      link.click()
      isFetchingInitialData.value = false
    })
    .catch((e) => {
      isFetchingInitialData.value = false
    })
}
</script>

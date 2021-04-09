<template>
  <div class="relative setting-main-container backup">
    <sw-card variant="setting-card">
      <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
        <div>
          <h6 class="sw-section-title">
            {{ $tc('settings.backup.title', 1) }}
          </h6>
          <p
            class="mt-2 text-sm leading-snug text-gray-500"
            style="max-width: 680px"
          >
            {{ $t('settings.backup.description') }}
          </p>
        </div>
        <div class="mt-4 lg:mt-0 lg:ml-2">
          <sw-button
            variant="primary-outline"
            size="lg"
            @click="onCreateNewBackup"
          >
            <plus-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('settings.backup.new_backup') }}
          </sw-button>
        </div>
      </div>
      <div class="grid mb-8 md:grid-cols-3">
        <sw-input-group :label="$t('settings.disk.select_disk')">
          <sw-select
            v-model="filters.selected_disk"
            :options="getDisks"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('settings.disk.select_disk')"
            :allow-empty="false"
            :custom-label="getCustomLabel"
            track-by="id"
            label="name"
            @select="refreshTable"
          />
        </sw-input-group>
      </div>
      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchBackupsData"
        variant="gray"
      >
        <sw-table-column :label="$t('settings.backup.path')" show="path">
          <template slot-scope="row">
            <span>{{ $t('settings.backup.path') }}</span>
            <span class="mt-6">{{ row.path }}</span>
          </template>
        </sw-table-column>
        <sw-table-column
          :label="$t('settings.backup.created_at')"
          show="created_at"
        />
        <sw-table-column :label="$t('settings.backup.size')" show="size" />
        <sw-table-column
          :sortable="false"
          :filterable="false"
          :data="fetchBackupsData"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.backup.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item @click="onDownloadBckup(row)">
                <cloud-download-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.download') }}
              </sw-dropdown-item>
              <sw-dropdown-item @click="onRemoveBackup(row)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </sw-card>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { TrashIcon, CloudDownloadIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    PlusIcon,
    TrashIcon,
    CloudDownloadIcon,
  },
  data() {
    return {
      isRequestOngoing: true,

      filters: {
        selected_disk: { driver: 'local' },
      },
    }
  },

  computed: {
    ...mapGetters('disks', ['getDisks']),
  },

  created() {
    this.loadDisksData()
  },

  methods: {
    ...mapActions('backup', ['fetchBackups', 'downloadBackup', 'removeBackup']),

    ...mapActions('disks', ['fetchDisks']),

    ...mapActions('modal', ['openModal']),

    ...mapActions('notification', ['showNotification']),

    getCustomLabel({ driver, name }) {
      if (!name) {
        return
      }
      return `${name} — [${driver}]`
    },

    async onRemoveBackup(backup) {
      this.$swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.backup.backup_confirm_delete'),
        icon: 'question',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let data = {
            disk: this.filters.selected_disk.driver,
            file_disk_id: this.filters.selected_disk.id,
            path: backup.path,
          }
          let response = await this.removeBackup(data)
          if (response.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$t('settings.backup.deleted_message'),
            })
            this.$refs.table.refresh()
            return true
          }
        }
      })
    },

    async loadDisksData() {
      this.isRequestOngoing = true

      let res = await this.fetchDisks({ limit: 'all' })
      this.filters.selected_disk = res.data.disks.data.find(
        (disk) => disk.set_as_default == 1
      )
      this.isRequestOngoing = false
    },

    async fetchBackupsData({ page, filter, sort }) {
      let data = {
        disk: this.filters.selected_disk.driver,
        file_disk_id: this.filters.selected_disk.id,
      }

      this.isRequestOngoing = true
      let response = await this.fetchBackups(data)

      if (response.data.error) {
        this.showNotification({
          type: 'error',
          message: this.$t('settings.backup.' + response.data.error),
        })
      }

      this.isRequestOngoing = false

      return {
        data: response.data.backups,
        pagination: {
          totalPages: 1,
          currentPage: 1,
        },
      }
      this.$refs.table.refresh()
    },

    refreshTable() {
      setTimeout(() => {
        this.$refs.table.refresh()
      }, 100)
    },

    async onCreateNewBackup() {
      this.openModal({
        title: this.$t('settings.backup.create_backup'),
        componentName: 'BackupModal',
        refreshData: this.refreshTable,
      })
    },

    async onDownloadBckup(backup) {
      this.isRequestOngoing = true
      window
        .axios({
          method: 'GET',
          url: '/api/v1/download-backup',
          responseType: 'blob', // important
          params: {
            disk: this.filters.selected_disk.driver,
            file_disk_id: this.filters.selected_disk.id,
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
          this.isRequestOngoing = false
        })
        .catch((e) => {
          this.isRequestOngoing = false
          this.showNotification({
            type: 'error',
            message: e.response.data.message,
          })
        })
    },
  },
}
</script>
